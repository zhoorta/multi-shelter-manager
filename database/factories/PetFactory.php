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
            'ref' => 'PET-'.strtoupper(fake()->unique()->bothify('????????')),
            'name' => fake()->firstName(),
            'gender' => fake()->randomElement(['male', 'female']),
            'status' => 'available',
        ];
    }
}
