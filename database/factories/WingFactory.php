<?php

namespace Database\Factories;

use App\Models\Shelter;
use App\Models\Wing;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Wing>
 */
class WingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'shelter_id' => Shelter::factory(),
            'name' => fake()->words(2, true),
            'description' => fake()->sentence(),
        ];
    }
}
