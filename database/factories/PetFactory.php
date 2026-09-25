<?php

namespace Database\Factories;

use App\Models\Breed;
use App\Models\Pet;
use App\Models\Shelter;
use App\Models\Species;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Pet>
 */
class PetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $species = Species::factory()->create();

        return [
            'shelter_id' => Shelter::factory(),
            'species_id' => $species->id,
            'breed_id' => Breed::factory()->for($species)->create()->id,
            'ref' => '',
            'name' => fake()->firstName(),
            'gender' => fake()->randomElement(['male', 'female']),
            'status' => 'available',
        ];
    }

    /**
     * A pet shown on the public portal (see Pet::publishedToPortal()).
     */
    public function publishedToPortal(): static
    {
        return $this->state(fn (): array => [
            'publish_to_portal' => true,
            'is_adoptable' => true,
            'status' => 'available',
        ]);
    }

    /**
     * @return Factory<Pet>
     */
    public function configure(): Factory
    {
        return $this->afterCreating(function (Pet $pet): void {
            $pet->update(['ref' => 'PET'.str_pad((string) $pet->id, 5, '0', STR_PAD_LEFT)]);
        });
    }
}
