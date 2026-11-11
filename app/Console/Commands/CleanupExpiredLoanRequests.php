<?php

namespace App\Console\Commands;

use App\Services\CirculationService;
use Illuminate\Console\Command;

class CleanupExpiredLoanRequests extends Command
{
    protected $signature = 'library:cleanup {--dry-run : Tampilkan jumlah pengajuan yang akan dibatalkan tanpa mengubah data}';

    protected $description = 'Batalkan pengajuan yang disetujui tetapi melewati batas pengambilan';

    public function handle(CirculationService $circulation): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $count = $circulation->expireStaleApprovedRequests($dryRun);

        if ($dryRun) {
            $this->info($count.' pengajuan kedaluwarsa akan dibatalkan (dry run, tidak ada data yang diubah).');

            return self::SUCCESS;
        }

        $this->info($count.' pengajuan kedaluwarsa berhasil dibatalkan.');

        return self::SUCCESS;
    }
}
