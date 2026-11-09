<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_request_id')->nullable()->unique()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->foreignId('book_copy_id')->constrained()->restrictOnDelete();
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->date('borrowed_at');
            $table->date('due_at');
            $table->date('returned_at')->nullable();
            $table->string('status', 20)->default('dipinjam');
            $table->string('return_condition', 30)->nullable();
            $table->text('return_notes')->nullable();
            $table->unsignedSmallInteger('duration_days');
            $table->unsignedTinyInteger('quota_snapshot')->nullable();
            $table->timestamps();

            $table->index(['status', 'due_at']);
            $table->index(['user_id', 'status']);
            $table->index(['book_copy_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
