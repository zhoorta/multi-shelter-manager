<?php

use App\Models\User;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('documentation'));
    $response->assertRedirect(route('login'));
});

test('authenticated users of any role can visit the documentation page', function (string $role) {
    $user = User::factory()->create(['role' => $role]);
    $this->actingAs($user);

    $response = $this->get(route('documentation'));

    $response->assertOk();
    $response->assertSee('Application Instructions');
})->with(['admin', 'manager', 'staff']);

test('the documentation content follows the active locale', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    app()->setLocale('pt');

    $response = $this->get(route('documentation'));

    $response->assertOk();
    $response->assertSee('Instruções da Aplicação');

    app()->setLocale('en');
});
