<?php

use App\Livewire\Pets\VaccinationForm;
use App\Models\Pet;
use App\Models\PetVaccine;
use App\Models\Shelter;
use App\Models\Species;
use App\Models\User;
use App\Models\Vaccine;
use Livewire\Livewire;

test('guests are redirected to the login page', function () {
    $pet = Pet::factory()->create();

    $this->get(route('pets.vaccinate', $pet))->assertRedirect(route('login'));
});

test('admins are forbidden from viewing the form', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $pet = Pet::factory()->create();

    $this->get(route('pets.vaccinate', $pet))->assertForbidden();
});

test('managers and staff can view the vaccination form for a pet in their shelter', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();

    $manager = User::factory()->forShelter($shelter, 'manager')->create();
    $this->actingAs($manager);
    $this->get(route('pets.vaccinate', $pet))->assertOk();

    $staff = User::factory()->forShelter($shelter, 'staff')->create();
    $this->actingAs($staff);
    $this->get(route('pets.vaccinate', $pet))->assertOk();
});

test('returns 404 when the pet belongs to another shelter', function () {
    $otherShelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($otherShelter)->create();

    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    $this->get(route('pets.vaccinate', $pet))->assertNotFound();
});

test('creates a vaccination record for the pet', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();
    $vaccine = Vaccine::factory()->create();
    $vaccine->species()->attach($pet->species_id);

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    $component = Livewire::test(VaccinationForm::class, ['pet' => $pet])
        ->set('vaccineId', (string) $vaccine->id)
        ->set('administeredDate', '2026-01-15')
        ->set('dueDate', '2027-01-15')
        ->set('lotNumber', 'LOT-123')
        ->set('veterinarianName', 'Dr. Silva')
        ->set('vaccinationNotes', 'No adverse reaction')
        ->call('saveVaccination')
        ->assertHasNoErrors();

    $petVaccine = $pet->vaccines()->first();
    expect($petVaccine)->not->toBeNull();
    expect($petVaccine->id)->toBe($vaccine->id);
    expect($petVaccine->pivot->administered_date->toDateString())->toBe('2026-01-15');
    expect($petVaccine->pivot->due_date->toDateString())->toBe('2027-01-15');
    expect($petVaccine->pivot->status)->toBe('administered');
    expect($petVaccine->pivot->lot_number)->toBe('LOT-123');
    expect($petVaccine->pivot->veterinarian_name)->toBe('Dr. Silva');
    expect($petVaccine->pivot->notes)->toBe('No adverse reaction');

    $component->assertRedirect(route('pets.show', $pet));
});

test('creates a scheduled vaccination with only a due date', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();
    $vaccine = Vaccine::factory()->create();
    $vaccine->species()->attach($pet->species_id);

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    Livewire::test(VaccinationForm::class, ['pet' => $pet])
        ->set('vaccineId', (string) $vaccine->id)
        ->set('dueDate', '2027-01-15')
        ->call('saveVaccination')
        ->assertHasNoErrors();

    $petVaccine = $pet->vaccines()->first();
    expect($petVaccine->pivot->administered_date)->toBeNull();
    expect($petVaccine->pivot->due_date->toDateString())->toBe('2027-01-15');
    expect($petVaccine->pivot->status)->toBe('scheduled');
});

test('allows administering the same vaccine to a pet more than once', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();
    $vaccine = Vaccine::factory()->create();
    $vaccine->species()->attach($pet->species_id);

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    Livewire::test(VaccinationForm::class, ['pet' => $pet])
        ->set('vaccineId', (string) $vaccine->id)
        ->set('administeredDate', '2026-01-15')
        ->call('saveVaccination');

    Livewire::test(VaccinationForm::class, ['pet' => $pet])
        ->set('vaccineId', (string) $vaccine->id)
        ->set('administeredDate', '2027-01-15')
        ->call('saveVaccination')
        ->assertHasNoErrors();

    expect($pet->vaccines()->count())->toBe(2);
});

