<?php

use App\Livewire\Pets\ManageVaccinations;
use App\Models\Pet;
use App\Models\Shelter;
use App\Models\User;
use App\Models\Vaccine;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Livewire;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('pets.vaccinations.index'));

    $response->assertRedirect(route('login'));
});

test('admins are forbidden from viewing the page', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $this->get(route('pets.vaccinations.index'))->assertForbidden();
});

test('managers and staff can view the page', function () {
    $shelter = Shelter::factory()->create();

    $manager = User::factory()->forShelter($shelter, 'manager')->create();
    $this->actingAs($manager);
    $this->get(route('pets.vaccinations.index'))->assertOk();

    $staff = User::factory()->forShelter($shelter, 'staff')->create();
    $this->actingAs($staff);
    $this->get(route('pets.vaccinations.index'))->assertOk();
});

test('shows a placeholder message when there are no vaccinations', function () {
    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    $this->get(route('pets.vaccinations.index'))->assertSee(__('No vaccinations registered'));
});

test('lists the vaccine, dates and the vaccinated pet', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create(['name' => 'Rex', 'ref' => 'PET00001']);
    $vaccine = Vaccine::factory()->create(['name' => 'Rabies']);
    $vaccine->species()->attach($pet->species_id);
    $pet->vaccines()->attach($vaccine, [
        'administered_date' => '2026-01-15',
        'due_date' => '2027-01-15',
    ]);

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    Livewire::test(ManageVaccinations::class)
        ->assertSee('Rabies')
        ->assertSee('15/01/2026')
        ->assertSee('15/01/2027')
        ->assertSee($pet->species->name)
        ->assertSee('PET00001')
        ->assertSee('Rex');
});

test('lists only vaccinations whose pet belongs to the acting user\'s shelter', function () {
    $shelter = Shelter::factory()->create();
    $otherShelter = Shelter::factory()->create();

    $pet = Pet::factory()->for($shelter)->create();
    $vaccine = Vaccine::factory()->create(['name' => 'Rabies']);
    $vaccine->species()->attach($pet->species_id);
    $pet->vaccines()->attach($vaccine, ['administered_date' => now()]);

    $otherPet = Pet::factory()->for($otherShelter)->create();
    $otherVaccine = Vaccine::factory()->create(['name' => 'Distemper']);
    $otherVaccine->species()->attach($otherPet->species_id);
    $otherPet->vaccines()->attach($otherVaccine, ['administered_date' => now()]);

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    Livewire::test(ManageVaccinations::class)
        ->assertSee('Rabies')
        ->assertDontSee('Distemper');
});

test('highlights an overdue due date in red when scheduled', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();
    $vaccine = Vaccine::factory()->create();
    $vaccine->species()->attach($pet->species_id);
    $pet->vaccines()->attach($vaccine, [
        'due_date' => today()->subDay(),
        'status' => 'scheduled',
    ]);

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    Livewire::test(ManageVaccinations::class)
        ->assertSeeHtml('bg-red-50 text-red-800');
});

test('does not highlight an overdue due date in red once the vaccine has been administered', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();
    $vaccine = Vaccine::factory()->create();
    $vaccine->species()->attach($pet->species_id);
    $pet->vaccines()->attach($vaccine, [
        'administered_date' => today(),
        'due_date' => today()->subDay(),
        'status' => 'administered',
    ]);

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    Livewire::test(ManageVaccinations::class)
        ->assertDontSeeHtml('bg-red-50 text-red-800');
});

test('highlights a due date within a week in amber when scheduled', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();
    $vaccine = Vaccine::factory()->create();
    $vaccine->species()->attach($pet->species_id);
    $pet->vaccines()->attach($vaccine, [
        'due_date' => today()->addDays(3),
        'status' => 'scheduled',
    ]);

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    Livewire::test(ManageVaccinations::class)
        ->assertSeeHtml('bg-amber-50 text-amber-800');
});

test('filters the vaccinations by vaccine name', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();
    $rabies = Vaccine::factory()->create(['name' => 'Rabies']);
    $distemper = Vaccine::factory()->create(['name' => 'Distemper']);
    $rabies->species()->attach($pet->species_id);
    $distemper->species()->attach($pet->species_id);
    $pet->vaccines()->attach($rabies, ['administered_date' => now()]);
    $pet->vaccines()->attach($distemper, ['administered_date' => now()]);

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    Livewire::test(ManageVaccinations::class)
        ->set('search', 'Rabies')
        ->assertSee('Rabies')
        ->assertDontSee('Distemper');
});

