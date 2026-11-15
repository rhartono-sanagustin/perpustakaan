<?php

namespace Database\Factories;

use App\Models\CarouselSlide;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CarouselSlide>
 */
class CarouselSlideFactory extends Factory
{
    protected $model = CarouselSlide::class;

    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'caption' => fake()->sentence(),
            'alt_text' => fake()->sentence(3),
            'image_path' => 'carousel/'.fake()->uuid().'.jpg',
            'link_url' => null,
            'sort_order' => fake()->numberBetween(1, 10),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes): array => [
            'is_active' => false,
        ]);
    }
}
