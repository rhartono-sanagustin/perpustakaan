<?php

namespace App\Http\Controllers;

use App\Enums\VisitRecordingStatus;
use App\Enums\VisitSource;
use App\Http\Requests\RecordPublicVisitRequest;
use App\Services\VisitRecorder;
use App\Support\VisitRecordingResult;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class VisitKioskController extends Controller
{
    public function __construct(private readonly VisitRecorder $visitRecorder) {}

    public function index(Request $request): Response
    {
        return response()->view('visits.kiosk', [
            'visitResult' => $request->session()->get('visit_result'),
        ]);
    }

    public function store(RecordPublicVisitRequest $request): RedirectResponse
    {
        $nomorInduk = trim((string) $request->validated('nomor_induk'));

        $result = $this->visitRecorder->record(
            $nomorInduk,
            null,
            VisitSource::Mandiri,
        );

        return redirect()
            ->route('visits.kiosk')
            ->with('visit_result', $this->resultPayload($result));
    }

    /**
     * @return array<string, string|null>
     */
    private function resultPayload(VisitRecordingResult $result): array
    {
        $user = $result->user;
        $time = $result->visit?->visited_at?->format('H:i');

        return [
            'status' => $result->status->value,
            'variant' => match ($result->status) {
                VisitRecordingStatus::Recorded => 'success',
                VisitRecordingStatus::Duplicate => 'info',
                default => 'danger',
            },
            'icon' => match ($result->status) {
                VisitRecordingStatus::Recorded => 'check-circle',
                VisitRecordingStatus::Duplicate => 'clock',
                VisitRecordingStatus::Inactive => 'no-symbol',
                VisitRecordingStatus::NotBorrower => 'identification',
                default => 'question-mark-circle',
            },
            'title' => match ($result->status) {
                VisitRecordingStatus::Recorded => 'Kunjungan Tercatat',
                VisitRecordingStatus::Duplicate => 'Sudah Tercatat',
                VisitRecordingStatus::Inactive => 'Akun Nonaktif',
                VisitRecordingStatus::NotBorrower => 'Bukan Kartu Anggota',
                default => 'Kartu Tidak Dikenali',
            },
            'message' => match ($result->status) {
                VisitRecordingStatus::Recorded => 'Kunjungan berhasil dicatat pukul '.$time.'. Selamat membaca!',
                VisitRecordingStatus::Duplicate => 'Kunjungan sudah tercatat pukul '.$time.'. Tidak perlu scan ulang.',
                VisitRecordingStatus::Inactive => 'Akun '.($user?->name ?? 'ini').' sedang nonaktif. Silakan hubungi petugas perpustakaan.',
                VisitRecordingStatus::NotBorrower => 'Kartu ini bukan kartu anggota perpustakaan.',
                default => 'Nomor induk tidak ditemukan. Periksa kembali kartu atau hubungi petugas.',
            },
            'name' => $user?->name,
            'nomor_induk' => $user?->nomor_induk,
            'role' => $user?->roleLabel(),
            'unit' => $user === null ? null : ($user->isGuru() ? $user->unit_kerja : $user->kelas),
            'time' => $time,
        ];
    }
}
