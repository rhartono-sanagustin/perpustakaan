<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambahkan atribut akun perpustakaan pada tabel users.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 20)->default('siswa')->after('name');
            $table->string('nomor_induk', 50)->nullable()->unique()->after('role');
            $table->string('status', 20)->default('aktif')->after('nomor_induk');
            $table->string('kelas', 50)->nullable()->after('status');
            $table->string('unit_kerja', 100)->nullable()->after('kelas');
            $table->string('no_telepon', 30)->nullable()->after('unit_kerja');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('email')->nullable()->change();
        });

        DB::table('users')
            ->whereNull('nomor_induk')
            ->orderBy('id')
            ->each(function (object $user): void {
                DB::table('users')->where('id', $user->id)->update([
                    'nomor_induk' => 'USER-'.$user->id,
                ]);
            });
    }

    /**
     * Hapus atribut akun perpustakaan dari tabel users.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['nomor_induk']);
            $table->dropColumn([
                'role',
                'nomor_induk',
                'status',
                'kelas',
                'unit_kerja',
                'no_telepon',
            ]);
        });
    }
};
