<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RecordVisitRequest;
use App\Models\LibraryVisit;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VisitController extends Controller
{
    public function __construct(private readonly AuditLogger $auditLogger) {}

    public function index(Request $request): View
    {
        $member = User::query()
            ->with(['loans' => fn ($query) => $query->active()->with('copy.book')])
            ->find($request->integer('anggota'));

        $visits = LibraryVisit::query()
            ->today()
            ->with(['user', 'recorder'])
            ->latest('visited_at')
            ->get();

        return view('admin.visits.index', [
            'visits' => $visits,
            'member' => $member,
            'counts' => [
                'total' => $visits->count(),
                'unique' => $visits->unique('user_id')->count(),
                'guru' => $visits->filter(fn (LibraryVisit $visit): bool => $visit->user->isGuru())->count(),
                'siswa' => $visits->filter(fn (LibraryVisit $visit): bool => $visit->user->isSiswa())->count(),
            ],
        ]);
    }

    public function store(RecordVisitRequest $request): RedirectResponse
    {
        $nomorInduk = trim((string) $request->validated('nomor_induk'));
        $user = User::query()->where('nomor_induk', $nomorInduk)->first();

        if ($user === null) {
            return back()
                ->withInput()
                ->with('error', 'Anggota dengan nomor induk "'.$nomorInduk.'" tidak ditemukan.');
        }

        if (! $user->isBorrower()) {
            return back()
                ->withInput()
                ->with('error', 'Akun admin tidak memiliki kartu anggota perpustakaan.');
        }

        if (! $user->isActive()) {
            return back()
                ->withInput()
                ->with('error', 'Akun '.$user->name.' sedang nonaktif dan tidak dapat mencatat kunjungan.');
        }

        $recentVisit = LibraryVisit::query()
            ->where('user_id', $user->id)
            ->where('visited_at', '>=', now()->subMinutes(30))
            ->latest('visited_at')
            ->first();

        if ($recentVisit !== null) {
            return redirect()
                ->route('admin.visits.index', ['anggota' => $user->id])
                ->with('info', 'Kunjungan '.$user->name.' sudah tercatat pukul '.$recentVisit->visited_at->format('H:i').'.');
        }

        $visit = LibraryVisit::query()->create([
            'user_id' => $user->id,
            'visit_date' => now()->toDateString(),
            'visited_at' => now(),
            'recorded_by' => $request->user()->id,
        ]);

        $this->auditLogger->record('visit.recorded', $visit, null, [
            'user_id' => $visit->user_id,
            'visit_date' => $visit->visit_date->toDateString(),
            'visited_at' => $visit->visited_at->toDateTimeString(),
            'recorded_by' => $visit->recorded_by,
        ]);

        return redirect()
            ->route('admin.visits.index', ['anggota' => $user->id])
            ->with('success', 'Kunjungan '.$user->name.' berhasil dicatat pukul '.$visit->visited_at->format('H:i').'.');
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
        ], null);

        $visit->delete();

        return redirect()
            ->route('admin.visits.index')
            ->with('success', 'Kunjungan '.$visit->user->name.' berhasil dibatalkan.');
    }
}
