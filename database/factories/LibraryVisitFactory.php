<?php

namespace Database\Factories;

use App\Models\LibraryVisit;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LibraryVisit>
 */
class LibraryVisitFactory extends Factory
{
    public function definition(): array
    {
        $visitedAt = fake()->dateTimeBetween('-14 days', 'now');

        return [
            'user_id' => User::factory()->siswa(),
            'visit_date' => $visitedAt->format('Y-m-d'),
            'visited_at' => $visitedAt,
            'recorded_by' => User::factory()->admin(),
            'catatan' => null,
        ];
    }
}
