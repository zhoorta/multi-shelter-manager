<?php

use App\Livewire\Dashboard;
use App\Models\Adoption;
use App\Models\Breed;
use App\Models\Cage;
use App\Models\Facility;
use App\Models\Pet;
use App\Models\Shelter;
use App\Models\Species;
use App\Models\Sponsorship;
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

test('the layout renders the decorative backdrop behind the content area', function () {
    $this->actingAs(User::factory()->create());

    $this->get(route('dashboard'))
        ->assertOk()
        ->assertSee('data-test="app-backdrop"', false);
});

test('the layout shows the navigation links and the user shelter name', function () {
    $shelter = Shelter::factory()->create(['name' => 'Happy Paws Shelter']);
    $user = User::factory()->forShelter($shelter, 'staff')->create();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));

    $response->assertOk();
    $response->assertSee(['Dashboard', 'Pets', 'Facilities']);
    $response->assertSee('Happy Paws Shelter');
    $response->assertSee($user->name);
    $response->assertDontSee('Users');
});

test('only managers and admins see the users navigation link', function () {
    $shelter = Shelter::factory()->create();
    $manager = User::factory()->forShelter($shelter, 'manager')->create();
    $this->actingAs($manager);

    $response = $this->get(route('dashboard'));

    $response->assertOk();
    $response->assertSee('Users');
});

test('staff and managers do not see the administration navigation links', function () {
    $staff = User::factory()->create();
    $this->actingAs($staff);

    $response = $this->get(route('dashboard'));
    $response->assertOk();
    $response->assertDontSee(['Shelters', 'Species', 'Breeds', 'Fur Types', 'Vaccines', 'Sicknesses']);

    $shelter = Shelter::factory()->create();
    $manager = User::factory()->forShelter($shelter, 'manager')->create();
    $this->actingAs($manager);

    $response = $this->get(route('dashboard'));
    $response->assertOk();
    $response->assertDontSee(['Shelters', 'Species', 'Breeds', 'Fur Types', 'Vaccines', 'Sicknesses']);
});

test('admins do not see the pets and facilities navigation links', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $response = $this->get(route('dashboard'));

    $response->assertOk();
    $response->assertDontSee(['>Pets<', '>Facilities<'], false);
});

test('only admins see the administration navigation links', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $response = $this->get(route('dashboard'));

    $response->assertOk();
    $response->assertSee(['Shelters', 'Species', 'Breeds', 'Fur Types', 'Vaccines', 'Sicknesses']);
    $response->assertSee('Users');
});

test('shows accurate active pets, adoptions, capacity, and staff counts for the current shelter', function () {
    $shelter = Shelter::factory()->create();
    $user = User::factory()->forShelter($shelter, 'staff')->create();
    $this->actingAs($user);

    User::factory()->count(3)->forShelter($shelter, 'staff')->create();

    $facility = Facility::factory()->create(['shelter_id' => $shelter->id]);
    $wing = Wing::factory()->create(['facility_id' => $facility->id]);
    Cage::factory()->create(['wing_id' => $wing->id, 'capacity' => 3]);
    Cage::factory()->create(['wing_id' => $wing->id, 'capacity' => 2]);

    Pet::factory()->count(2)->create(['shelter_id' => $shelter->id, 'status' => 'available']);
    Pet::factory()->create(['shelter_id' => $shelter->id, 'status' => 'not_available']);
    Pet::factory()->count(2)->create(['shelter_id' => $shelter->id, 'status' => 'adopted']);
    Pet::factory()->create(['shelter_id' => $shelter->id, 'status' => 'available', 'date_of_death' => now()->subDay()]);

    Livewire::test(Dashboard::class)
        ->assertSet('activePetsCount', 3)
        ->assertSet('adoptionsPetsCount', 2)
        ->assertSet('availableCapacity', 2)
        ->assertSet('staffCount', 4);
});

