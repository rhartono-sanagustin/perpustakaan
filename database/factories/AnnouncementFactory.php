<?php

namespace Database\Factories;

use App\Enums\AnnouncementAudience;
use App\Models\Announcement;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Announcement>
 */
class AnnouncementFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(5),
            'body' => fake()->paragraph(4),
            'audience' => AnnouncementAudience::Semua,
            'is_active' => true,
            'published_at' => now(),
            'created_by' => User::factory()->admin(),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
            'published_at' => null,
        ]);
    }
}
