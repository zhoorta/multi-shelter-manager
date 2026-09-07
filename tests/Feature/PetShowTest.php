<?php

use App\Models\Pet;
use App\Models\Shelter;
use App\Models\User;

test('guests are redirected to the login page', function () {
    $pet = Pet::factory()->create();

    $this->get(route('pets.show', $pet))->assertRedirect(route('login'));
});

test('admins are forbidden from viewing the page', function () {
    $admin = User::factory()->create(['role' => 'admin', 'shelter_id' => null]);
    $this->actingAs($admin);

    $pet = Pet::factory()->create();

    $this->get(route('pets.show', $pet))->assertForbidden();
});

test('managers and staff can view a pet belonging to their shelter', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create(['name' => 'Rex']);

    $manager = User::factory()->create(['role' => 'manager', 'shelter_id' => $shelter->id]);
    $this->actingAs($manager);
    $this->get(route('pets.show', $pet))->assertOk()->assertSee('Rex');

    $staff = User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]);
    $this->actingAs($staff);
    $this->get(route('pets.show', $pet))->assertOk()->assertSee('Rex');
});

test('returns 404 when viewing a pet belonging to another shelter', function () {
    $otherShelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($otherShelter)->create();

    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    $this->get(route('pets.show', $pet))->assertNotFound();
});

test('links to the edit page', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    $this->get(route('pets.show', $pet))->assertSee(route('pets.edit', $pet), false);
});

test('does not display placeholder text when color and fur type are not assigned', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create([
        'primary_color_id' => null,
        'secondary_color_id' => null,
        'fur_type_id' => null,
    ]);

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    $this->get(route('pets.show', $pet))
        ->assertOk()
        ->assertDontSee('No Color Assigned')
        ->assertDontSee('No Fur Type Assigned');
});