test('excludes other shelters pets, cages, and staff from the statistics', function () {
    $shelter = Shelter::factory()->create();
    $otherShelter = Shelter::factory()->create();

    $user = User::factory()->forShelter($shelter, 'staff')->create();
    $this->actingAs($user);

    $facility = Facility::factory()->create(['shelter_id' => $shelter->id]);
    $wing = Wing::factory()->create(['facility_id' => $facility->id]);
    Cage::factory()->create(['wing_id' => $wing->id, 'capacity' => 5]);
    Pet::factory()->create(['shelter_id' => $shelter->id, 'status' => 'available']);

    $otherFacility = Facility::factory()->create(['shelter_id' => $otherShelter->id]);
    $otherWing = Wing::factory()->create(['facility_id' => $otherFacility->id]);
    Cage::factory()->create(['wing_id' => $otherWing->id, 'capacity' => 10]);
    Pet::factory()->count(2)->create(['shelter_id' => $otherShelter->id, 'status' => 'adopted']);
    User::factory()->count(2)->forShelter($otherShelter, 'staff')->create();

    Livewire::test(Dashboard::class)
        ->assertSet('activePetsCount', 1)
        ->assertSet('adoptionsPetsCount', 0)
        ->assertSet('availableCapacity', 4)
        ->assertSet('staffCount', 1);
});

test('lists the five most recent intakes with their species, ref, and a link to the pet', function () {
    $shelter = Shelter::factory()->create();
    $user = User::factory()->forShelter($shelter, 'staff')->create();
    $this->actingAs($user);

    $species = Species::factory()->create(['name' => 'Dog']);

    $facility = Facility::factory()->create(['shelter_id' => $shelter->id]);
    $wing = Wing::factory()->create(['facility_id' => $facility->id]);
    $cage = Cage::factory()->create(['wing_id' => $wing->id]);

    Pet::factory()->create([
        'shelter_id' => $shelter->id,
        'species_id' => $species->id,
        'cage_id' => $cage->id,
        'name' => 'Oldest Pet',
        'checkin_date' => now()->subDays(10),
    ]);

    Pet::factory()->count(4)->sequence(
        ['name' => 'Pet A', 'checkin_date' => now()->subDays(4)],
        ['name' => 'Pet B', 'checkin_date' => now()->subDays(3)],
        ['name' => 'Pet C', 'checkin_date' => now()->subDays(2)],
        ['name' => 'Pet D', 'checkin_date' => now()->subDays(1)],
    )->create(['shelter_id' => $shelter->id, 'species_id' => $species->id, 'cage_id' => $cage->id]);

    $newest = Pet::factory()->create([
        'shelter_id' => $shelter->id,
        'species_id' => $species->id,
        'cage_id' => $cage->id,
        'name' => 'Newest Pet',
        'checkin_date' => now(),
    ]);

    $response = $this->get(route('dashboard'));

    $response->assertOk();
    $response->assertSee(['Pet A', 'Pet B', 'Pet C', 'Pet D', 'Newest Pet']);
    $response->assertDontSee('Oldest Pet');
    $response->assertSee('Dog - '.$newest->ref);
    $response->assertSee(route('pets.show', $newest));
});

test('breaks recent intakes ties on the same check-in date by created_at desc', function () {
    $shelter = Shelter::factory()->create();
    $user = User::factory()->forShelter($shelter, 'staff')->create();
    $this->actingAs($user);

    $checkinDate = now()->subDay();

    $earlierCreated = Pet::factory()->create([
        'shelter_id' => $shelter->id,
        'name' => 'Earlier Created Pet',
        'checkin_date' => $checkinDate,
        'created_at' => now()->subHours(2),
    ]);

    $laterCreated = Pet::factory()->create([
        'shelter_id' => $shelter->id,
        'name' => 'Later Created Pet',
        'checkin_date' => $checkinDate,
        'created_at' => now()->subHour(),
    ]);

    $response = $this->get(route('dashboard'));

    $response->assertOk();
    $response->assertSeeInOrder([$laterCreated->name, $earlierCreated->name]);
});

test('excludes pets without a check-in date from recent intakes', function () {
    $shelter = Shelter::factory()->create();
    $user = User::factory()->forShelter($shelter, 'staff')->create();
    $this->actingAs($user);

    $facility = Facility::factory()->create(['shelter_id' => $shelter->id]);
    $wing = Wing::factory()->create(['facility_id' => $facility->id]);
    $cage = Cage::factory()->create(['wing_id' => $wing->id]);

    Pet::factory()->create([
        'shelter_id' => $shelter->id,
        'cage_id' => $cage->id,
        'name' => 'No Checkin Pet',
        'checkin_date' => null,
    ]);

    $response = $this->get(route('dashboard'));

    $response->assertOk();
    $response->assertDontSee('No Checkin Pet');
    $response->assertSee(__('No Recent Intakes'));
});

