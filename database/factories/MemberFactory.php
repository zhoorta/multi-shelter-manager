<?php

namespace Database\Factories;

use App\Models\Member;
use App\Models\Shelter;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Member>
 */
class MemberFactory extends Factory
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
            'tin' => fake()->numerify('#########'),
            'email' => fake()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->streetAddress(),
            'postal_code' => fake()->postcode(),
            'city' => fake()->city(),
            'join_date' => fake()->dateTimeBetween('-3 years', 'now'),
            'status' => 'active',
            'joining_fee' => 0,
            'membership_fee' => 12,
            'membership_fee_frequency' => 'yearly',
        ];
    }
}
