<?php

use App\Models\Shelter;
use App\Models\User;

beforeEach(function () {
    config(['app.public_portal_enabled' => true]);
});

test('public pages are indexable and expose description, canonical and open graph tags', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee('<meta name="robots" content="index, follow, max-image-preview:large">', false)
        ->assertSee('<meta name="description" content="Find a shelter animal waiting for a home.', false)
        ->assertSee('<link rel="canonical" href="'.route('home').'">', false)
        ->assertSee('<meta property="og:title"', false)
        ->assertSee('"@type":"WebSite"', false);
});

test('the canonical url ignores filter query strings', function () {
    $this->get(route('home', ['species' => 1]))
        ->assertSee('<link rel="canonical" href="'.route('home').'">', false);
});

test('backoffice pages are not indexable', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('<meta name="robots" content="noindex, nofollow">', false)
        ->assertDontSee('og:title', false);
});

test('shelter page describes the shelter with animal shelter structured data', function () {
    $shelter = Shelter::factory()->create([
        'name' => 'Abrigo Feliz',
        'description' => 'Um abrigo cheio de amor.',
        'phone' => '912345678',
    ]);

    $this->get(route('shelters.show', $shelter))
        ->assertOk()
        ->assertSee('<meta name="description" content="Um abrigo cheio de amor.">', false)
        ->assertSee('"@type":"AnimalShelter"', false)
        ->assertSee('"telephone":"912345678"', false);
});

test('structured data cannot break out of its script tag', function () {
    $shelter = Shelter::factory()->create(['description' => '</script><script>alert(1)</script>']);

    $this->get(route('shelters.show', $shelter))
        ->assertOk()
        ->assertDontSee('</script><script>alert(1)', false);
});

test('sitemap lists the public pages and every shelter', function () {
    $shelter = Shelter::factory()->create();

    $this->get(route('sitemap'))
        ->assertOk()
        ->assertHeader('Content-Type', 'application/xml; charset=UTF-8')
        ->assertSee('<loc>'.route('home').'</loc>', false)
        ->assertSee('<loc>'.route('about').'</loc>', false)
        ->assertSee('<loc>'.route('shelters.show', $shelter).'</loc>', false);
});

test('robots.txt blocks the backoffice and announces the sitemap', function () {
    $this->get(route('robots'))
        ->assertOk()
        ->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
        ->assertSee('Disallow: /admin')
        ->assertSee('Sitemap: '.route('sitemap'))
        ->assertDontSee("Disallow: /\n", false);
});

test('robots.txt blocks the whole site when the public portal is disabled', function () {
    config(['app.public_portal_enabled' => false]);

    $this->get(route('robots'))
        ->assertOk()
        ->assertSee("Disallow: /\n", false)
        ->assertDontSee('Sitemap:');
});

test('sitemap is unavailable when the public portal is disabled', function () {
    config(['app.public_portal_enabled' => false]);

    $this->get(route('sitemap'))->assertRedirect(route('login'));
});