test('lists the five most recent adoptions with their species, ref, and a link to the pet', function () {
    $shelter = Shelter::factory()->create();
    $user = User::factory()->forShelter($shelter, 'staff')->create();
    $this->actingAs($user);

    $species = Species::factory()->create(['name' => 'Cat']);

    $oldestPet = Pet::factory()->create(['shelter_id' => $shelter->id, 'species_id' => $species->id, 'name' => 'Oldest Adoption', 'status' => 'adopted']);
    Adoption::factory()->create(['pet_id' => $oldestPet->id, 'adoption_date' => now()->subDays(10)]);

    Pet::factory()->count(4)->sequence(
        ['name' => 'Pet A'],
        ['name' => 'Pet B'],
        ['name' => 'Pet C'],
        ['name' => 'Pet D'],
    )->create(['shelter_id' => $shelter->id, 'species_id' => $species->id, 'status' => 'adopted'])
        ->each(function (Pet $pet, int $index): void {
            Adoption::factory()->create(['pet_id' => $pet->id, 'adoption_date' => now()->subDays(4 - $index)]);
        });

    $newestPet = Pet::factory()->create(['shelter_id' => $shelter->id, 'species_id' => $species->id, 'name' => 'Newest Adoption', 'status' => 'adopted']);
    Adoption::factory()->create(['pet_id' => $newestPet->id, 'adoption_date' => now()]);

    $response = $this->get(route('dashboard'));

    $response->assertOk();
    $response->assertSee(['Pet A', 'Pet B', 'Pet C', 'Pet D', 'Newest Adoption']);
    $response->assertDontSee('Oldest Adoption');
    $response->assertSee('Cat - '.$newestPet->ref);
    $response->assertSee(route('pets.show', $newestPet));
});

test('breaks recent adoptions ties on the same adoption date by created_at desc', function () {
    $shelter = Shelter::factory()->create();
    $user = User::factory()->forShelter($shelter, 'staff')->create();
    $this->actingAs($user);

    $adoptionDate = now()->subDay();

    $earlierPet = Pet::factory()->create(['shelter_id' => $shelter->id, 'name' => 'Earlier Created Adoption', 'status' => 'adopted']);
    Adoption::factory()->create(['pet_id' => $earlierPet->id, 'adoption_date' => $adoptionDate, 'created_at' => now()->subHours(2)]);

    $laterPet = Pet::factory()->create(['shelter_id' => $shelter->id, 'name' => 'Later Created Adoption', 'status' => 'adopted']);
    Adoption::factory()->create(['pet_id' => $laterPet->id, 'adoption_date' => $adoptionDate, 'created_at' => now()->subHour()]);

    $response = $this->get(route('dashboard'));

    $response->assertOk();
    $response->assertSeeInOrder([$laterPet->name, $earlierPet->name]);
});

test('excludes other shelters adoptions from recent adoptions', function () {
    $shelter = Shelter::factory()->create();
    $otherShelter = Shelter::factory()->create();
    $user = User::factory()->forShelter($shelter, 'staff')->create();
    $this->actingAs($user);

    $otherPet = Pet::factory()->create(['shelter_id' => $otherShelter->id, 'name' => 'Other Shelter Pet', 'status' => 'adopted']);
    Adoption::factory()->create(['pet_id' => $otherPet->id]);

    $response = $this->get(route('dashboard'));

    $response->assertOk();
    $response->assertDontSee('Other Shelter Pet');
    $response->assertSee(__('No Recent Adoptions'));
});

