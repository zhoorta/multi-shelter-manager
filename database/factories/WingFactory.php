<?php

namespace Database\Factories;

use App\Models\Facility;
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
            'facility_id' => Facility::factory(),
            'name' => fake()->words(2, true),
            'description' => fake()->sentence(),
        ];
    }

    /**
     * A wing of foster families: each cage is one family.
     */
    public function foster(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_foster' => true,
        ]);
    }
}
