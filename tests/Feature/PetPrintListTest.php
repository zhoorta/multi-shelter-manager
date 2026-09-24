<?php

use App\Models\Pet;
use App\Models\Shelter;
use App\Models\User;

test('guests are redirected to the login page', function () {
    $this->get(route('pets.print.list'))->assertRedirect(route('login'));
});

test('admins are forbidden from viewing the page', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $this->get(route('pets.print.list'))->assertForbidden();
});

test('managers and staff can view the printable list for their shelter', function () {
    $shelter = Shelter::factory()->create(['name' => 'Happy Paws', 'city' => 'Lisbon']);
    Pet::factory()->for($shelter)->create(['name' => 'Rex']);

    $manager = User::factory()->forShelter($shelter, 'manager')->create();
    $this->actingAs($manager);
    $this->get(route('pets.print.list'))->assertOk()->assertSee('Rex')->assertSee('Happy Paws');

    $staff = User::factory()->forShelter($shelter, 'staff')->create();
    $this->actingAs($staff);
    $this->get(route('pets.print.list'))->assertOk()->assertSee('Rex')->assertSee('Happy Paws');
});

test('only lists pets belonging to the acting user\'s shelter', function () {
    $shelter = Shelter::factory()->create();
    $otherShelter = Shelter::factory()->create();

    Pet::factory()->for($shelter)->create(['name' => 'Rex']);
    Pet::factory()->for($otherShelter)->create(['name' => 'Other Shelter Dog']);

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    $this->get(route('pets.print.list'))
        ->assertSee('Rex')
        ->assertDontSee('Other Shelter Dog');
});

test('respects the search filter passed via the query string', function () {
    $shelter = Shelter::factory()->create();
    Pet::factory()->for($shelter)->create(['name' => 'Rex']);
    Pet::factory()->for($shelter)->create(['name' => 'Bella']);

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    $this->get(route('pets.print.list', ['search' => 'Rex']))
        ->assertOk()
        ->assertSee('Rex')
        ->assertDontSee('Bella');
});

test('respects the status filter passed via the query string', function () {
    $shelter = Shelter::factory()->create();
    Pet::factory()->for($shelter)->create(['name' => 'Rex', 'status' => 'available']);
    Pet::factory()->for($shelter)->create(['name' => 'Bella', 'status' => 'adopted']);

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    $this->get(route('pets.print.list', ['statusFilter' => 'adopted']))
        ->assertOk()
        ->assertSee('Bella')
        ->assertDontSee('Rex');
});

test('shows a placeholder message when no pets match', function () {
    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    $this->get(route('pets.print.list'))->assertSee(__('No pets registered'));
});

test('links to the print list page from the manage pets page', function () {
    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    $this->get(route('pets.index'))->assertSee(route('pets.print.list'), false);
});

test('viewers can print the pets list', function () {
    $shelter = Shelter::factory()->create();
    Pet::factory()->for($shelter)->create(['name' => 'Rex']);
    $this->actingAs(User::factory()->forShelter($shelter, 'viewer')->create());

    $this->get(route('pets.print.list'))->assertOk()->assertSee('Rex');
});