test('excludes adoptions whose pet is not currently adopted from recent adoptions', function () {
    $shelter = Shelter::factory()->create();
    $user = User::factory()->forShelter($shelter, 'staff')->create();
    $this->actingAs($user);

    $facility = Facility::factory()->create(['shelter_id' => $shelter->id]);
    $wing = Wing::factory()->create(['facility_id' => $facility->id]);
    $cage = Cage::factory()->create(['wing_id' => $wing->id]);

    $returnedPet = Pet::factory()->create(['shelter_id' => $shelter->id, 'name' => 'Returned Pet', 'status' => 'available', 'cage_id' => $cage->id]);
    Adoption::factory()->create(['pet_id' => $returnedPet->id, 'return_date' => now()]);

    $response = $this->get(route('dashboard'));

    $response->assertOk();
    $response->assertDontSee('Returned Pet');
    $response->assertSee(__('No Recent Adoptions'));
});

test('lists active pets with no cage assigned, up to five, most recent first', function () {
    $shelter = Shelter::factory()->create();
    $user = User::factory()->forShelter($shelter, 'staff')->create();
    $this->actingAs($user);

    $species = Species::factory()->create(['name' => 'Rabbit']);

    $facility = Facility::factory()->create(['shelter_id' => $shelter->id]);
    $wing = Wing::factory()->create(['facility_id' => $facility->id]);
    $cage = Cage::factory()->create(['wing_id' => $wing->id]);

    $hasCage = Pet::factory()->create(['shelter_id' => $shelter->id, 'species_id' => $species->id, 'name' => 'Has A Cage', 'cage_id' => $cage->id]);
    $adopted = Pet::factory()->create(['shelter_id' => $shelter->id, 'species_id' => $species->id, 'name' => 'Adopted Without Cage', 'status' => 'adopted', 'cage_id' => null]);
    $deceased = Pet::factory()->create(['shelter_id' => $shelter->id, 'species_id' => $species->id, 'name' => 'Deceased Without Cage', 'date_of_death' => now(), 'cage_id' => null]);

    $olderPet = Pet::factory()->create([
        'shelter_id' => $shelter->id,
        'species_id' => $species->id,
        'name' => 'Older No Location Pet',
        'cage_id' => null,
        'created_at' => now()->subDay(),
    ]);

    $newerPet = Pet::factory()->create([
        'shelter_id' => $shelter->id,
        'species_id' => $species->id,
        'name' => 'Newer No Location Pet',
        'cage_id' => null,
        'created_at' => now(),
    ]);

    $response = $this->get(route('dashboard'));

    $response->assertOk();
    $response->assertSeeInOrder([$newerPet->name, $olderPet->name]);
    $response->assertSee('Rabbit - '.$newerPet->ref);
    $response->assertSee(route('pets.show', $newerPet));

    $unknownLocationIds = Livewire::test(Dashboard::class)->get('unknownLocationPets')->pluck('id');
    expect($unknownLocationIds)
        ->toContain($olderPet->id, $newerPet->id)
        ->not->toContain($hasCage->id, $adopted->id, $deceased->id);
});

test('excludes other shelters pets from pets with unknown location', function () {
    $shelter = Shelter::factory()->create();
    $otherShelter = Shelter::factory()->create();
    $user = User::factory()->forShelter($shelter, 'staff')->create();
    $this->actingAs($user);

    Pet::factory()->create(['shelter_id' => $otherShelter->id, 'name' => 'Other Shelter No Location Pet', 'cage_id' => null]);

    $response = $this->get(route('dashboard'));

    $response->assertOk();
    $response->assertDontSee('Other Shelter No Location Pet');
    $response->assertSee(__('No Pets with Unknown Location'));
});

test('lists the five most recent passings with their species, ref, and a link to the pet', function () {
    $shelter = Shelter::factory()->create();
    $user = User::factory()->forShelter($shelter, 'staff')->create();
    $this->actingAs($user);

    $species = Species::factory()->create(['name' => 'Cat']);

    Pet::factory()->create([
        'shelter_id' => $shelter->id,
        'species_id' => $species->id,
        'name' => 'Oldest Passing',
        'date_of_death' => now()->subDays(10),
    ]);

    Pet::factory()->count(4)->sequence(
        ['name' => 'Pet A', 'date_of_death' => now()->subDays(4)],
        ['name' => 'Pet B', 'date_of_death' => now()->subDays(3)],
        ['name' => 'Pet C', 'date_of_death' => now()->subDays(2)],
        ['name' => 'Pet D', 'date_of_death' => now()->subDays(1)],
    )->create(['shelter_id' => $shelter->id, 'species_id' => $species->id]);

    $newest = Pet::factory()->create([
        'shelter_id' => $shelter->id,
        'species_id' => $species->id,
        'name' => 'Newest Passing',
        'date_of_death' => now(),
    ]);

    $response = $this->get(route('dashboard'));

    $response->assertOk();
    $response->assertSee(['Pet A', 'Pet B', 'Pet C', 'Pet D', 'Newest Passing']);
    $response->assertDontSee('Oldest Passing');
    $response->assertSee('Cat - '.$newest->ref);
    $response->assertSee(route('pets.show', $newest));
});

