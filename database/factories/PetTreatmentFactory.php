<?php

namespace Database\Factories;

use App\Models\Pet;
use App\Models\PetTreatment;
use App\Models\Treatment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PetTreatment>
 */
class PetTreatmentFactory extends Factory
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
            'treatment_id' => Treatment::factory(),
            'administered_date' => today()->subMonth()->toDateString(),
            'due_date' => null,
            'status' => 'administered',
        ];
    }

    /**
     * A future reminder with no dose logged yet.
     */
    public function scheduled(string $dueDate): static
    {
        return $this->state(fn (): array => [
            'administered_date' => null,
            'due_date' => $dueDate,
            'status' => 'scheduled',
        ]);
    }
}
