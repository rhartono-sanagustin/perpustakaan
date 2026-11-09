<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('book_copies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained()->cascadeOnDelete();
            $table->string('inventory_code')->unique();
            $table->string('shelf_location')->nullable();
            $table->string('condition', 30)->default('baik');
            $table->string('status', 30)->default('tersedia');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['book_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('book_copies');
    }
};
