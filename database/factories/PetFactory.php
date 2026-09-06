<?php

namespace Database\Factories;

use App\Models\Pet;
use App\Models\Shelter;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

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
        $speciesId = DB::table('species')->insertGetId([
            'name' => fake()->unique()->word(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $breedId = DB::table('breeds')->insertGetId([
            'species_id' => $speciesId,
            'name' => fake()->word(),
            'is_default' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return [
            'shelter_id' => Shelter::factory(),
            'species_id' => $speciesId,
            'breed_id' => $breedId,
            'name' => fake()->firstName(),
            'gender' => fake()->randomElement(['male', 'female', 'unknown']),
            'status' => 'available',
        ];
    }
}
