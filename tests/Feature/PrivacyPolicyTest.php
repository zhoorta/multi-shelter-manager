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

test('shows the spanish policy when the locale is es', function () {
    app()->setLocale('es');

    $this->get(route('privacy-policy'))
        ->assertOk()
        ->assertSee('Política de Privacidad')
        ->assertSee('Quién es responsable de sus datos');
});

test('shows the french policy when the locale is fr', function () {
    app()->setLocale('fr');

    $this->get(route('privacy-policy'))
        ->assertOk()
        ->assertSee('Politique de Confidentialité')
        ->assertSee('Qui est responsable de vos données');
});

test('shows the german policy when the locale is de', function () {
    app()->setLocale('de');

    $this->get(route('privacy-policy'))
        ->assertOk()
        ->assertSee('Datenschutzerklärung')
        ->assertSee('Wer für Ihre Daten verantwortlich ist');
});

test('shows the dutch policy when the locale is nl', function () {
    app()->setLocale('nl');

    $this->get(route('privacy-policy'))
        ->assertOk()
        ->assertSee('Privacybeleid')
        ->assertSee('Wie verantwoordelijk is voor uw gegevens');
});

test('shows the polish policy when the locale is pl', function () {
    app()->setLocale('pl');

    $this->get(route('privacy-policy'))
        ->assertOk()
        ->assertSee('Polityka prywatności')
        ->assertSee('Kto odpowiada za Twoje dane');
});

test('shows the italian policy when the locale is it', function () {
    app()->setLocale('it');

    $this->get(route('privacy-policy'))
        ->assertOk()
        ->assertSee('Informativa sulla Privacy')
        ->assertSee('Chi è responsabile dei tuoi dati');
});

test('shows the swedish policy when the locale is sv', function () {
    app()->setLocale('sv');

    $this->get(route('privacy-policy'))
        ->assertOk()
        ->assertSee('Integritetspolicy')
        ->assertSee('Vem som ansvarar för dina uppgifter');
});

test('shows the danish policy when the locale is da', function () {
    app()->setLocale('da');

    $this->get(route('privacy-policy'))
        ->assertOk()
        ->assertSee('Privatlivspolitik')
        ->assertSee('Hvem der er ansvarlig for dine oplysninger');
});

test('falls back to the english policy for a locale without its own text', function () {
    app()->setLocale('fi');

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
    config(['app.public_portal_enabled' => true]);

    $this->get(route('home'))->assertDontSee('fonts.googleapis.com', false);
});

test('the public footer links to the privacy policy', function () {
    config(['app.public_portal_enabled' => true]);

    $this->get(route('home'))->assertSee(route('privacy-policy'), false);
});

test('the privacy policy stays available when the public portal is disabled', function () {
    config(['app.public_portal_enabled' => false]);

    $this->get(route('privacy-policy'))->assertOk();
});
