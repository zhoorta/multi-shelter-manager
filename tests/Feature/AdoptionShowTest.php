<?php

use App\Models\Adoption;
use App\Models\Pet;
use App\Models\Shelter;
use App\Models\User;

test('guests are redirected to the login page', function () {
    $pet = Pet::factory()->create();
    $adoption = Adoption::factory()->for($pet)->create();

    $this->get(route('pets.adopt.show', [$pet, $adoption]))->assertRedirect(route('login'));
});

test('admins are forbidden from viewing the page', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $pet = Pet::factory()->create();
    $adoption = Adoption::factory()->for($pet)->create();

    $this->get(route('pets.adopt.show', [$pet, $adoption]))->assertForbidden();
});

test('managers and staff can view an adoption for a pet in their shelter', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();
    $adoption = Adoption::factory()->for($pet)->create(['name' => 'Maria Silva']);

    $manager = User::factory()->forShelter($shelter, 'manager')->create();
    $this->actingAs($manager);
    $this->get(route('pets.adopt.show', [$pet, $adoption]))->assertOk()->assertSee('Maria Silva');

    $staff = User::factory()->forShelter($shelter, 'staff')->create();
    $this->actingAs($staff);
    $this->get(route('pets.adopt.show', [$pet, $adoption]))->assertOk()->assertSee('Maria Silva');
});

test('returns 404 when the adoption does not belong to the given pet', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();
    $otherPet = Pet::factory()->for($shelter)->create();
    $adoption = Adoption::factory()->for($otherPet)->create();

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    $this->get(route('pets.adopt.show', [$pet, $adoption]))->assertNotFound();
});

test('returns 404 when the pet belongs to another shelter', function () {
    $otherShelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($otherShelter)->create();
    $adoption = Adoption::factory()->for($pet)->create();

    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    $this->get(route('pets.adopt.show', [$pet, $adoption]))->assertNotFound();
});

test('shows the owner contacts and adoption details', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();
    $adoption = Adoption::factory()->for($pet)->create([
        'name' => 'Maria Silva',
        'email' => 'maria@example.com',
        'phone' => '912345678',
    ]);

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    $this->get(route('pets.adopt.show', [$pet, $adoption]))
        ->assertOk()
        ->assertSeeInOrder(['Maria Silva', 'maria@example.com', '912345678']);
});

test('the edit link sends the form back to the adoptions list', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();
    $adoption = Adoption::factory()->for($pet)->create();

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    $this->get(route('pets.adopt.show', [$pet, $adoption]))
        ->assertOk()
        ->assertSeeHtml('href="'.route('pets.adopt.edit', [$pet, $adoption]).'?from=adoptions"');
});
