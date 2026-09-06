<?php

use App\Models\Pet;
use App\Models\Shelter;
use App\Models\User;
use App\Models\Wing;

test('soft deletes without removing the record from the database', function () {
    $shelter = Shelter::factory()->create();

    $shelter->delete();

    $this->assertSoftDeleted($shelter);
    expect(Shelter::find($shelter->id))->toBeNull();
});

test('users relation only returns users belonging to the shelter', function () {
    $shelter = Shelter::factory()->create();
    $otherShelter = Shelter::factory()->create();

    $ownUser = User::factory()->create(['shelter_id' => $shelter->id]);
    User::factory()->create(['shelter_id' => $otherShelter->id]);

    expect($shelter->users)->toHaveCount(1)
        ->and($shelter->users->first()->is($ownUser))->toBeTrue();
});

test('wings relation only returns wings belonging to the shelter', function () {
    $shelter = Shelter::factory()->create();
    $otherShelter = Shelter::factory()->create();

    $ownWing = Wing::factory()->for($shelter)->create();
    Wing::factory()->for($otherShelter)->create();

    expect($shelter->wings)->toHaveCount(1)
        ->and($shelter->wings->first()->is($ownWing))->toBeTrue();
});

test('pets relation only returns pets belonging to the shelter', function () {
    $shelter = Shelter::factory()->create();
    $otherShelter = Shelter::factory()->create();

    $ownPet = Pet::factory()->for($shelter)->create();
    Pet::factory()->for($otherShelter)->create();

    expect($shelter->pets)->toHaveCount(1)
        ->and($shelter->pets->first()->is($ownPet))->toBeTrue();
});
