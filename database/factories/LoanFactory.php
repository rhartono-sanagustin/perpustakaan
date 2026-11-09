<?php

namespace Database\Factories;

use App\Enums\BookCondition;
use App\Enums\LoanStatus;
use App\Models\BookCopy;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Loan>
 */
class LoanFactory extends Factory
{
    public function definition(): array
    {
        $borrowedAt = now()->subDays(2);

        return [
            'loan_request_id' => null,
            'user_id' => User::factory()->siswa(),
            'book_copy_id' => BookCopy::factory(),
            'processed_by' => User::factory()->admin(),
            'borrowed_at' => $borrowedAt,
            'due_at' => $borrowedAt->copy()->addDays(7),
            'returned_at' => null,
            'status' => LoanStatus::Dipinjam,
            'return_condition' => null,
            'return_notes' => null,
            'duration_days' => 7,
            'quota_snapshot' => 2,
        ];
    }

    public function returned(): static
    {
        return $this->state(fn (array $attributes) => [
            'returned_at' => now(),
            'status' => LoanStatus::Dikembalikan,
            'return_condition' => BookCondition::Baik,
        ]);
    }

    public function overdue(): static
    {
        return $this->state(fn (array $attributes) => [
            'borrowed_at' => now()->subDays(20),
            'due_at' => now()->subDays(10),
        ]);
    }
}
