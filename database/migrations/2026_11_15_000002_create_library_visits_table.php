<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Catat kunjungan anggota ke perpustakaan.
     */
    public function up(): void
    {
        Schema::create('library_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('visit_date');
            $table->dateTime('visited_at');
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('catatan', 255)->nullable();
            $table->timestamps();

            $table->index('visit_date');
            $table->index(['user_id', 'visited_at']);
        });
    }

    /**
     * Hapus tabel kunjungan.
     */
    public function down(): void
    {
        Schema::dropIfExists('library_visits');
    }
};
