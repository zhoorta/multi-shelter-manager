<?php

test('guests can view the english privacy policy', function () {
    app()->setLocale('en');

    $this->get(route('privacy-policy'))
        ->assertOk()
        ->assertSee('Privacy Policy');
});

test('shows the portuguese policy when the locale is pt', function () {
    app()->setLocale('pt');

    $this->get(route('privacy-policy'))
        ->assertOk()
        ->assertSee('Política de Privacidade')
        ->assertSee('www.cnpd.pt');
});

test('falls back to the english policy for a locale without its own text', function () {
    app()->setLocale('fr');

    $this->get(route('privacy-policy'))
        ->assertOk()
        ->assertSee('Who is responsible for your data');
});

test('shows the configured privacy contact e-mail', function () {
    config(['app.privacy_contact_email' => 'privacidade@focinhos.test']);

    $this->get(route('privacy-policy'))->assertSee('mailto:privacidade@focinhos.test', false);
});

test('lists the session cookie by its configured name', function () {
    config(['session.cookie' => 'focinhos-session']);

    $this->get(route('privacy-policy'))
        ->assertSee('focinhos-session')
        ->assertSee('XSRF-TOKEN');
});

test('the public pages load no fonts from google', function () {
    $this->get(route('home'))->assertDontSee('fonts.googleapis.com', false);
});

test('the public footer links to the privacy policy', function () {
    $this->get(route('home'))->assertSee(route('privacy-policy'), false);
});