test('breaks recent passings ties on the same date of death by created_at desc', function () {
    $shelter = Shelter::factory()->create();
    $user = User::factory()->forShelter($shelter, 'staff')->create();
    $this->actingAs($user);

    $dateOfDeath = now()->subDay();

    $earlierCreated = Pet::factory()->create([
        'shelter_id' => $shelter->id,
        'name' => 'Earlier Created Passing',
        'date_of_death' => $dateOfDeath,
        'created_at' => now()->subHours(2),
    ]);

    $laterCreated = Pet::factory()->create([
        'shelter_id' => $shelter->id,
        'name' => 'Later Created Passing',
        'date_of_death' => $dateOfDeath,
        'created_at' => now()->subHour(),
    ]);

    $response = $this->get(route('dashboard'));

    $response->assertOk();
    $response->assertSeeInOrder([$laterCreated->name, $earlierCreated->name]);
});

test('excludes pets without a date of death from recent passings', function () {
    $shelter = Shelter::factory()->create();
    $user = User::factory()->forShelter($shelter, 'staff')->create();
    $this->actingAs($user);

    $facility = Facility::factory()->create(['shelter_id' => $shelter->id]);
    $wing = Wing::factory()->create(['facility_id' => $facility->id]);
    $cage = Cage::factory()->create(['wing_id' => $wing->id]);

    Pet::factory()->create(['shelter_id' => $shelter->id, 'name' => 'Alive Pet', 'date_of_death' => null, 'cage_id' => $cage->id]);

    $response = $this->get(route('dashboard'));

    $response->assertOk();
    $response->assertDontSee('Alive Pet');
    $response->assertSee(__('No Recent Passings'));
});

test('excludes other shelters pets from recent passings', function () {
    $shelter = Shelter::factory()->create();
    $otherShelter = Shelter::factory()->create();
    $user = User::factory()->forShelter($shelter, 'staff')->create();
    $this->actingAs($user);

    Pet::factory()->create(['shelter_id' => $otherShelter->id, 'name' => 'Other Shelter Passing', 'date_of_death' => now()]);

    $response = $this->get(route('dashboard'));

    $response->assertOk();
    $response->assertDontSee('Other Shelter Passing');
    $response->assertSee(__('No Recent Passings'));
});

test('lists the five most recent sponsorships with their species, ref, and a link to the pet', function () {
    $shelter = Shelter::factory()->create();
    $user = User::factory()->forShelter($shelter, 'staff')->create();
    $this->actingAs($user);

    $species = Species::factory()->create(['name' => 'Parrot']);

    $facility = Facility::factory()->create(['shelter_id' => $shelter->id]);
    $wing = Wing::factory()->create(['facility_id' => $facility->id]);
    $cage = Cage::factory()->create(['wing_id' => $wing->id]);

    $oldestPet = Pet::factory()->create(['shelter_id' => $shelter->id, 'species_id' => $species->id, 'cage_id' => $cage->id, 'name' => 'Oldest Sponsorship']);
    Sponsorship::factory()->create(['pet_id' => $oldestPet->id, 'created_at' => now()->subDays(10)]);

    Pet::factory()->count(4)->sequence(
        ['name' => 'Pet A'],
        ['name' => 'Pet B'],
        ['name' => 'Pet C'],
        ['name' => 'Pet D'],
    )->create(['shelter_id' => $shelter->id, 'species_id' => $species->id, 'cage_id' => $cage->id])
        ->each(function (Pet $pet, int $index): void {
            Sponsorship::factory()->create(['pet_id' => $pet->id, 'created_at' => now()->subDays(4 - $index)]);
        });

    $newestPet = Pet::factory()->create(['shelter_id' => $shelter->id, 'species_id' => $species->id, 'cage_id' => $cage->id, 'name' => 'Newest Sponsorship']);
    Sponsorship::factory()->create(['pet_id' => $newestPet->id, 'created_at' => now()]);

    $response = $this->get(route('dashboard'));

    $response->assertOk();
    $response->assertSee(['Pet A', 'Pet B', 'Pet C', 'Pet D', 'Newest Sponsorship']);
    $response->assertDontSee('Oldest Sponsorship');
    $response->assertSee('Parrot - '.$newestPet->ref);
    $response->assertSee(route('pets.show', $newestPet));
});

