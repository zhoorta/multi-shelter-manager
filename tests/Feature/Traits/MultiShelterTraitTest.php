<?php

use App\Models\Pet;
use App\Models\Shelter;
use App\Models\User;
use App\Models\Wing;

test('only returns records belonging to the authenticated user\'s shelter', function () {
    $shelter = Shelter::factory()->create();
    $otherShelter = Shelter::factory()->create();

    $ownWing = Wing::factory()->for($shelter)->create();
    Wing::factory()->for($otherShelter)->create();

    $this->actingAs(User::factory()->create(['shelter_id' => $shelter->id, 'role' => 'staff']));

    expect(Wing::all())->toHaveCount(1)
        ->and(Wing::all()->first()->is($ownWing))->toBeTrue();
});

test('does not scope records when no user is authenticated', function () {
    Wing::factory()->create();
    Wing::factory()->create();

    expect(Wing::all())->toHaveCount(2);
});

test('does not scope records for an admin without a shelter_id', function () {
    Wing::factory()->create();
    Wing::factory()->create();

    $this->actingAs(User::factory()->create(['shelter_id' => null, 'role' => 'admin']));

    expect(Wing::all())->toHaveCount(2);
});

test('auto-assigns shelter_id from the authenticated user when creating', function () {
    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->create(['shelter_id' => $shelter->id, 'role' => 'staff']));

    $pet = Pet::factory()->make();
    $pet->shelter_id = null;
    $pet->save();

    expect($pet->fresh()->shelter_id)->toBe($shelter->id);
});

test('does not override an explicitly assigned shelter_id when creating', function () {
    $shelter = Shelter::factory()->create();
    $otherShelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->create(['shelter_id' => $shelter->id, 'role' => 'staff']));

    $pet = Pet::factory()->for($otherShelter)->create();

    expect($pet->shelter_id)->toBe($otherShelter->id);
});
