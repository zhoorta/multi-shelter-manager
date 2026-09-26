<?php

beforeEach(function () {
    config(['app.public_portal_enabled' => true]);
});

test('guests can view the about page with the contact e-mail', function () {
    config(['app.contact_email' => 'hello@shelters.test']);

    $this->get(route('about'))
        ->assertOk()
        ->assertSee('nonprofit')
        ->assertSee('foster families')
        ->assertSee('mailto:hello@shelters.test', false);
});

test('redirects to the login page when the public portal is disabled', function () {
    config(['app.public_portal_enabled' => false]);

    $this->get(route('about'))->assertRedirect(route('login'));
});

test('public pages hide the about link when the public portal is disabled', function () {
    config(['app.public_portal_enabled' => false]);

    $this->get(route('privacy-policy'))->assertDontSee('href="'.route('about').'"', false);
});

test('hides the contact block when no e-mail is configured', function () {
    config(['app.contact_email' => null]);

    $this->get(route('about'))
        ->assertOk()
        ->assertDontSee('mailto:', false);
});

test('shows the portuguese text when the locale is pt', function () {
    app()->setLocale('pt');

    $this->get(route('about'))
        ->assertOk()
        ->assertSee('sem fins lucrativos')
        ->assertSee('candidatar-se à adoção online');
});
