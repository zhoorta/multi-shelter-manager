<?php

namespace Database\Factories;

use App\Models\Shelter;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'is_admin' => false,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Indicate that the model has two-factor authentication configured.
     */
    public function withTwoFactor(): static {}

    /**
     * A global admin, with no shelter membership.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_admin' => true,
            'current_shelter_id' => null,
        ]);
    }

    /**
     * Attach the created user to a shelter with the given membership role
     * and notification preferences, and set it as their active shelter.
     */
    public function forShelter(Shelter|int $shelter, string $role = 'staff', bool $vaccinationNotifications = false, bool $adoptionApplicationNotifications = false): static
    {
        $shelterId = $shelter instanceof Shelter ? $shelter->id : $shelter;

        return $this->state(['current_shelter_id' => $shelterId])
            ->afterCreating(function (User $user) use ($shelterId, $role, $vaccinationNotifications, $adoptionApplicationNotifications): void {
                $user->shelters()->attach($shelterId, [
                    'role' => $role,
                    'vaccination_notifications' => $vaccinationNotifications,
                    'adoption_application_notifications' => $adoptionApplicationNotifications,
                ]);
            });
    }
}
