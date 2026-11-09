<?php

namespace Database\Factories;

use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\Publisher;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Book>
 */
class BookFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->unique()->sentence(3);

        return [
            'category_id' => Category::factory(),
            'publisher_id' => Publisher::factory(),
            'title' => Str::title($title),
            'slug' => Str::slug($title).'-'.fake()->unique()->numerify('###'),
            'isbn' => fake()->unique()->isbn13(),
            'publication_year' => fake()->numberBetween(2000, 2026),
            'edition' => fake()->randomElement(['Cetakan 1', 'Cetakan 2', 'Edisi Revisi', null]),
            'language' => 'Indonesia',
            'summary' => fake()->paragraph(5),
            'cover_path' => null,
            'is_active' => true,
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Book $book) {
            if ($book->authors()->count() === 0) {
                $book->authors()->attach(Author::factory()->count(fake()->numberBetween(1, 2))->create());
            }
        });
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
