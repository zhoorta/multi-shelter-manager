<?php

namespace Database\Factories;

use App\Models\Sponsorship;
use App\Models\SponsorshipPayment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SponsorshipPayment>
 */
class SponsorshipPaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('-1 year', 'now');

        return [
            'sponsorship_id' => Sponsorship::factory(),
            'start_date' => $startDate,
            'end_date' => (clone $startDate)->modify('+1 month'),
            'payment_date' => $startDate,
            'payment_value' => fake()->randomFloat(2, 5, 100),
        ];
    }
}
