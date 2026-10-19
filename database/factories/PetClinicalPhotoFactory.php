<?php

namespace Database\Factories;

use App\Models\Pet;
use App\Models\PetClinicalPhoto;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PetClinicalPhoto>
 */
class PetClinicalPhotoFactory extends Factory
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
            'label' => fake()->words(3, true),
            'taken_at' => fake()->date(),
            'notes' => null,
            'image_path' => 'clinical-photos/'.fake()->uuid().'.jpg',
            'thumb_path' => null,
            'file_size' => 1024,
        ];
    }
}