test('filters the vaccinations by pet name', function () {
    $shelter = Shelter::factory()->create();
    $rex = Pet::factory()->for($shelter)->create(['name' => 'Rex']);
    $bella = Pet::factory()->for($shelter)->create(['name' => 'Bella']);
    $vaccine = Vaccine::factory()->create();
    $vaccine->species()->attach([$rex->species_id, $bella->species_id]);
    $rex->vaccines()->attach($vaccine, ['administered_date' => now()]);
    $bella->vaccines()->attach($vaccine, ['administered_date' => now()]);

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    Livewire::test(ManageVaccinations::class)
        ->set('search', 'Rex')
        ->assertSee('Rex')
        ->assertDontSee('Bella');
});

test('filters the vaccinations by next due date within a week', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();
    $withinWeek = Vaccine::factory()->create(['name' => 'Within Week Vaccine']);
    $outsideWeek = Vaccine::factory()->create(['name' => 'Outside Week Vaccine']);
    $withinWeek->species()->attach($pet->species_id);
    $outsideWeek->species()->attach($pet->species_id);
    $pet->vaccines()->attach($withinWeek, ['administered_date' => now(), 'due_date' => today()->addDays(3)]);
    $pet->vaccines()->attach($outsideWeek, ['administered_date' => now(), 'due_date' => today()->addDays(20)]);

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    Livewire::test(ManageVaccinations::class)
        ->set('nextDueFilter', 'within_week')
        ->assertSee('Within Week Vaccine')
        ->assertDontSee('Outside Week Vaccine');
});

test('filters the vaccinations by next due date within two weeks', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();
    $withinTwoWeeks = Vaccine::factory()->create(['name' => 'Within Two Weeks Vaccine']);
    $outsideTwoWeeks = Vaccine::factory()->create(['name' => 'Outside Two Weeks Vaccine']);
    $withinTwoWeeks->species()->attach($pet->species_id);
    $outsideTwoWeeks->species()->attach($pet->species_id);
    $pet->vaccines()->attach($withinTwoWeeks, ['administered_date' => now(), 'due_date' => today()->addDays(10)]);
    $pet->vaccines()->attach($outsideTwoWeeks, ['administered_date' => now(), 'due_date' => today()->addDays(20)]);

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    Livewire::test(ManageVaccinations::class)
        ->set('nextDueFilter', 'within_two_weeks')
        ->assertSee('Within Two Weeks Vaccine')
        ->assertDontSee('Outside Two Weeks Vaccine');
});

test('filters the vaccinations by next due date within a month', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();
    $withinMonth = Vaccine::factory()->create(['name' => 'Within Month Vaccine']);
    $outsideMonth = Vaccine::factory()->create(['name' => 'Outside Month Vaccine']);
    $withinMonth->species()->attach($pet->species_id);
    $outsideMonth->species()->attach($pet->species_id);
    $pet->vaccines()->attach($withinMonth, ['administered_date' => now(), 'due_date' => today()->addDays(25)]);
    $pet->vaccines()->attach($outsideMonth, ['administered_date' => now(), 'due_date' => today()->addDays(40)]);

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    Livewire::test(ManageVaccinations::class)
        ->set('nextDueFilter', 'within_month')
        ->assertSee('Within Month Vaccine')
        ->assertDontSee('Outside Month Vaccine');
});

test('filters the vaccinations by overdue vaccines', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();
    $overdue = Vaccine::factory()->create(['name' => 'Overdue Vaccine']);
    $notYetDue = Vaccine::factory()->create(['name' => 'Not Yet Due Vaccine']);
    $overdue->species()->attach($pet->species_id);
    $notYetDue->species()->attach($pet->species_id);
    $pet->vaccines()->attach($overdue, ['due_date' => today()->subDay(), 'status' => 'scheduled']);
    $pet->vaccines()->attach($notYetDue, ['due_date' => today()->addDays(3), 'status' => 'scheduled']);

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    Livewire::test(ManageVaccinations::class)
        ->set('nextDueFilter', 'overdue')
        ->assertSee('Overdue Vaccine')
        ->assertDontSee('Not Yet Due Vaccine');
});

