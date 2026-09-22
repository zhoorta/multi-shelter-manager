<?php

use App\Models\Facility;
use App\Models\Pet;
use App\Models\Shelter;
use App\Models\User;

test('soft deletes without removing the record from the database', function () {
    $shelter = Shelter::factory()->create();

    $shelter->delete();

    $this->assertSoftDeleted($shelter);
    expect(Shelter::find($shelter->id))->toBeNull();
});

test('users relation only returns users belonging to the shelter, with role and notifications on the pivot', function () {
    $shelter = Shelter::factory()->create();
    $otherShelter = Shelter::factory()->create();

    $ownUser = User::factory()->forShelter($shelter, 'manager', true)->create();
    User::factory()->forShelter($otherShelter, 'staff')->create();

    expect($shelter->users)->toHaveCount(1)
        ->and($shelter->users->first()->is($ownUser))->toBeTrue()
        ->and($shelter->users->first()->pivot->role)->toBe('manager')
        ->and($shelter->users->first()->pivot->vaccination_notifications)->toBeTrue();
});

test('facilities relation only returns facilities belonging to the shelter', function () {
    $shelter = Shelter::factory()->create();
    $otherShelter = Shelter::factory()->create();

    $ownFacility = Facility::factory()->for($shelter)->create();
    Facility::factory()->for($otherShelter)->create();

    expect($shelter->facilities)->toHaveCount(1)
        ->and($shelter->facilities->first()->is($ownFacility))->toBeTrue();
});

test('pets relation only returns pets belonging to the shelter', function () {
    $shelter = Shelter::factory()->create();
    $otherShelter = Shelter::factory()->create();

    $ownPet = Pet::factory()->for($shelter)->create();
    Pet::factory()->for($otherShelter)->create();

    expect($shelter->pets)->toHaveCount(1)
        ->and($shelter->pets->first()->is($ownPet))->toBeTrue();
});
