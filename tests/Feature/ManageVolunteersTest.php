<?php

use App\Livewire\Volunteers\ManageVolunteers;
use App\Models\Activity;
use App\Models\Shelter;
use App\Models\Species;
use App\Models\User;
use App\Models\Volunteer;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Livewire;

test('guests are redirected to the login page', function () {
    $this->get(route('volunteers.index'))->assertRedirect(route('login'));
});

test('admins are forbidden from viewing the page', function () {
    $admin = User::factory()->create(['role' => 'admin', 'shelter_id' => null]);
    $this->actingAs($admin);

    $this->get(route('volunteers.index'))->assertForbidden();
});

test('managers and staff can view the page', function () {
    $shelter = Shelter::factory()->create();

    $manager = User::factory()->create(['role' => 'manager', 'shelter_id' => $shelter->id]);
    $this->actingAs($manager);
    $this->get(route('volunteers.index'))->assertOk();

    $staff = User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]);
    $this->actingAs($staff);
    $this->get(route('volunteers.index'))->assertOk();
});

test('shows a placeholder message when there are no volunteers', function () {
    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    $this->get(route('volunteers.index'))->assertSee(__('No volunteers registered'));
});

test('lists only volunteers belonging to the acting user\'s shelter', function () {
    $shelter = Shelter::factory()->create();
    $otherShelter = Shelter::factory()->create();

    Volunteer::factory()->for($shelter)->create(['name' => 'Maria Silva']);
    Volunteer::factory()->for($otherShelter)->create(['name' => 'Other Shelter Volunteer']);

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    Livewire::test(ManageVolunteers::class)
        ->assertSee('Maria Silva')
        ->assertDontSee('Other Shelter Volunteer');
});

test('lists a volunteer\'s activities, sector and availability alongside contact details', function () {
    $shelter = Shelter::factory()->create();
    $volunteer = Volunteer::factory()->for($shelter)->create([
        'name' => 'Maria Silva',
        'phone' => '912345678',
        'email' => 'maria@example.com',
    ]);

    $activity = Activity::factory()->create(['name' => 'Dog Walking']);
    $volunteer->activities()->attach($activity);

    $species = Species::factory()->create(['name' => 'Dog', 'name_plural' => 'Dogs']);
    $volunteer->species()->attach($species);

    $volunteer->availabilities()->create(['day_index' => 0, 'mornings' => true, 'afternoons' => false, 'frequency' => 'weekly']);

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    Livewire::test(ManageVolunteers::class)
        ->assertSeeText('Maria Silva')
        ->assertSeeText('912345678')
        ->assertSeeText('maria@example.com')
        ->assertSeeText('Dog Walking')
        ->assertSeeText('Dogs')
        ->assertSeeText('Monday morning weekly')
        ->assertDontSeeText('Present on Monday')
        ->assertDontSeeText('Activities:')
        ->assertDontSeeText('Availability:');
});

test('shows the end date only when the volunteer has one', function () {
    $shelter = Shelter::factory()->create();
    Volunteer::factory()->for($shelter)->create(['name' => 'Maria Silva', 'end_date' => '2026-01-15']);
    Volunteer::factory()->for($shelter)->create(['name' => 'Joao Costa', 'end_date' => null]);

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    Livewire::test(ManageVolunteers::class)
        ->assertSeeText(__('Ended at').' 15/01/2026');
});

test('only managers see the create and edit links', function () {
    $shelter = Shelter::factory()->create();
    $volunteer = Volunteer::factory()->for($shelter)->create();

    $manager = User::factory()->create(['role' => 'manager', 'shelter_id' => $shelter->id]);
    $this->actingAs($manager);
    $this->get(route('volunteers.index'))
        ->assertSee(route('volunteers.create'), false)
        ->assertSee(route('volunteers.edit', $volunteer), false);

    $staff = User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]);
    $this->actingAs($staff);
    $this->get(route('volunteers.index'))
        ->assertDontSee(route('volunteers.create'), false)
        ->assertDontSee(route('volunteers.edit', $volunteer), false);
});

test('staff cannot delete a volunteer', function () {
    $shelter = Shelter::factory()->create();
    $volunteer = Volunteer::factory()->for($shelter)->create();

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    Livewire::test(ManageVolunteers::class)
        ->call('deleteVolunteer', $volunteer->id)
        ->assertForbidden();

    expect($volunteer->fresh()->trashed())->toBeFalse();
});

test('manager can soft-delete a volunteer, stamping deleted_by', function () {
    $shelter = Shelter::factory()->create();
    $volunteer = Volunteer::factory()->for($shelter)->create(['name' => 'Maria Silva']);

    $manager = User::factory()->create(['role' => 'manager', 'shelter_id' => $shelter->id]);
    $this->actingAs($manager);

    Livewire::test(ManageVolunteers::class)
        ->call('deleteVolunteer', $volunteer->id)
        ->assertDontSee('Maria Silva');

    $volunteer->refresh();
    expect($volunteer->trashed())->toBeTrue();
    expect($volunteer->deleted_by)->toBe($manager->id);
});

test('manager cannot delete a volunteer belonging to another shelter', function () {
    $otherShelter = Shelter::factory()->create();
    $volunteer = Volunteer::factory()->for($otherShelter)->create();

    $shelter = Shelter::factory()->create();
    $manager = User::factory()->create(['role' => 'manager', 'shelter_id' => $shelter->id]);
    $this->actingAs($manager);

    expect(fn () => Livewire::test(ManageVolunteers::class)->call('deleteVolunteer', $volunteer->id))
        ->toThrow(ModelNotFoundException::class);

    expect($volunteer->fresh()->trashed())->toBeFalse();
});
