<?php

use App\Models\Shelter;
use App\Models\User;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('documentation'));
    $response->assertRedirect(route('login'));
});

test('authenticated users of any role can visit the documentation page', function (string $role) {
    $user = $role === 'admin'
        ? User::factory()->admin()->create()
        : User::factory()->forShelter(Shelter::factory()->create(), $role)->create();
    $this->actingAs($user);

    $response = $this->get(route('documentation'));

    $response->assertOk();
    $response->assertSee('Application Instructions');
    $response->assertSeeInOrder(['Getting Started', 'Recommended setup order', 'Finding your way around', 'Status', 'Options and location', 'Vaccinations', 'Adoptions', 'Sponsorships', 'Volunteers', 'Facilities', 'Public Portal', 'What is shown publicly', 'Administration', 'Regions', 'Settings']);
})->with(['admin', 'manager', 'staff', 'viewer']);

test('the documentation content follows the active locale', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    app()->setLocale('pt');

    $response = $this->get(route('documentation'));

    $response->assertOk();
    $response->assertSee('Instruções da Aplicação');
    $response->assertSee('Ordem de configuração recomendada');
    $response->assertSee('O que é mostrado publicamente');
    $response->assertDontSee('Recommended setup order');

    app()->setLocale('en');
});

test('the documentation content is available in spanish', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    app()->setLocale('es');

    $response = $this->get(route('documentation'));

    $response->assertOk();
    $response->assertSee('Instrucciones de la Aplicación');
    $response->assertSee('Orden de configuración recomendado');
    $response->assertDontSee('Recommended setup order');

    app()->setLocale('en');
});

test('the documentation content is available in french', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    app()->setLocale('fr');

    $response = $this->get(route('documentation'));

    $response->assertOk();
    $response->assertSee("Instructions de l'Application", false);
    $response->assertSee('Ordre de configuration recommandé');
    $response->assertDontSee('Recommended setup order');

    app()->setLocale('en');
});

test('the documentation content is available in german', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    app()->setLocale('de');

    $response = $this->get(route('documentation'));

    $response->assertOk();
    $response->assertSee('Anleitung zur Anwendung');
    $response->assertSee('Empfohlene Reihenfolge der Einrichtung');
    $response->assertDontSee('Recommended setup order');

    app()->setLocale('en');
});

test('the documentation content is available in dutch', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    app()->setLocale('nl');

    $response = $this->get(route('documentation'));

    $response->assertOk();
    $response->assertSee('Handleiding van de applicatie');
    $response->assertSee('Aanbevolen volgorde van inrichting');
    $response->assertDontSee('Recommended setup order');

    app()->setLocale('en');
});

test('the documentation content is available in polish', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    app()->setLocale('pl');

    $response = $this->get(route('documentation'));

    $response->assertOk();
    $response->assertSee('Instrukcja aplikacji');
    $response->assertSee('Zalecana kolejność konfiguracji');
    $response->assertDontSee('Recommended setup order');

    app()->setLocale('en');
});

test('the documentation content is available in italian', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    app()->setLocale('it');

    $response = $this->get(route('documentation'));

    $response->assertOk();
    $response->assertSee("Istruzioni dell'Applicazione", false);
    $response->assertSee('Ordine di configurazione consigliato');
    $response->assertDontSee('Recommended setup order');

    app()->setLocale('en');
});

test('the documentation content is available in swedish', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    app()->setLocale('sv');

    $response = $this->get(route('documentation'));

    $response->assertOk();
    $response->assertSee('Instruktioner för applikationen');
    $response->assertSee('Rekommenderad ordning för uppstart');
    $response->assertDontSee('Recommended setup order');

    app()->setLocale('en');
});

test('the documentation content is available in danish', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    app()->setLocale('da');

    $response = $this->get(route('documentation'));

    $response->assertOk();
    $response->assertSee('Vejledning til applikationen');
    $response->assertSee('Anbefalet rækkefølge for opsætning');
    $response->assertDontSee('Recommended setup order');

    app()->setLocale('en');
});

test('describes the viewer role', function () {
    $this->actingAs(User::factory()->create());

    $this->get(route('documentation'))
        ->assertOk()
        ->assertSeeInOrder(['Roles', 'Staff', 'Viewer', 'read-only access']);
});
