<?php

use App\Models\Size;
use App\Models\Species;
use Database\Seeders\DogSizeSeeder;

test('seeds the dog sizes and can be re-run without duplicating them', function () {
    $dog = Species::factory()->create(['name' => 'Cão']);
    $cat = Species::factory()->create(['name' => 'Gato']);

    $this->seed(DogSizeSeeder::class);
    $this->seed(DogSizeSeeder::class);

    expect(Size::query()->where('species_id', $dog->id)->orderBy('id')->pluck('name')->all())->toBe(DogSizeSeeder::SIZES)
        ->and(Size::query()->where('species_id', $cat->id)->exists())->toBeFalse();
});
