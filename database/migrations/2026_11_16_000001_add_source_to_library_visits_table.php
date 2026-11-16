<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tandai sumber kunjungan: dicatat admin atau mandiri oleh pengunjung.
     */
    public function up(): void
    {
        Schema::table('library_visits', function (Blueprint $table) {
            $table->string('source', 20)->default('admin')->after('recorded_by');
            $table->index('source');
        });
    }

    /**
     * Hapus kolom sumber kunjungan.
     */
    public function down(): void
    {
        Schema::table('library_visits', function (Blueprint $table) {
            $table->dropIndex(['source']);
            $table->dropColumn('source');
        });
    }
};
