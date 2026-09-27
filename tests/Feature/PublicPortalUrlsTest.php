<?php

use App\Models\Pet;
use App\Models\Shelter;
use Illuminate\Routing\RouteCollection;
use Illuminate\Support\Facades\Route;

beforeEach(function () {
    config(['app.public_portal_enabled' => true]);
});

/**
 * Register the web routes again as an installation in another language
 * (APP_LOCALE) would, since the public paths are read when routes load.
 */
function loadRoutesForInstallationLocale(string $locale): void
{
    config(['app.locale' => $locale]);
    app()->setLocale($locale);

    $router = app('router');
    $router->setRoutes(new RouteCollection);
    Route::middleware('web')->group(base_path('routes/web.php'));
    $router->getRoutes()->refreshNameLookups();
    app('url')->setRoutes($router->getRoutes());
}

test('the shelter page is served at its slug', function () {
    $shelter = Shelter::factory()->create(['name' => 'Abrigo Feliz', 'slug' => 'abrigo-feliz']);

    expect(route('shelters.show', $shelter))->toBe(url('shelters/abrigo-feliz'));
    $this->get('/shelters/abrigo-feliz')->assertOk()->assertSee('Abrigo Feliz');
    $this->get('/shelters/no-such-shelter')->assertNotFound();
});

test('a portuguese installation serves the public portal under portuguese paths', function () {
    loadRoutesForInstallationLocale('pt');
    $shelter = Shelter::factory()->create(['slug' => 'abrigo-feliz']);
    $pet = Pet::factory()->publishedToPortal()->for($shelter)->create();

    expect(route('shelters'))->toBe(url('abrigos'))
        ->and(route('shelters.show', $shelter))->toBe(url('abrigos/abrigo-feliz'))
        ->and(route('about'))->toBe(url('sobre'))
        ->and(route('adoption-applications.create', $pet->ref))->toBe(url("adotar/{$pet->ref}"))
        ->and($pet->publicPageUrl())->toStartWith(url("animais/{$pet->id}/"))
        ->and(route('shelters.feed', $shelter))->toBe(url("shelters/{$shelter->id}/feed"));
});

test('a portuguese installation moves the old english about link permanently', function () {
    loadRoutesForInstallationLocale('pt');

    $this->get('/about')->assertStatus(301)->assertRedirect(url('sobre'));
});
