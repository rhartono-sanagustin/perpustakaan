<?php

namespace App\Services;

use App\Enums\VisitRecordingStatus;
use App\Enums\VisitSource;
use App\Models\LibraryVisit;
use App\Models\User;
use App\Support\VisitRecordingResult;
use Illuminate\Support\Facades\DB;

class VisitRecorder
{
    /**
     * Jendela waktu untuk menganggap scan berulang sebagai kunjungan yang sama.
     */
    public const DUPLICATE_WINDOW_MINUTES = 30;

    public function __construct(private readonly AuditLogger $auditLogger) {}

    /**
     * Catat kunjungan anggota berdasarkan nomor induk.
     *
     * Pengecekan duplikat dan pembuatan baris dijalankan dalam transaksi dengan
     * lock per anggota agar dua scan bersamaan tidak menghasilkan dua kunjungan.
     */
    public function record(string $nomorInduk, ?User $recordedBy, VisitSource $source): VisitRecordingResult
    {
        $nomorInduk = trim($nomorInduk);

        $user = User::query()->where('nomor_induk', $nomorInduk)->first();

        if ($user === null) {
            return new VisitRecordingResult(VisitRecordingStatus::NotFound);
        }

        if (! $user->isBorrower()) {
            return new VisitRecordingResult(VisitRecordingStatus::NotBorrower, $user);
        }

        if (! $user->isActive()) {
            return new VisitRecordingResult(VisitRecordingStatus::Inactive, $user);
        }

        return DB::transaction(function () use ($user, $recordedBy, $source): VisitRecordingResult {
            $lockedUser = User::query()->whereKey($user->id)->lockForUpdate()->firstOrFail();
            $visitedAt = now();

            $recentVisit = LibraryVisit::query()
                ->where('user_id', $lockedUser->id)
                ->where('visited_at', '>=', $visitedAt->copy()->subMinutes(self::DUPLICATE_WINDOW_MINUTES))
                ->latest('visited_at')
                ->first();

            if ($recentVisit !== null) {
                return new VisitRecordingResult(VisitRecordingStatus::Duplicate, $lockedUser, $recentVisit);
            }

            $visit = LibraryVisit::query()->create([
                'user_id' => $lockedUser->id,
                'visit_date' => $visitedAt->toDateString(),
                'visited_at' => $visitedAt,
                'recorded_by' => $recordedBy?->id,
                'source' => $source,
            ]);

            $this->auditLogger->record('visit.recorded', $visit, null, [
                'user_id' => $visit->user_id,
                'visit_date' => $visit->visit_date->toDateString(),
                'visited_at' => $visit->visited_at->toDateTimeString(),
                'recorded_by' => $visit->recorded_by,
                'source' => $visit->source->value,
            ]);

            return new VisitRecordingResult(VisitRecordingStatus::Recorded, $lockedUser, $visit);
        });
    }
}
