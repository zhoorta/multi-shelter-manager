<?php

namespace Database\Factories;

use App\Models\DataExport;
use App\Models\Shelter;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DataExport>
 */
class DataExportFactory extends Factory
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
            'user_id' => User::factory(),
            'ip_address' => fake()->ipv4(),
        ];
    }
}
