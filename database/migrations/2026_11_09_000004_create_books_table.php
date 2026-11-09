<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->foreignId('publisher_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('isbn', 32)->nullable()->unique();
            $table->unsignedSmallInteger('publication_year')->nullable();
            $table->string('edition')->nullable();
            $table->string('language', 50)->default('Indonesia');
            $table->text('summary')->nullable();
            $table->string('cover_path')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();

            $table->index('title');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
