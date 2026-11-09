<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed akun demo untuk pengujian awal.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['nomor_induk' => 'ADM001'],
            [
                'name' => 'Administrator Perpustakaan',
                'email' => 'admin@perpus.test',
                'password' => 'password',
                'role' => UserRole::Admin,
                'status' => UserStatus::Aktif,
                'kelas' => null,
                'unit_kerja' => 'Perpustakaan',
                'no_telepon' => '081234567890',
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['nomor_induk' => 'NIP001'],
            [
                'name' => 'Budi Santoso',
                'email' => 'guru@perpus.test',
                'password' => 'password',
                'role' => UserRole::Guru,
                'status' => UserStatus::Aktif,
                'kelas' => null,
                'unit_kerja' => 'Bahasa Indonesia',
                'no_telepon' => '081234567891',
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['nomor_induk' => 'NIS001'],
            [
                'name' => 'Siti Aminah',
                'email' => 'siswa@perpus.test',
                'password' => 'password',
                'role' => UserRole::Siswa,
                'status' => UserStatus::Aktif,
                'kelas' => 'X IPA 1',
                'unit_kerja' => null,
                'no_telepon' => '081234567892',
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['nomor_induk' => 'NIS002'],
            [
                'name' => 'Akun Nonaktif',
                'email' => 'nonaktif@perpus.test',
                'password' => 'password',
                'role' => UserRole::Siswa,
                'status' => UserStatus::Nonaktif,
                'kelas' => 'X IPA 1',
                'unit_kerja' => null,
                'no_telepon' => null,
                'email_verified_at' => now(),
            ]
        );

        $this->call([
            LibrarySettingSeeder::class,
            MasterDataSeeder::class,
            CirculationSeeder::class,
        ]);
    }
}
