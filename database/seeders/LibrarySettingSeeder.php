<?php

namespace Database\Seeders;

use App\Models\LibrarySetting;
use Illuminate\Database\Seeder;

class LibrarySettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            [
                'key' => 'max_loans_siswa',
                'value' => '2',
                'type' => 'integer',
                'group_name' => 'kuota',
                'label' => 'Maksimal pinjaman siswa',
                'description' => 'Jumlah maksimal eksemplar yang dapat dipinjam siswa pada waktu bersamaan.',
            ],
            [
                'key' => 'max_loans_guru',
                'value' => '5',
                'type' => 'integer',
                'group_name' => 'kuota',
                'label' => 'Maksimal pinjaman guru',
                'description' => 'Jumlah maksimal eksemplar yang dapat dipinjam guru pada waktu bersamaan.',
            ],
            [
                'key' => 'loan_duration_siswa',
                'value' => '7',
                'type' => 'integer',
                'group_name' => 'durasi',
                'label' => 'Durasi pinjaman siswa',
                'description' => 'Lama peminjaman siswa dalam hari kalender.',
            ],
            [
                'key' => 'loan_duration_guru',
                'value' => '14',
                'type' => 'integer',
                'group_name' => 'durasi',
                'label' => 'Durasi pinjaman guru',
                'description' => 'Lama peminjaman guru dalam hari kalender.',
            ],
            [
                'key' => 'pickup_deadline_days',
                'value' => '1',
                'type' => 'integer',
                'group_name' => 'permintaan',
                'label' => 'Batas pengambilan permintaan',
                'description' => 'Jumlah hari operasional sebelum permintaan yang disetujui dianggap kedaluwarsa.',
            ],
            [
                'key' => 'fine_enabled',
                'value' => '0',
                'type' => 'boolean',
                'group_name' => 'denda',
                'label' => 'Aktifkan denda keterlambatan',
                'description' => 'Denda tidak diterapkan sampai kebijakan sekolah ditetapkan.',
            ],
            [
                'key' => 'fine_per_day',
                'value' => '0',
                'type' => 'integer',
                'group_name' => 'denda',
                'label' => 'Denda per hari',
                'description' => 'Nominal denda keterlambatan per hari dalam rupiah.',
            ],
        ];

        foreach ($settings as $setting) {
            LibrarySetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
