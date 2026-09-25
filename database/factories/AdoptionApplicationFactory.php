<?php

namespace Database\Factories;

use App\Models\AdoptionApplication;
use App\Models\Pet;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AdoptionApplication>
 */
class AdoptionApplicationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'pet_id' => Pet::factory(),
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'phone' => fake()->numerify('9########'),
            'postal_code' => fake()->postcode(),
            'city' => fake()->city(),
            'housing_type' => fake()->randomElement(['apartment', 'house']),
            'has_garden' => fake()->boolean(),
            'has_children' => fake()->boolean(),
            'other_animals' => null,
            'message' => fake()->paragraph(),
            'status' => 'pending',
            'consent_at' => now(),
            'ip_address' => fake()->ipv4(),
        ];
    }

    public function rejected(): static
    {
        return $this->state(fn (): array => ['status' => 'rejected', 'reviewed_at' => now()]);
    }
}
