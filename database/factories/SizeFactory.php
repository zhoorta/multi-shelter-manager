<?php

namespace Database\Factories;

use App\Models\Size;
use App\Models\Species;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Size>
 */
class SizeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'species_id' => Species::factory(),
            'name' => fake()->unique()->word(),
        ];
    }
}