test('logging a dose fulfils the open scheduled vaccination instead of adding a new row', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();
    $vaccine = Vaccine::factory()->create();
    $vaccine->species()->attach($pet->species_id);
    $pet->vaccines()->attach($vaccine, ['due_date' => '2026-10-01', 'status' => 'scheduled']);
    $scheduled = PetVaccine::query()->sole();

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    Livewire::test(VaccinationForm::class, ['pet' => $pet])
        ->set('vaccineId', (string) $vaccine->id)
        ->set('administeredDate', '2026-09-28')
        ->set('dueDate', '2027-09-28')
        ->call('saveVaccination')
        ->assertHasNoErrors();

    $vaccination = PetVaccine::query()->sole();
    expect($vaccination->id)->toBe($scheduled->id)
        ->and($vaccination->status)->toBe('administered')
        ->and($vaccination->administered_date->toDateString())->toBe('2026-09-28')
        ->and($vaccination->due_date->toDateString())->toBe('2027-09-28');
});

test('backfilling a dose older than one already logged leaves the scheduled vaccination open', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();
    $vaccine = Vaccine::factory()->create();
    $vaccine->species()->attach($pet->species_id);
    $pet->vaccines()->attach($vaccine, ['administered_date' => '2025-10-01', 'status' => 'administered']);
    $pet->vaccines()->attach($vaccine, ['due_date' => '2026-10-01', 'status' => 'scheduled']);

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    Livewire::test(VaccinationForm::class, ['pet' => $pet])
        ->set('vaccineId', (string) $vaccine->id)
        ->set('administeredDate', '2024-10-01')
        ->call('saveVaccination')
        ->assertHasNoErrors();

    expect(PetVaccine::query()->count())->toBe(3)
        ->and(PetVaccine::query()->where('status', 'scheduled')->count())->toBe(1);
});

test('requires a vaccine', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    Livewire::test(VaccinationForm::class, ['pet' => $pet])
        ->set('vaccineId', '')
        ->set('administeredDate', '2026-01-15')
        ->call('saveVaccination')
        ->assertHasErrors(['vaccineId' => 'required']);
});

test('requires either the administered date or the due date', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();
    $vaccine = Vaccine::factory()->create();
    $vaccine->species()->attach($pet->species_id);

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    Livewire::test(VaccinationForm::class, ['pet' => $pet])
        ->set('vaccineId', (string) $vaccine->id)
        ->set('administeredDate', '')
        ->set('dueDate', '')
        ->call('saveVaccination')
        ->assertHasErrors(['administeredDate']);
});

test('rejects a vaccine that does not belong to the pet species', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();

    $otherSpecies = Species::factory()->create();
    $mismatchedVaccine = Vaccine::factory()->create();
    $mismatchedVaccine->species()->attach($otherSpecies);

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    Livewire::test(VaccinationForm::class, ['pet' => $pet])
        ->set('vaccineId', (string) $mismatchedVaccine->id)
        ->set('administeredDate', '2026-01-15')
        ->call('saveVaccination')
        ->assertHasErrors(['vaccineId' => 'exists']);
});

test('allows an administered date after the due date', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();
    $vaccine = Vaccine::factory()->create();
    $vaccine->species()->attach($pet->species_id);

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    Livewire::test(VaccinationForm::class, ['pet' => $pet])
        ->set('vaccineId', (string) $vaccine->id)
        ->set('administeredDate', '2026-01-15')
        ->set('dueDate', '2026-01-10')
        ->call('saveVaccination')
        ->assertHasNoErrors();

    $petVaccine = $pet->vaccines()->first();
    expect($petVaccine->pivot->administered_date->toDateString())->toBe('2026-01-15');
    expect($petVaccine->pivot->due_date->toDateString())->toBe('2026-01-10');
});

test('managers and staff can view the edit form for an existing vaccination', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();
    $vaccine = Vaccine::factory()->create();
    $vaccine->species()->attach($pet->species_id);
    $pet->vaccines()->attach($vaccine, ['administered_date' => now()]);
    $petVaccine = $pet->vaccines()->first()->pivot;

    $manager = User::factory()->forShelter($shelter, 'manager')->create();
    $this->actingAs($manager);
    $this->get(route('pets.vaccinate.edit', [$pet, $petVaccine]))->assertOk();

    $staff = User::factory()->forShelter($shelter, 'staff')->create();
    $this->actingAs($staff);
    $this->get(route('pets.vaccinate.edit', [$pet, $petVaccine]))->assertOk();
});