test('excludes other shelters sponsorships from recent sponsorships', function () {
    $shelter = Shelter::factory()->create();
    $otherShelter = Shelter::factory()->create();
    $user = User::factory()->forShelter($shelter, 'staff')->create();
    $this->actingAs($user);

    $facility = Facility::factory()->create(['shelter_id' => $otherShelter->id]);
    $wing = Wing::factory()->create(['facility_id' => $facility->id]);
    $cage = Cage::factory()->create(['wing_id' => $wing->id]);

    $otherPet = Pet::factory()->create(['shelter_id' => $otherShelter->id, 'name' => 'Other Shelter Sponsorship Pet', 'cage_id' => $cage->id]);
    Sponsorship::factory()->create(['pet_id' => $otherPet->id]);

    $response = $this->get(route('dashboard'));

    $response->assertOk();
    $response->assertDontSee('Other Shelter Sponsorship Pet');
    $response->assertSee(__('No Recent Sponsorships'));
});

test('the pets sidebar only lists species enabled for the current shelter', function () {
    $shelter = Shelter::factory()->create();
    $user = User::factory()->forShelter($shelter, 'staff')->create();
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
    $user = User::factory()->forShelter($shelter, 'staff')->create();
    $this->actingAs($user);

    Species::factory()->create(['name' => 'Dog', 'name_plural' => 'Dogs']);

    $response = $this->get(route('dashboard'));

    $response->assertOk();
    $response->assertDontSee('Dogs');
});

test('shows a placeholder message when there are no recent intakes', function () {
    $shelter = Shelter::factory()->create();
    $user = User::factory()->forShelter($shelter, 'staff')->create();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));

    $response->assertOk();
    $response->assertSee(__('No Recent Intakes'));
});

test('managers see a warning linking to facilities when the shelter has no facilities defined', function () {
    $shelter = Shelter::factory()->create();
    $user = User::factory()->forShelter($shelter, 'manager')->create();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));

    $response->assertOk();
    $response->assertSee(__('No facilities defined. Please configure the Facilities, Wings and Cages on the Facilities option.'));
    $response->assertDontSee(__('No facilities defined. Contact the shelter manager to configure the Facilities, Wings and Cages.'));
});

test('staff are told to contact the manager when the shelter has no facilities defined', function () {
    $shelter = Shelter::factory()->create();
    $user = User::factory()->forShelter($shelter, 'staff')->create();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));

    $response->assertOk();
    $response->assertSee(__('No facilities defined. Contact the shelter manager to configure the Facilities, Wings and Cages.'));
    $response->assertDontSee(__('No facilities defined. Please configure the Facilities, Wings and Cages on the Facilities option.'));
});

test('shelter users still see the facilities warning when a facility and wing exist but no cage does', function () {
    $shelter = Shelter::factory()->create();
    $user = User::factory()->forShelter($shelter, 'staff')->create();
    $this->actingAs($user);

    $facility = Facility::factory()->create(['shelter_id' => $shelter->id]);
    Wing::factory()->create(['facility_id' => $facility->id]);

    $response = $this->get(route('dashboard'));

    $response->assertOk();
    $response->assertSee(__('No facilities defined. Contact the shelter manager to configure the Facilities, Wings and Cages.'));
});

