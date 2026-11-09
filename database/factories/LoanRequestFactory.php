<?php

namespace Database\Factories;

use App\Enums\LoanRequestStatus;
use App\Models\Book;
use App\Models\LoanRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LoanRequest>
 */
class LoanRequestFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->siswa(),
            'book_id' => Book::factory(),
            'processed_by' => null,
            'status' => LoanRequestStatus::Menunggu,
            'requested_at' => now(),
            'approved_at' => null,
            'rejected_at' => null,
            'cancelled_at' => null,
            'expires_at' => null,
            'rejection_reason' => null,
            'notes' => null,
        ];
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => LoanRequestStatus::Disetujui,
            'processed_by' => User::factory()->admin(),
            'approved_at' => now(),
            'expires_at' => now()->addDay(),
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => LoanRequestStatus::Ditolak,
            'processed_by' => User::factory()->admin(),
            'rejected_at' => now(),
            'rejection_reason' => 'Stok eksemplar sedang tidak tersedia.',
        ]);
    }
}