test('loads the existing vaccination data when editing', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();
    $vaccine = Vaccine::factory()->create();
    $vaccine->species()->attach($pet->species_id);
    $pet->vaccines()->attach($vaccine, [
        'administered_date' => '2026-01-15',
        'due_date' => '2027-01-15',
        'lot_number' => 'LOT-123',
        'veterinarian_name' => 'Dr. Silva',
        'notes' => 'No adverse reaction',
    ]);
    $petVaccine = $pet->vaccines()->first()->pivot;

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    Livewire::test(VaccinationForm::class, ['pet' => $pet, 'petVaccine' => $petVaccine])
        ->assertSet('vaccineId', (string) $vaccine->id)
        ->assertSet('administeredDate', '2026-01-15')
        ->assertSet('dueDate', '2027-01-15')
        ->assertSet('lotNumber', 'LOT-123')
        ->assertSet('veterinarianName', 'Dr. Silva')
        ->assertSet('vaccinationNotes', 'No adverse reaction');
});

test('updates an existing vaccination record', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();
    $vaccine = Vaccine::factory()->create();
    $vaccine->species()->attach($pet->species_id);
    $pet->vaccines()->attach($vaccine, ['administered_date' => '2026-01-15']);
    $petVaccine = $pet->vaccines()->first()->pivot;

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    $component = Livewire::test(VaccinationForm::class, ['pet' => $pet, 'petVaccine' => $petVaccine])
        ->set('dueDate', '2027-02-01')
        ->set('lotNumber', 'LOT-999')
        ->call('saveVaccination')
        ->assertHasNoErrors();

    expect($pet->vaccines()->count())->toBe(1);
    expect($petVaccine->fresh()->due_date->toDateString())->toBe('2027-02-01');
    expect($petVaccine->fresh()->lot_number)->toBe('LOT-999');

    $component->assertRedirect(route('pets.show', $pet));
});

test('editing a scheduled vaccination to add an administered date changes its status to administered', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();
    $vaccine = Vaccine::factory()->create();
    $vaccine->species()->attach($pet->species_id);
    $pet->vaccines()->attach($vaccine, ['due_date' => '2026-02-01', 'status' => 'scheduled']);
    $petVaccine = $pet->vaccines()->first()->pivot;

    expect($petVaccine->status)->toBe('scheduled');

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    Livewire::test(VaccinationForm::class, ['pet' => $pet, 'petVaccine' => $petVaccine])
        ->set('administeredDate', '2026-02-01')
        ->call('saveVaccination')
        ->assertHasNoErrors();

    expect($petVaccine->fresh()->status)->toBe('administered');
    expect($petVaccine->fresh()->administered_date->toDateString())->toBe('2026-02-01');
});

test('returns 404 when editing a vaccination that does not belong to the given pet', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();
    $otherPet = Pet::factory()->for($shelter)->create();
    $vaccine = Vaccine::factory()->create();
    $vaccine->species()->attach($otherPet->species_id);
    $otherPet->vaccines()->attach($vaccine, ['administered_date' => now()]);
    $otherPetVaccine = $otherPet->vaccines()->first()->pivot;

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    $this->get(route('pets.vaccinate.edit', [$pet, $otherPetVaccine]))->assertNotFound();
});

test('viewers are forbidden from viewing the form', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();
    $this->actingAs(User::factory()->forShelter($shelter, 'viewer')->create());

    $this->get(route('pets.vaccinate', $pet))->assertForbidden();
});

test('fills the next due date from the vaccine frequency and recalculates it when the dose date changes', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();
    $rabies = Vaccine::factory()->create(['frequency_months' => 36]);
    $rabies->species()->attach($pet->species_id);
    $withoutFrequency = Vaccine::factory()->create();
    $withoutFrequency->species()->attach($pet->species_id);

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    Livewire::test(VaccinationForm::class, ['pet' => $pet])
        ->set('vaccineId', (string) $rabies->id)
        ->set('administeredDate', '2024-02-29')
        ->assertSet('dueDate', '2027-02-28')
        ->assertSee('every 36 months')
        ->set('administeredDate', '2026-09-28')
        ->assertSet('dueDate', '2029-09-28')
        ->set('vaccineId', (string) $withoutFrequency->id)
        ->assertSet('dueDate', '');
});

test('never overwrites a next due date the user typed', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();
    $rabies = Vaccine::factory()->create(['frequency_months' => 36]);
    $rabies->species()->attach($pet->species_id);

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    Livewire::test(VaccinationForm::class, ['pet' => $pet])
        ->set('dueDate', '2027-06-01')
        ->set('vaccineId', (string) $rabies->id)
        ->set('administeredDate', '2026-09-28')
        ->assertSet('dueDate', '2027-06-01');
});
