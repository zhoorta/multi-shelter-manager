<?php

use App\Livewire\Dashboard;
use App\Models\Cage;
use App\Models\Facility;
use App\Models\Pet;
use App\Models\Shelter;
use App\Models\Species;
use App\Models\User;
use App\Models\Wing;
use Livewire\Livewire;

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
    $response->assertSee(['Dashboard', 'Pets', 'Facilities']);
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

test('staff and managers do not see the administration navigation links', function () {
    $staff = User::factory()->create(['role' => 'staff']);
    $this->actingAs($staff);

    $response = $this->get(route('dashboard'));
    $response->assertOk();
    $response->assertDontSee(['Shelters', 'Species', 'Breeds', 'Fur Types', 'Vaccines', 'Sicknesses']);

    $manager = User::factory()->create(['role' => 'manager']);
    $this->actingAs($manager);

    $response = $this->get(route('dashboard'));
    $response->assertOk();
    $response->assertDontSee(['Shelters', 'Species', 'Breeds', 'Fur Types', 'Vaccines', 'Sicknesses']);
});

test('admins do not see the pets and facilities navigation links', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    $response = $this->get(route('dashboard'));

    $response->assertOk();
    $response->assertDontSee(['>Pets<', '>Facilities<'], false);
});

test('only admins see the administration navigation links', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    $response = $this->get(route('dashboard'));

    $response->assertOk();
    $response->assertSee(['Shelters', 'Species', 'Breeds', 'Fur Types', 'Vaccines', 'Sicknesses']);
    $response->assertSee('Users');
});

test('shows accurate active pets, quarantine, capacity, and staff counts for the current shelter', function () {
    $shelter = Shelter::factory()->create();
    $user = User::factory()->create(['shelter_id' => $shelter->id, 'role' => 'staff']);
    $this->actingAs($user);

    User::factory()->count(3)->create(['shelter_id' => $shelter->id, 'role' => 'staff']);

    $facility = Facility::factory()->create(['shelter_id' => $shelter->id]);
    $wing = Wing::factory()->create(['facility_id' => $facility->id]);
    Cage::factory()->create(['wing_id' => $wing->id, 'capacity' => 3]);
    Cage::factory()->create(['wing_id' => $wing->id, 'capacity' => 2]);

    Pet::factory()->count(2)->create(['shelter_id' => $shelter->id, 'status' => 'available']);
    Pet::factory()->create(['shelter_id' => $shelter->id, 'status' => 'quarantine']);
    Pet::factory()->create(['shelter_id' => $shelter->id, 'status' => 'adopted']);
    Pet::factory()->create(['shelter_id' => $shelter->id, 'status' => 'medical', 'date_of_death' => now()->subDay()]);

    Livewire::test(Dashboard::class)
        ->assertSet('activePetsCount', 3)
        ->assertSet('quarantinedPetsCount', 1)
        ->assertSet('availableCapacity', 2)
        ->assertSet('staffCount', 4);
});

test('excludes other shelters pets, cages, and staff from the statistics', function () {
    $shelter = Shelter::factory()->create();
    $otherShelter = Shelter::factory()->create();

    $user = User::factory()->create(['shelter_id' => $shelter->id, 'role' => 'staff']);
    $this->actingAs($user);

    $facility = Facility::factory()->create(['shelter_id' => $shelter->id]);
    $wing = Wing::factory()->create(['facility_id' => $facility->id]);
    Cage::factory()->create(['wing_id' => $wing->id, 'capacity' => 5]);
    Pet::factory()->create(['shelter_id' => $shelter->id, 'status' => 'available']);

    $otherFacility = Facility::factory()->create(['shelter_id' => $otherShelter->id]);
    $otherWing = Wing::factory()->create(['facility_id' => $otherFacility->id]);
    Cage::factory()->create(['wing_id' => $otherWing->id, 'capacity' => 10]);
    Pet::factory()->count(2)->create(['shelter_id' => $otherShelter->id, 'status' => 'quarantine']);
    User::factory()->count(2)->create(['shelter_id' => $otherShelter->id, 'role' => 'staff']);

    Livewire::test(Dashboard::class)
        ->assertSet('activePetsCount', 1)
        ->assertSet('quarantinedPetsCount', 0)
        ->assertSet('availableCapacity', 4)
        ->assertSet('staffCount', 1);
});

test('lists the five most recently added pets with their status and cage code', function () {
    $shelter = Shelter::factory()->create();
    $user = User::factory()->create(['shelter_id' => $shelter->id, 'role' => 'staff']);
    $this->actingAs($user);

    $facility = Facility::factory()->create(['shelter_id' => $shelter->id]);
    $wing = Wing::factory()->create(['facility_id' => $facility->id]);
    $cage = Cage::factory()->create(['wing_id' => $wing->id, 'code' => 'C-01']);

    Pet::factory()->create([
        'shelter_id' => $shelter->id,
        'name' => 'Oldest Pet',
        'status' => 'available',
        'created_at' => now()->subDays(10),
    ]);

    Pet::factory()->count(4)->sequence(
        ['name' => 'Pet A', 'status' => 'available', 'created_at' => now()->subDays(4)],
        ['name' => 'Pet B', 'status' => 'quarantine', 'created_at' => now()->subDays(3)],
        ['name' => 'Pet C', 'status' => 'adopted', 'created_at' => now()->subDays(2)],
        ['name' => 'Pet D', 'status' => 'medical', 'created_at' => now()->subDays(1)],
    )->create(['shelter_id' => $shelter->id, 'cage_id' => $cage->id]);

    Pet::factory()->create([
        'shelter_id' => $shelter->id,
        'name' => 'Newest Pet',
        'status' => 'available',
        'created_at' => now(),
    ]);

    $response = $this->get(route('dashboard'));

    $response->assertOk();
    $response->assertSee(['Pet A', 'Pet B', 'Pet C', 'Pet D', 'Newest Pet']);
    $response->assertDontSee('Oldest Pet');
    $response->assertSee('C-01');
    $response->assertSee(__('No Cage Assigned'));
});

test('the pets sidebar only lists species enabled for the current shelter', function () {
    $shelter = Shelter::factory()->create();
    $user = User::factory()->create(['shelter_id' => $shelter->id, 'role' => 'staff']);
    $this->actingAs($user);

    $dog = Species::factory()->create(['name' => 'Dog', 'name_plural' => 'Dogs']);
    $cat = Species::factory()->create(['name' => 'Cat', 'name_plural' => 'Cats']);
    $shelter->species()->attach($dog->id);

    $response = $this->get(route('dashboard'));

    $response->assertOk();
    $response->assertSee('Dogs');
    $response->assertDontSee('Cats');
});

test('the pets sidebar is empty when the shelter has no species enabled', function () {
    $shelter = Shelter::factory()->create();
    $user = User::factory()->create(['shelter_id' => $shelter->id, 'role' => 'staff']);
    $this->actingAs($user);

    Species::factory()->create(['name' => 'Dog', 'name_plural' => 'Dogs']);

    $response = $this->get(route('dashboard'));

    $response->assertOk();
    $response->assertDontSee('Dogs');
});

test('shows a placeholder message when there are no recent intakes', function () {
    $shelter = Shelter::factory()->create();
    $user = User::factory()->create(['shelter_id' => $shelter->id, 'role' => 'staff']);
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));

    $response->assertOk();
    $response->assertSee(__('No Recent Intakes'));
});
