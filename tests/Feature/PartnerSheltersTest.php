<?php

use App\Livewire\PartnerShelters;
use App\Models\Pet;
use App\Models\Region;
use App\Models\Shelter;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    config(['app.public_portal_enabled' => true]);
});

test('guests see every partner shelter with its contacts', function () {
    Shelter::factory()->create(['name' => 'Abrigo Feliz', 'email' => 'ola@abrigo.test']);
    Shelter::factory()->create(['name' => 'Casa dos Bigodes']);

    $this->get(route('shelters'))
        ->assertOk()
        ->assertSee('Abrigo Feliz')
        ->assertSee('mailto:ola@abrigo.test', false)
        ->assertSeeText('ola@abrigo.test')
        ->assertSee('Casa dos Bigodes');
});

test('redirects to login when the public portal is disabled', function () {
    config(['app.public_portal_enabled' => false]);

    $this->get(route('shelters'))->assertRedirect(route('login'));
});

test('hides soft-deleted shelters', function () {
    Shelter::factory()->create(['name' => 'Closed Shelter'])->delete();

    Livewire::test(PartnerShelters::class)->assertDontSee('Closed Shelter');
});

test('counts only pets published for adoption, across shelters for logged-in staff', function () {
    $ownShelter = Shelter::factory()->create();
    $otherShelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->forShelter($ownShelter, 'staff')->create());

    $published = ['publish_to_portal' => true, 'is_adoptable' => true, 'status' => 'available', 'shelter_id' => $otherShelter->id];
    Pet::factory()->count(2)->create($published);
    Pet::factory()->create([...$published, 'publish_to_portal' => false]);
    Pet::factory()->create([...$published, 'status' => 'adopted']);

    $shelters = Livewire::test(PartnerShelters::class)->instance()->shelters;

    expect($shelters->firstWhere('id', $otherShelter->id)->published_pets_count)->toBe(2)
        ->and($shelters->firstWhere('id', $ownShelter->id)->published_pets_count)->toBe(0);
});

test('filters shelters by region', function () {
    $region = Region::factory()->create();

    Shelter::factory()->create(['name' => 'InRegion', 'region_id' => $region->id]);
    Shelter::factory()->create(['name' => 'OutOfRegion', 'region_id' => Region::factory()->create()->id]);

    Livewire::test(PartnerShelters::class)
        ->set('regionFilter', (string) $region->id)
        ->assertSee('InRegion')
        ->assertDontSee('OutOfRegion');
});

test('links each shelter to its public page', function () {
    $shelter = Shelter::factory()->create();

    Livewire::test(PartnerShelters::class)->assertSee(route('shelters.show', $shelter));
});