test('shelter users do not see the facilities warning once a cage exists', function () {
    $shelter = Shelter::factory()->create();
    $user = User::factory()->forShelter($shelter, 'staff')->create();
    $this->actingAs($user);

    $facility = Facility::factory()->create(['shelter_id' => $shelter->id]);
    $wing = Wing::factory()->create(['facility_id' => $facility->id]);
    Cage::factory()->create(['wing_id' => $wing->id]);

    $response = $this->get(route('dashboard'));

    $response->assertOk();
    $response->assertDontSee(__('No facilities defined. Contact the shelter manager to configure the Facilities, Wings and Cages.'));
});

test('shelter users see a warning when the shelter has no species configured', function () {
    $shelter = Shelter::factory()->create();
    $user = User::factory()->forShelter($shelter, 'staff')->create();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));

    $response->assertOk();
    $response->assertSee(__('No pet species defined for the shelter. Please contact the site administrator to configure the shelter species.'));
});

test('shelter users do not see the species warning once the shelter has a species configured', function () {
    $shelter = Shelter::factory()->create();
    $user = User::factory()->forShelter($shelter, 'staff')->create();
    $this->actingAs($user);

    $species = Species::factory()->create();
    $shelter->species()->attach($species);

    $response = $this->get(route('dashboard'));

    $response->assertOk();
    $response->assertDontSee(__('No pet species defined for the shelter. Please contact the site administrator to configure the shelter species.'));
});

test('admins do not see the facilities or species warnings', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $response = $this->get(route('dashboard'));

    $response->assertOk();
    $response->assertDontSee(__('No facilities defined. Please configure the Facilities, Wings and Cages on the Facilities option.'));
    $response->assertDontSee(__('No facilities defined. Contact the shelter manager to configure the Facilities, Wings and Cages.'));
    $response->assertDontSee(__('No pet species defined for the shelter. Please contact the site administrator to configure the shelter species.'));
});

test('shelter users see a warning for a shelter species that has no breeds', function () {
    $shelter = Shelter::factory()->create();
    $user = User::factory()->forShelter($shelter, 'staff')->create();
    $this->actingAs($user);

    $species = Species::factory()->create(['name' => 'Dog']);
    $shelter->species()->attach($species);

    $response = $this->get(route('dashboard'));

    $response->assertOk();
    $response->assertSee(__('No breeds of :species exist. Please contact the site administrator to configure the breeds.', ['species' => 'Dog']));
});

test('shelter users do not see the missing breeds warning once the species has a breed', function () {
    $shelter = Shelter::factory()->create();
    $user = User::factory()->forShelter($shelter, 'staff')->create();
    $this->actingAs($user);

    $species = Species::factory()->create(['name' => 'Dog']);
    $shelter->species()->attach($species);
    Breed::factory()->create(['species_id' => $species->id]);

    $response = $this->get(route('dashboard'));

    $response->assertOk();
    $response->assertDontSee(__('No breeds of :species exist. Please contact the site administrator to configure the breeds.', ['species' => 'Dog']));
});

test('shelter users do not see the missing breeds warning for species not enabled for their shelter', function () {
    $shelter = Shelter::factory()->create();
    $user = User::factory()->forShelter($shelter, 'staff')->create();
    $this->actingAs($user);

    Species::factory()->create(['name' => 'Cat']);

    $response = $this->get(route('dashboard'));

    $response->assertOk();
    $response->assertDontSee(__('No breeds of :species exist. Please contact the site administrator to configure the breeds.', ['species' => 'Cat']));
});

test('admins do not see the missing breeds warning', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $species = Species::factory()->create(['name' => 'Dog']);

    $response = $this->get(route('dashboard'));

    $response->assertOk();
    $response->assertDontSee(__('No breeds of :species exist. Please contact the site administrator to configure the breeds.', ['species' => 'Dog']));
});

test('viewers do not see the navigation links to people\'s personal data', function () {
    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->forShelter($shelter, 'viewer')->create());

    $this->get(route('dashboard'))
        ->assertOk()
        ->assertDontSee(route('pets.sponsorships.index'))
        ->assertDontSee(route('pets.adoptions.index'))
        ->assertDontSee(route('volunteers.index'))
        ->assertDontSee(route('members.index'))
        ->assertSee(route('pets.vaccinations.index'));
});
