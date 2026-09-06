<?php

use App\Models\Shelter;
use App\Models\User;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertOk();
});

test('the layout shows the navigation links and the user shelter name', function () {
    $shelter = Shelter::factory()->create(['name' => 'Happy Paws Shelter']);
    $user = User::factory()->create(['shelter_id' => $shelter->id, 'role' => 'staff']);
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));

    $response->assertOk();
    $response->assertSee(['Dashboard', 'Pets', 'Wings', 'Vaccines', 'Sicknesses']);
    $response->assertSee('Happy Paws Shelter');
    $response->assertSee($user->name);
    $response->assertDontSee('Users');
});

test('only managers and admins see the users navigation link', function () {
    $manager = User::factory()->create(['role' => 'manager']);
    $this->actingAs($manager);

    $response = $this->get(route('dashboard'));

    $response->assertOk();
    $response->assertSee('Users');
});
