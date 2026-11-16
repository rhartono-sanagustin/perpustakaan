<?php

namespace Database\Factories;

use App\Enums\VisitSource;
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
            'source' => VisitSource::Admin,
            'catatan' => null,
        ];
    }

    /**
     * Kunjungan yang dicatat sendiri oleh pengunjung melalui kiosk.
     */
    public function mandiri(): static
    {
        return $this->state(fn (): array => [
            'recorded_by' => null,
            'source' => VisitSource::Mandiri,
        ]);
    }
}
