<?php

use App\Models\Cage;
use App\Models\Pet;
use App\Models\Shelter;
use App\Models\Species;
use Database\Seeders\DemoCagesSeeder;

test('houses every non-adopted pet in a cage destined to its species, in its species wing within the shelter', function () {
    $shelter = Shelter::factory()->create();
    $dogs = Species::factory()->create(['name_plural' => 'Cães']);
    $cats = Species::factory()->create(['name_plural' => 'Gatos']);

    $dogPets = Pet::factory()->count(3)->for($shelter)->create(['species_id' => $dogs->id]);
    $catPet = Pet::factory()->for($shelter)->create(['species_id' => $cats->id]);
    $adoptedPet = Pet::factory()->for($shelter)->create(['species_id' => $dogs->id, 'status' => 'adopted']);

    $this->seed(DemoCagesSeeder::class);

    foreach ([...$dogPets, $catPet] as $pet) {
        $cage = $pet->fresh()->cage;

        expect($cage)->not->toBeNull()
            ->and($cage->wing->facility->shelter_id)->toBe($shelter->id)
            ->and($cage->species_id)->toBe($pet->species_id)
            ->and($cage->wing->name)->toBe($pet->species_id === $dogs->id ? 'Ala - Cães' : 'Ala - Gatos');
    }

    expect($adoptedPet->fresh()->cage_id)->toBeNull();

    Cage::query()->withCount('pets')->get()
        ->each(fn (Cage $cage) => expect($cage->pets_count)->toBeLessThanOrEqual($cage->capacity));
});

test('can be re-run without duplicating spaces or moving already caged pets', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();

    $this->seed(DemoCagesSeeder::class);
    $cageId = $pet->fresh()->cage_id;
    $cageCount = Cage::query()->count();

    $this->seed(DemoCagesSeeder::class);

    expect($pet->fresh()->cage_id)->toBe($cageId)
        ->and(Cage::query()->count())->toBe($cageCount);
});
