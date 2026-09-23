<?php

use App\Models\Breed;
use App\Models\Species;
use Database\Seeders\CatBreedSeeder;

test('seeds every cat breed with Europeu Comum as the only default', function () {
    $cat = Species::factory()->create(['name' => 'Gato']);
    $previousDefault = Breed::factory()->for($cat)->create(['name' => 'Indefinida', 'is_default' => true]);

    $this->seed(CatBreedSeeder::class);

    expect(Breed::query()->where('species_id', $cat->id)->pluck('name')->all())
        ->toEqualCanonicalizing([...CatBreedSeeder::BREEDS, 'Indefinida'])
        ->and(Breed::query()->where('species_id', $cat->id)->where('is_default', true)->pluck('name')->all())
        ->toBe([CatBreedSeeder::DEFAULT_BREED])
        ->and($previousDefault->fresh()->is_default)->toBeFalse();
});
