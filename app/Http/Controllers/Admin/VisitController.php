<?php

namespace App\Http\Controllers\Admin;

use App\Enums\VisitRecordingStatus;
use App\Enums\VisitSource;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RecordVisitRequest;
use App\Models\LibraryVisit;
use App\Models\User;
use App\Services\AuditLogger;
use App\Services\VisitRecorder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VisitController extends Controller
{
    public function __construct(
        private readonly AuditLogger $auditLogger,
        private readonly VisitRecorder $visitRecorder,
    ) {}

    public function index(Request $request): View
    {
        $member = User::query()
            ->with(['loans' => fn ($query) => $query->active()->with('copy.book')])
            ->find($request->integer('anggota'));

        $sourceFilter = VisitSource::tryFrom((string) $request->query('sumber', ''));

        $todayVisits = LibraryVisit::query()
            ->today()
            ->with(['user', 'recorder'])
            ->latest('visited_at')
            ->get();

        $visits = $sourceFilter === null
            ? $todayVisits
            : $todayVisits->where('source', $sourceFilter)->values();

        return view('admin.visits.index', [
            'visits' => $visits,
            'member' => $member,
            'sourceFilter' => $sourceFilter?->value,
            'counts' => [
                'total' => $todayVisits->count(),
                'unique' => $todayVisits->unique('user_id')->count(),
                'guru' => $todayVisits->filter(fn (LibraryVisit $visit): bool => $visit->user->isGuru())->count(),
                'siswa' => $todayVisits->filter(fn (LibraryVisit $visit): bool => $visit->user->isSiswa())->count(),
            ],
        ]);
    }

    public function store(RecordVisitRequest $request): RedirectResponse
    {
        $nomorInduk = trim((string) $request->validated('nomor_induk'));

        $result = $this->visitRecorder->record(
            $nomorInduk,
            $request->user(),
            VisitSource::Admin,
        );

        return match ($result->status) {
            VisitRecordingStatus::NotFound => back()
                ->withInput()
                ->with('error', 'Anggota dengan nomor induk "'.$nomorInduk.'" tidak ditemukan.'),
            VisitRecordingStatus::NotBorrower => back()
                ->withInput()
                ->with('error', 'Akun admin tidak memiliki kartu anggota perpustakaan.'),
            VisitRecordingStatus::Inactive => back()
                ->withInput()
                ->with('error', 'Akun '.$result->user->name.' sedang nonaktif dan tidak dapat mencatat kunjungan.'),
            VisitRecordingStatus::Duplicate => redirect()
                ->route('admin.visits.index', ['anggota' => $result->user->id])
                ->with('info', 'Kunjungan '.$result->user->name.' sudah tercatat pukul '.$result->visit->visited_at->format('H:i').'.'),
            VisitRecordingStatus::Recorded => redirect()
                ->route('admin.visits.index', ['anggota' => $result->user->id])
                ->with('success', 'Kunjungan '.$result->user->name.' berhasil dicatat pukul '.$result->visit->visited_at->format('H:i').'.'),
        };
    }

    public function destroy(LibraryVisit $visit): RedirectResponse
    {
        if (! $visit->visit_date->isToday()) {
            return redirect()
                ->route('admin.visits.index')
                ->with('error', 'Hanya kunjungan hari ini yang dapat dibatalkan.');
        }

        $visit->loadMissing('user');

        $this->auditLogger->record('visit.deleted', $visit, [
            'user_id' => $visit->user_id,
            'visit_date' => $visit->visit_date->toDateString(),
            'visited_at' => $visit->visited_at->toDateTimeString(),
            'recorded_by' => $visit->recorded_by,
            'source' => $visit->source->value,
        ], null);

        $visit->delete();

        return redirect()
            ->route('admin.visits.index')
            ->with('success', 'Kunjungan '.$visit->user->name.' berhasil dibatalkan.');
    }
}
