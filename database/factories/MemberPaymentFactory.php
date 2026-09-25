<?php

namespace Database\Factories;

use App\Models\Member;
use App\Models\MemberPayment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MemberPayment>
 */
class MemberPaymentFactory extends Factory
{
    /**
     * Define the model's default state: a yearly membership fee.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('-1 year', 'now');

        return [
            'member_id' => Member::factory(),
            'type' => 'membership_fee',
            'start_date' => $startDate,
            'end_date' => (clone $startDate)->modify('+1 year -1 day'),
            'payment_date' => $startDate,
            'payment_value' => 12,
            'payment_method' => fake()->randomElement(['cash', 'bank_transfer', 'mobile', 'other']),
        ];
    }

    /**
     * A one-time joining fee payment, which covers no period.
     */
    public function joiningFee(): static
    {
        return $this->state(fn (): array => [
            'type' => 'joining_fee',
            'start_date' => null,
            'end_date' => null,
        ]);
    }
}
