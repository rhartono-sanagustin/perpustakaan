<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loan_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->foreignId('book_id')->constrained()->restrictOnDelete();
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status', 30)->default('menunggu');
            $table->timestamp('requested_at');
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['status', 'requested_at']);
            $table->index(['user_id', 'status']);
            $table->index(['book_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_requests');
    }
};
