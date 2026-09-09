<?php

use App\Models\Pet;
use App\Models\Shelter;
use App\Models\Sickness;
use App\Models\Species;
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

test('links back to the pets list scoped to the pet species', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    $this->get(route('pets.show', $pet))
        ->assertSee(route('pets.index', ['speciesFilter' => $pet->species_id]), false)
        ->assertSee($pet->species->name_plural);
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

test('shows the birth date and death date', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create([
        'birth_date' => '2018-05-01',
        'date_of_death' => '2024-03-15',
    ]);

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    $this->get(route('pets.show', $pet))
        ->assertOk()
        ->assertSeeInOrder(['Birth Date', '01/05/2018', 'Death Date', '15/03/2024']);
});

test('shows the checkin date', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create(['checkin_date' => '2023-01-10']);

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    $this->get(route('pets.show', $pet))
        ->assertOk()
        ->assertSeeInOrder(['Checkin Date', '10/01/2023']);
});

test('shows the pet description as rendered HTML in its own box at the end', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create(['description' => '<p>Loves <b>belly rubs</b>.</p>']);

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    $this->get(route('pets.show', $pet))
        ->assertOk()
        ->assertSeeInOrder(['Checkin Date', 'Description', '<b>belly rubs</b>'], false);
});

test('shows a placeholder when the pet has no description', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create(['description' => null]);

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    $this->get(route('pets.show', $pet))
        ->assertOk()
        ->assertSeeInOrder(['Description', '—']);
});

test('lists the species sicknesses next to neutered status, marking which ones the pet has', function () {
    $shelter = Shelter::factory()->create();
    $species = Species::factory()->create();
    $otherSpecies = Species::factory()->create();
    $pet = Pet::factory()->for($shelter)->create(['species_id' => $species->id, 'is_neutered' => false]);

    $diagnosed = Sickness::factory()->create(['name' => 'Parvovirus']);
    $diagnosed->species()->attach($species);
    $pet->sicknesses()->attach($diagnosed, ['diagnosed_at' => now()]);

    $notDiagnosed = Sickness::factory()->create(['name' => 'Ringworm']);
    $notDiagnosed->species()->attach($species);

    $unrelated = Sickness::factory()->create(['name' => 'Feline Leukemia']);
    $unrelated->species()->attach($otherSpecies);

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    $this->get(route('pets.show', $pet))
        ->assertOk()
        ->assertSeeInOrder(['Neutered / Spayed', 'No', 'Parvovirus', 'Yes', 'Ringworm', 'No'])
        ->assertDontSee('Feline Leukemia');
});
