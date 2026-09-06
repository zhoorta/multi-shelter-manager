<?php

namespace Database\Factories;

use App\Models\Cage;
use App\Models\Wing;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Cage>
 */
class CageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'wing_id' => Wing::factory(),
            'code' => fake()->unique()->bothify('C-##'),
            'capacity' => fake()->numberBetween(1, 4),
        ];
    }
}
