<?php

namespace Database\Factories;

use App\Models\Pet;
use App\Models\PetImage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PetImage>
 */
class PetImageFactory extends Factory
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
            'image_path' => 'pets/'.fake()->uuid().'.jpg',
            'is_main' => false,
        ];
    }
}
