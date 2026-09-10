<?php

use App\Models\Facility;
use App\Models\Pet;
use App\Models\Shelter;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

test('only returns records belonging to the authenticated user\'s shelter', function () {
    $shelter = Shelter::factory()->create();
    $otherShelter = Shelter::factory()->create();

    $ownFacility = Facility::factory()->for($shelter)->create();
    Facility::factory()->for($otherShelter)->create();

    $this->actingAs(User::factory()->create(['shelter_id' => $shelter->id, 'role' => 'staff']));

    expect(Facility::all())->toHaveCount(1)
        ->and(Facility::all()->first()->is($ownFacility))->toBeTrue();
});

test('does not scope records when no user is authenticated', function () {
    Facility::factory()->create();
    Facility::factory()->create();

    expect(Facility::all())->toHaveCount(2);
});

test('does not scope records for an admin without a shelter_id', function () {
    Facility::factory()->create();
    Facility::factory()->create();

    $this->actingAs(User::factory()->create(['shelter_id' => null, 'role' => 'admin']));

    expect(Facility::all())->toHaveCount(2);
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

test('resolves the logged-in user from the session without recursing through its own shelter scope', function () {
    $shelter = Shelter::factory()->create();
    $user = User::factory()->create(['shelter_id' => $shelter->id, 'role' => 'staff']);

    $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ])->assertRedirect(route('dashboard', absolute: false));

    // actingAs()/the guard that just logged in keeps the user cached in memory,
    // which would hide this bug. Forget the guard so the next request resolves
    // the user purely from the session, the same as a fresh request would.
    Auth::forgetGuards();

    $response = $this->get(route('dashboard'));

    $response->assertOk();
    $this->assertAuthenticatedAs($user);
});
