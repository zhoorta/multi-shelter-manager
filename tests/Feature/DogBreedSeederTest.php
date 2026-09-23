<?php

use App\Models\Breed;
use App\Models\Species;
use Database\Seeders\DogBreedSeeder;

test('seeds every dog breed with Cão Rafeiro as the only default', function () {
    $dog = Species::factory()->create(['name' => 'Cão']);
    $previousDefault = Breed::factory()->for($dog)->create(['name' => 'Indefinida', 'is_default' => true]);

    $this->seed(DogBreedSeeder::class);

    expect(Breed::query()->where('species_id', $dog->id)->pluck('name')->all())
        ->toEqualCanonicalizing([...DogBreedSeeder::BREEDS, 'Indefinida'])
        ->and(Breed::query()->where('species_id', $dog->id)->where('is_default', true)->pluck('name')->all())
        ->toBe([DogBreedSeeder::DEFAULT_BREED])
        ->and($previousDefault->fresh()->is_default)->toBeFalse();
});

test('leaves other species untouched and can be re-run without duplicating breeds', function () {
    $dog = Species::factory()->create(['name' => 'Cão']);
    $catDefault = Breed::factory()->for(Species::factory()->create(['name' => 'Gato']))->create(['is_default' => true]);

    $this->seed(DogBreedSeeder::class);
    $this->seed(DogBreedSeeder::class);

    expect(Breed::query()->where('species_id', $dog->id)->count())->toBe(count(DogBreedSeeder::BREEDS))
        ->and($catDefault->fresh()->is_default)->toBeTrue();
});
