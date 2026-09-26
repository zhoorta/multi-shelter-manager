<?php

use App\Models\Pet;
use App\Models\Region;
use App\Models\Shelter;
use App\Models\Species;
use App\Models\User;

beforeEach(function () {
    config(['app.public_portal_enabled' => true]);
});

test('a published pet has its own indexable page', function () {
    $shelter = Shelter::factory()->for(Region::factory()->create(['name' => 'Açores - Faial']))->create(['name' => 'Abrigo Feliz', 'city' => 'Horta']);
    $pet = Pet::factory()->publishedToPortal()->for($shelter)->for(Species::factory()->create(['name' => 'Cão']))->create([
        'name' => 'Frozen',
        'description' => '<p>Muito meiga.</p>',
    ]);

    $response = $this->get($pet->publicPageUrl());

    expect($pet->publicPageUrl())->toBe(url("animais/{$pet->id}/frozen-cao-horta-acores-faial"));
    $response->assertOk()
        ->assertSee('<title>', false)
        ->assertSee('Frozen — Cão for adoption in Horta, Açores - Faial - '.config('app.name'))
        ->assertSee('<meta name="robots" content="index, follow, max-image-preview:large">', false)
        ->assertSee('<link rel="canonical" href="'.$pet->publicPageUrl().'">', false)
        ->assertSee('"@type":"BreadcrumbList"', false)
        ->assertSee('Muito meiga.')
        ->assertSee(route('adoption-applications.create', $pet->ref), false);
    expect($pet->fresh()->view_count)->toBe(1);
});

test('the slug and location do not repeat a region named like its city', function () {
    $shelter = Shelter::factory()->for(Region::factory()->create(['name' => 'Lisboa']))->create(['city' => 'Lisboa']);
    $pet = Pet::factory()->publishedToPortal()->for($shelter)->for(Species::factory()->create(['name' => 'Gato']))->create(['name' => 'Bobi']);

    expect($pet->publicSlug())->toBe('bobi-gato-lisboa')
        ->and($pet->publicLocation())->toBe('Lisboa');
});

test('logged-in staff of another shelter see the same pet page', function () {
    $pet = Pet::factory()->publishedToPortal()->create(['name' => 'Frozen']);
    $this->actingAs(User::factory()->forShelter(Shelter::factory()->create(), 'staff')->create());

    $this->get($pet->publicPageUrl())->assertOk()->assertSee('Frozen');
});

test('a missing or outdated slug moves permanently to the current url', function (string $slug) {
    $pet = Pet::factory()->publishedToPortal()->create();

    $this->get(url("animais/{$pet->id}/{$slug}"))
        ->assertStatus(301)
        ->assertRedirect($pet->publicPageUrl());
})->with([
    'outdated' => 'old-name',
    'missing' => '',
]);

test('an adopted pet keeps a page that is not indexed and offers no adoption', function () {
    $pet = Pet::factory()->create(['name' => 'Frozen', 'publish_to_portal' => true, 'status' => 'adopted']);

    $this->get($pet->publicPageUrl())
        ->assertOk()
        ->assertSee('Frozen has already found a family!')
        ->assertSee('<meta name="robots" content="noindex, follow">', false)
        ->assertSee('<meta property="og:title"', false)
        ->assertDontSee(route('adoption-applications.create', $pet->ref), false);
    expect($pet->fresh()->view_count)->toBe(0);
});

test('pets that are not public have no page', function (array $attributes) {
    $pet = Pet::factory()->publishedToPortal()->create($attributes);

    $this->get($pet->publicPageUrl())->assertNotFound();
})->with([
    'unpublished' => [['publish_to_portal' => false]],
    'not adoptable' => [['is_adoptable' => false]],
    'adopted but never published' => [['publish_to_portal' => false, 'status' => 'adopted']],
    'deceased' => [['status' => 'adopted', 'date_of_death' => now()->subDay()]],
]);

test('a pet of a deleted shelter has no page', function () {
    $pet = Pet::factory()->publishedToPortal()->create();
    $url = $pet->publicPageUrl();
    $pet->shelter->delete();

    $this->get($url)->assertNotFound();
});

test('redirects to login when the public portal is disabled', function () {
    $pet = Pet::factory()->publishedToPortal()->create();
    config(['app.public_portal_enabled' => false]);

    $this->get($pet->publicPageUrl())->assertRedirect(route('login'));
});