test('reads the due date filter from the query string', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();
    $overdue = Vaccine::factory()->create(['name' => 'Overdue Vaccine']);
    $notYetDue = Vaccine::factory()->create(['name' => 'Not Yet Due Vaccine']);
    $pet->vaccines()->attach($overdue, ['due_date' => today()->subDay(), 'status' => 'scheduled']);
    $pet->vaccines()->attach($notYetDue, ['due_date' => today()->addDays(3), 'status' => 'scheduled']);

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    Livewire::withQueryParams(['nextDueFilter' => 'overdue'])
        ->test(ManageVaccinations::class)
        ->assertSee('Overdue Vaccine')
        ->assertDontSee('Not Yet Due Vaccine');
});

test('the overdue filter excludes vaccines that have already been administered', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();
    $vaccine = Vaccine::factory()->create(['name' => 'Administered Vaccine']);
    $vaccine->species()->attach($pet->species_id);
    $pet->vaccines()->attach($vaccine, ['administered_date' => today(), 'due_date' => today()->subDay(), 'status' => 'administered']);

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    Livewire::test(ManageVaccinations::class)
        ->set('nextDueFilter', 'overdue')
        ->assertDontSee('Administered Vaccine');
});

test('links the edit action to the vaccination edit route', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();
    $vaccine = Vaccine::factory()->create();
    $vaccine->species()->attach($pet->species_id);
    $pet->vaccines()->attach($vaccine, ['administered_date' => now()]);
    $petVaccine = $pet->vaccines()->first()->pivot;

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    Livewire::test(ManageVaccinations::class)
        ->assertSeeHtml(route('pets.vaccinate.edit', [$pet, $petVaccine]));
});

test('soft-deletes a vaccination instead of removing it permanently', function () {
    $shelter = Shelter::factory()->create();
    $user = User::factory()->forShelter($shelter, 'staff')->create();
    $this->actingAs($user);

    $pet = Pet::factory()->for($shelter)->create();
    $vaccine = Vaccine::factory()->create(['name' => 'Rabies']);
    $vaccine->species()->attach($pet->species_id);
    $pet->vaccines()->attach($vaccine, ['administered_date' => now()]);
    $petVaccine = $pet->vaccines()->first()->pivot;

    Livewire::test(ManageVaccinations::class)
        ->call('deleteVaccination', $petVaccine->id)
        ->assertDontSee('Rabies');

    expect($petVaccine->fresh()->trashed())->toBeTrue();
    expect($petVaccine->fresh()->deleted_by)->toBe($user->id);
});

test('cannot delete a vaccination belonging to another shelter\'s pet', function () {
    $otherShelter = Shelter::factory()->create();
    $otherPet = Pet::factory()->for($otherShelter)->create();
    $vaccine = Vaccine::factory()->create();
    $vaccine->species()->attach($otherPet->species_id);
    $otherPet->vaccines()->attach($vaccine, ['administered_date' => now()]);
    $petVaccine = $otherPet->vaccines()->first()->pivot;

    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    expect(fn () => Livewire::test(ManageVaccinations::class)->call('deleteVaccination', $petVaccine->id))
        ->toThrow(ModelNotFoundException::class);

    expect($petVaccine->fresh()->trashed())->toBeFalse();
});

test('viewers can view the list but cannot edit or delete vaccinations', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();
    $vaccine = Vaccine::factory()->create(['name' => 'Rabies']);
    $pet->vaccines()->attach($vaccine, ['administered_date' => now()]);
    $petVaccine = $pet->vaccines()->first()->pivot;
    $this->actingAs(User::factory()->forShelter($shelter, 'viewer')->create());

    $this->get(route('pets.vaccinations.index'))
        ->assertOk()
        ->assertSee('Rabies')
        ->assertDontSee(route('pets.vaccinate.edit', [$pet, $petVaccine]))
        ->assertDontSee('confirm-vaccination-deletion-'.$petVaccine->id);

    Livewire::test(ManageVaccinations::class)
        ->call('deleteVaccination', $petVaccine->id)
        ->assertForbidden();

    expect($petVaccine->fresh()->trashed())->toBeFalse();
});
