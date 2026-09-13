<?php

namespace Database\Factories;

use App\Models\Shelter;
use App\Models\Volunteer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Volunteer>
 */
class VolunteerFactory extends Factory
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
            'name' => fake()->name(),
            'gender' => fake()->randomElement(['male', 'female']),
            'id_card' => fake()->numerify('########'),
            'tin' => fake()->numerify('#########'),
            'birth_date' => fake()->date(),
            'professional_activity' => fake()->jobTitle(),
            'email' => fake()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->streetAddress(),
            'postal_code' => fake()->postcode(),
            'city' => fake()->city(),
            'transport_mode' => fake()->randomElement(['foot', 'bycicle', 'hitchhike', 'public transportation', 'own vehicule']),
            'attendance_evaluation' => fake()->randomElement(['very low', 'low', 'regular', 'high', 'very high', 'excellent']),
            'performance_evaluation' => fake()->randomElement(['very low', 'low', 'regular', 'high', 'very high', 'excellent']),
            'start_date' => fake()->date(),
            'send_newsletter' => fake()->boolean(),
            'notes' => fake()->sentence(),
        ];
    }
}
