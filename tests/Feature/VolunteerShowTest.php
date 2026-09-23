<?php

use App\Models\Activity;
use App\Models\Shelter;
use App\Models\Species;
use App\Models\User;
use App\Models\Volunteer;

test('guests are redirected to the login page', function () {
    $volunteer = Volunteer::factory()->create();

    $this->get(route('volunteers.show', $volunteer))->assertRedirect(route('login'));
});

test('admins are forbidden from viewing the page', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $volunteer = Volunteer::factory()->create();

    $this->get(route('volunteers.show', $volunteer))->assertForbidden();
});

test('managers and staff can view a volunteer belonging to their shelter', function () {
    $shelter = Shelter::factory()->create();
    $volunteer = Volunteer::factory()->for($shelter)->create(['name' => 'Maria Silva']);

    $manager = User::factory()->forShelter($shelter, 'manager')->create();
    $this->actingAs($manager);
    $this->get(route('volunteers.show', $volunteer))->assertOk()->assertSee('Maria Silva');

    $staff = User::factory()->forShelter($shelter, 'staff')->create();
    $this->actingAs($staff);
    $this->get(route('volunteers.show', $volunteer))->assertOk()->assertSee('Maria Silva');
});

test('only managers see the edit link', function () {
    $shelter = Shelter::factory()->create();
    $volunteer = Volunteer::factory()->for($shelter)->create();

    $manager = User::factory()->forShelter($shelter, 'manager')->create();
    $this->actingAs($manager);
    $this->get(route('volunteers.show', $volunteer))
        ->assertSee(route('volunteers.edit', $volunteer), false);

    $staff = User::factory()->forShelter($shelter, 'staff')->create();
    $this->actingAs($staff);
    $this->get(route('volunteers.show', $volunteer))
        ->assertDontSee(route('volunteers.edit', $volunteer), false);
});

test('shows only the activities assigned to the volunteer', function () {
    $shelter = Shelter::factory()->create();
    $volunteer = Volunteer::factory()->for($shelter)->create();

    $assignedActivity = Activity::factory()->create(['name' => 'Dog Walking']);
    $otherActivity = Activity::factory()->create(['name' => 'Cat Grooming']);
    $volunteer->activities()->attach($assignedActivity);

    $this->actingAs(User::factory()->forShelter($shelter, 'manager')->create());

    $this->get(route('volunteers.show', $volunteer))
        ->assertOk()
        ->assertSeeText('Dog Walking')
        ->assertDontSeeText('Cat Grooming');
});

test('shows only the species (sections) assigned to the volunteer', function () {
    $shelter = Shelter::factory()->create();
    $volunteer = Volunteer::factory()->for($shelter)->create();

    $assignedSpecies = Species::factory()->create(['name' => 'Dog', 'name_plural' => 'Dogs']);
    $otherSpecies = Species::factory()->create(['name' => 'Cat', 'name_plural' => 'Cats']);
    $volunteer->species()->attach($assignedSpecies);

    $this->actingAs(User::factory()->forShelter($shelter, 'manager')->create());

    $this->get(route('volunteers.show', $volunteer))
        ->assertOk()
        ->assertSeeText('Dogs')
        ->assertDontSeeText('Cats');
});

test('shows the volunteer\'s availability days with their periods and frequency', function () {
    $shelter = Shelter::factory()->create();
    $volunteer = Volunteer::factory()->for($shelter)->create();
    $volunteer->availabilities()->create(['day_index' => 0, 'mornings' => true, 'afternoons' => false, 'frequency' => 'weekly']);
    $volunteer->availabilities()->create(['day_index' => 3, 'mornings' => true, 'afternoons' => true, 'frequency' => 'biweekly']);

    $this->actingAs(User::factory()->forShelter($shelter, 'manager')->create());

    $this->get(route('volunteers.show', $volunteer))
        ->assertOk()
        ->assertSeeText('Present on Monday')
        ->assertSeeText('Present on Thursday')
        ->assertDontSeeText('Present on Tuesday');
});

test('shows a dash when the volunteer has no availability set', function () {
    $shelter = Shelter::factory()->create();
    $volunteer = Volunteer::factory()->for($shelter)->create();

    $this->actingAs(User::factory()->forShelter($shelter, 'manager')->create());

    $this->get(route('volunteers.show', $volunteer))
        ->assertOk()
        ->assertDontSeeText('Present on');
});

test('returns 404 when viewing a volunteer belonging to another shelter', function () {
    $otherShelter = Shelter::factory()->create();
    $volunteer = Volunteer::factory()->for($otherShelter)->create();

    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    $this->get(route('volunteers.show', $volunteer))->assertNotFound();
});
