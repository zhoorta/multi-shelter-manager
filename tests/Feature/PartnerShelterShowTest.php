<?php

use App\Livewire\PartnerShelterShow;
use App\Models\Pet;
use App\Models\Shelter;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Livewire;

beforeEach(function () {
    config(['app.public_portal_enabled' => true]);
});

/**
 * @param  array<string, mixed>  $attributes
 */
function publishedShelterPet(Shelter $shelter, array $attributes = []): Pet
{
    return Pet::factory()->create([
        'shelter_id' => $shelter->id,
        'publish_to_portal' => true,
        'is_adoptable' => true,
        'status' => 'available',
        ...$attributes,
    ]);
}

test('guests see the shelter details and its published pets only', function () {
    $shelter = Shelter::factory()->create(['name' => 'Abrigo Feliz', 'email' => 'ola@abrigo.test']);
    publishedShelterPet($shelter, ['name' => 'Bolinha']);
    publishedShelterPet($shelter, ['name' => 'Unpublished', 'publish_to_portal' => false]);
    publishedShelterPet(Shelter::factory()->create(), ['name' => 'OtherShelterPet']);

    $this->get(route('shelters.show', $shelter))
        ->assertOk()
        ->assertSee('Abrigo Feliz')
        ->assertSee('mailto:ola@abrigo.test', false)
        ->assertSee('Bolinha')
        ->assertDontSee('Unpublished')
        ->assertDontSee('OtherShelterPet');
});

test('logged-in staff of another shelter see the same published pets', function () {
    $shelter = Shelter::factory()->create();
    publishedShelterPet($shelter, ['name' => 'Bolinha']);
    $this->actingAs(User::factory()->forShelter(Shelter::factory()->create(), 'staff')->create());

    Livewire::test(PartnerShelterShow::class, ['shelter' => $shelter])->assertSee('Bolinha');
});

test('returns not found for a soft-deleted shelter', function () {
    $shelter = Shelter::factory()->create();
    $shelter->delete();

    $this->get(route('shelters.show', $shelter))->assertNotFound();
});

test('redirects to login when the public portal is disabled', function () {
    config(['app.public_portal_enabled' => false]);

    $this->get(route('shelters.show', Shelter::factory()->create()))->assertRedirect(route('login'));
});

test('opens the details of a pet', function () {
    $shelter = Shelter::factory()->create();
    $pet = publishedShelterPet($shelter);

    Livewire::test(PartnerShelterShow::class, ['shelter' => $shelter])
        ->call('showPet', $pet->id)
        ->assertSet('selectedPetId', $pet->id)
        ->assertSee($pet->ref);
});

test('cannot open the details of an unpublished pet', function () {
    $shelter = Shelter::factory()->create();
    $pet = publishedShelterPet($shelter, ['publish_to_portal' => false]);

    Livewire::test(PartnerShelterShow::class, ['shelter' => $shelter])->call('showPet', $pet->id);
})->throws(ModelNotFoundException::class);
