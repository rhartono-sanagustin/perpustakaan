<?php

namespace Database\Factories;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AuditLog>
 */
class AuditLogFactory extends Factory
{
    public function definition(): array
    {
        return [
            'actor_id' => User::factory()->admin(),
            'action' => fake()->randomElement(['created', 'updated', 'deleted', 'approved']),
            'entity_type' => fake()->randomElement(['book', 'book_copy', 'loan', 'user']),
            'entity_id' => fake()->numberBetween(1, 100),
            'old_values' => null,
            'new_values' => ['contoh' => fake()->word()],
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
        ];
    }
}
