<?php

use App\Models\Pet;
use App\Models\Shelter;

beforeEach(function () {
    config(['app.public_portal_enabled' => true]);
});

test('feed lists the shelter published pets with a link to each one', function () {
    $shelter = Shelter::factory()->create(['name' => 'Abrigo Feliz']);
    $pet = Pet::factory()->for($shelter)->create(['name' => 'Bolinha', 'publish_to_portal' => true, 'is_adoptable' => true]);
    Pet::factory()->for($shelter)->create(['name' => 'Unpublished', 'publish_to_portal' => false, 'is_adoptable' => true]);
    Pet::factory()->create(['name' => 'OtherShelterPet', 'publish_to_portal' => true, 'is_adoptable' => true]);

    $response = $this->get(route('shelters.feed', $shelter));

    $response
        ->assertHeader('Content-Type', 'application/rss+xml; charset=UTF-8')
        ->assertSee('<title>Abrigo Feliz</title>', false)
        ->assertSee('<title>Bolinha is looking for a family!</title>', false)
        ->assertSee('<link>'.e(route('shelters.show', ['shelter' => $shelter, 'animal' => $pet->id])).'</link>', false)
        ->assertDontSee('Unpublished')
        ->assertDontSee('OtherShelterPet');
});

test('feed redirects to login when the public portal is disabled', function () {
    config(['app.public_portal_enabled' => false]);

    $this->get(route('shelters.feed', Shelter::factory()->create()))->assertRedirect(route('login'));
});
