<?php

namespace Database\Factories;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'role' => UserRole::Siswa,
            'nomor_induk' => fake()->unique()->numerify('NIS#####'),
            'status' => UserStatus::Aktif,
            'kelas' => fake()->randomElement(['X IPA 1', 'X IPA 2', 'XI IPS 1', 'XII IPA 1']),
            'unit_kerja' => null,
            'no_telepon' => fake()->numerify('08##########'),
            'remember_token' => Str::random(10),
        ];
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => UserRole::Admin,
            'kelas' => null,
            'unit_kerja' => 'Perpustakaan',
            'nomor_induk' => fake()->unique()->numerify('ADM###'),
        ]);
    }

    public function guru(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => UserRole::Guru,
            'kelas' => null,
            'unit_kerja' => fake()->randomElement(['Bahasa Indonesia', 'Matematika', 'IPA', 'IPS']),
            'nomor_induk' => fake()->unique()->numerify('NIP#####'),
        ]);
    }

    public function siswa(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => UserRole::Siswa,
            'unit_kerja' => null,
            'nomor_induk' => fake()->unique()->numerify('NIS#####'),
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => UserStatus::Nonaktif,
        ]);
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
