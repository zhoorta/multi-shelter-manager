<?php

use App\Models\Adoption;
use App\Models\Pet;
use App\Models\Shelter;
use App\Models\User;

test('guests are redirected to the login page', function () {
    $pet = Pet::factory()->create();

    $this->get(route('pets.print', $pet))->assertRedirect(route('login'));
});

test('admins are forbidden from viewing the page', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $pet = Pet::factory()->create();

    $this->get(route('pets.print', $pet))->assertForbidden();
});

test('returns 404 when printing a pet belonging to another shelter', function () {
    $otherShelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($otherShelter)->create();

    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    $this->get(route('pets.print', $pet))->assertNotFound();
});

test('managers and staff can view the printable page for a pet belonging to their shelter', function () {
    $shelter = Shelter::factory()->create(['name' => 'Happy Paws', 'city' => 'Lisbon']);
    $pet = Pet::factory()->for($shelter)->create(['name' => 'Rex']);

    $manager = User::factory()->forShelter($shelter, 'manager')->create();
    $this->actingAs($manager);
    $this->get(route('pets.print', $pet))->assertOk()->assertSee('Rex')->assertSee($pet->ref);

    $staff = User::factory()->forShelter($shelter, 'staff')->create();
    $this->actingAs($staff);
    $this->get(route('pets.print', $pet))->assertOk()->assertSee('Rex')->assertSee($pet->ref);
});

test('shows the pet reference, breed, shelter name, city, website and email', function () {
    $shelter = Shelter::factory()->create([
        'name' => 'Happy Paws',
        'city' => 'Lisbon',
        'website' => 'https://happypaws.example.com',
        'email' => 'contact@happypaws.example.com',
    ]);
    $pet = Pet::factory()->for($shelter)->create();

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    $this->get(route('pets.print', $pet))
        ->assertOk()
        ->assertSee($pet->ref)
        ->assertSee($pet->breed->name)
        ->assertSee('Happy Paws')
        ->assertSee('Lisbon')
        ->assertSee('https://happypaws.example.com')
        ->assertSee('contact@happypaws.example.com');
});

test('links to the print page from the pet show page', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    $this->get(route('pets.show', $pet))->assertSee(route('pets.print', $pet), false);
});

test('shows time in captivity since the checkin date for a pet that is neither adopted nor deceased', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create([
        'status' => 'available',
        'checkin_date' => now()->subYear()->subMonths(2),
        'date_of_death' => null,
    ]);

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    $this->get(route('pets.print', $pet))
        ->assertOk()
        ->assertSee(__('In captivity'))
        ->assertSee($pet->time_in_captivity)
        ->assertDontSee(__('Adopted at'))
        ->assertDontSee(__('Deceased at'));
});

test('shows the adoption date instead of time in captivity when the pet is adopted', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create(['status' => 'available', 'checkin_date' => now()->subYear()]);
    $adoption = Adoption::factory()->for($pet)->create(['adoption_date' => '2025-03-10', 'return_date' => null]);
    $pet->update(['status' => 'adopted']);

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    $this->get(route('pets.print', $pet))
        ->assertOk()
        ->assertSee(__('Adopted at'))
        ->assertSee($adoption->adoption_date->format('d/m/Y'))
        ->assertDontSee(__('In captivity'));
});

test('shows the death date and hides the age field when the pet is deceased', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create([
        'birth_date' => now()->subYears(2),
        'checkin_date' => now()->subYear(),
        'date_of_death' => '2025-06-15',
    ]);

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    $this->get(route('pets.print', $pet))
        ->assertOk()
        ->assertSee(__('Deceased at'))
        ->assertSee($pet->date_of_death->format('d/m/Y'))
        ->assertDontSee(__('In captivity'))
        ->assertDontSee(__('Adopted at'))
        ->assertDontSee(__('Age'));
});

test('viewers can print a pet', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create(['name' => 'Rex']);
    $this->actingAs(User::factory()->forShelter($shelter, 'viewer')->create());

    $this->get(route('pets.print', $pet))->assertOk()->assertSee('Rex');
});
