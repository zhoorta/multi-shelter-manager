<?php

namespace Database\Factories;

use App\Models\Colony;
use App\Models\Shelter;
use App\Models\Species;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Colony>
 */
class ColonyFactory extends Factory
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
            'species_id' => Species::factory()->state(['has_colonies' => true]),
            'name' => fake()->streetName(),
            'location' => fake()->streetAddress(),
            'city' => fake()->city(),
            'caretaker_name' => fake()->name(),
            'caretaker_phone' => fake()->phoneNumber(),
            'notes' => fake()->sentence(),
            'is_active' => true,
        ];
    }
}
