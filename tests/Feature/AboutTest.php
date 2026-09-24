<?php

test('guests can view the about page with the contact e-mail', function () {
    config(['app.contact_email' => 'hello@shelters.test']);

    $this->get(route('about'))
        ->assertOk()
        ->assertSee('nonprofit')
        ->assertSee('mailto:hello@shelters.test', false);
});

test('stays public when the adoption portal is disabled', function () {
    config(['app.public_portal_enabled' => false]);

    $this->get(route('about'))->assertOk();
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
        ->assertSee('sem fins lucrativos');
});
