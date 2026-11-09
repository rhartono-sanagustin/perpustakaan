<?php

namespace Database\Factories;

use App\Enums\BookCondition;
use App\Enums\BookCopyStatus;
use App\Models\Book;
use App\Models\BookCopy;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BookCopy>
 */
class BookCopyFactory extends Factory
{
    public function definition(): array
    {
        return [
            'book_id' => Book::factory(),
            'inventory_code' => 'INV-'.fake()->unique()->numerify('#####'),
            'shelf_location' => fake()->randomElement(['Rak A-1', 'Rak A-2', 'Rak B-1', 'Rak B-2', 'Rak C-1']),
            'condition' => BookCondition::Baik,
            'status' => BookCopyStatus::Tersedia,
            'notes' => null,
        ];
    }

    public function borrowed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => BookCopyStatus::Dipinjam,
        ]);
    }

    public function damaged(): static
    {
        return $this->state(fn (array $attributes) => [
            'condition' => BookCondition::RusakRingan,
            'status' => BookCopyStatus::Rusak,
        ]);
    }

    public function lost(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => BookCopyStatus::Hilang,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => BookCopyStatus::Nonaktif,
        ]);
    }
}
