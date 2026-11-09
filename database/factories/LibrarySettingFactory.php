<?php

namespace Database\Factories;

use App\Models\LibrarySetting;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LibrarySetting>
 */
class LibrarySettingFactory extends Factory
{
    public function definition(): array
    {
        return [
            'key' => fake()->unique()->slug(3),
            'value' => (string) fake()->numberBetween(1, 30),
            'type' => 'integer',
            'group_name' => 'umum',
            'label' => fake()->words(3, true),
            'description' => fake()->sentence(),
            'updated_by' => null,
        ];
    }
}
