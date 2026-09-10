<?php

namespace Database\Factories;

use App\Models\Facility;
use App\Models\Shelter;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Facility>
 */
class FacilityFactory extends Factory
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
            'address' => fake()->streetAddress(),
            'postal_code' => fake()->postcode(),
            'city' => fake()->city(),
            'notes' => fake()->sentence(),
        ];
    }
}
