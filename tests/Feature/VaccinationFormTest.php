<?php

use App\Livewire\Pets\VaccinationForm;
use App\Models\Pet;
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
    $admin = User::factory()->create(['role' => 'admin', 'shelter_id' => null]);
    $this->actingAs($admin);

    $pet = Pet::factory()->create();

    $this->get(route('pets.vaccinate', $pet))->assertForbidden();
});

test('managers and staff can view the vaccination form for a pet in their shelter', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();

    $manager = User::factory()->create(['role' => 'manager', 'shelter_id' => $shelter->id]);
    $this->actingAs($manager);
    $this->get(route('pets.vaccinate', $pet))->assertOk();

    $staff = User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]);
    $this->actingAs($staff);
    $this->get(route('pets.vaccinate', $pet))->assertOk();
});

test('returns 404 when the pet belongs to another shelter', function () {
    $otherShelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($otherShelter)->create();

    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    $this->get(route('pets.vaccinate', $pet))->assertNotFound();
});

test('creates a vaccination record for the pet', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();
    $vaccine = Vaccine::factory()->create();
    $vaccine->species()->attach($pet->species_id);

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    $component = Livewire::test(VaccinationForm::class, ['pet' => $pet])
        ->set('vaccineId', (string) $vaccine->id)
        ->set('administeredAt', '2026-01-15')
        ->set('nextDueAt', '2027-01-15')
        ->set('lotNumber', 'LOT-123')
        ->set('veterinarianName', 'Dr. Silva')
        ->set('vaccinationNotes', 'No adverse reaction')
        ->call('saveVaccination')
        ->assertHasNoErrors();

    $petVaccine = $pet->vaccines()->first();
    expect($petVaccine)->not->toBeNull();
    expect($petVaccine->id)->toBe($vaccine->id);
    expect($petVaccine->pivot->administered_at->toDateString())->toBe('2026-01-15');
    expect($petVaccine->pivot->next_due_at->toDateString())->toBe('2027-01-15');
    expect($petVaccine->pivot->lot_number)->toBe('LOT-123');
    expect($petVaccine->pivot->veterinarian_name)->toBe('Dr. Silva');
    expect($petVaccine->pivot->notes)->toBe('No adverse reaction');

    $component->assertRedirect(route('pets.show', $pet));
});

test('allows administering the same vaccine to a pet more than once', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();
    $vaccine = Vaccine::factory()->create();
    $vaccine->species()->attach($pet->species_id);

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    Livewire::test(VaccinationForm::class, ['pet' => $pet])
        ->set('vaccineId', (string) $vaccine->id)
        ->set('administeredAt', '2026-01-15')
        ->call('saveVaccination');

    Livewire::test(VaccinationForm::class, ['pet' => $pet])
        ->set('vaccineId', (string) $vaccine->id)
        ->set('administeredAt', '2027-01-15')
        ->call('saveVaccination')
        ->assertHasNoErrors();

    expect($pet->vaccines()->count())->toBe(2);
});

test('requires a vaccine and an administered date', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    Livewire::test(VaccinationForm::class, ['pet' => $pet])
        ->set('vaccineId', '')
        ->set('administeredAt', '')
        ->call('saveVaccination')
        ->assertHasErrors(['vaccineId' => 'required', 'administeredAt' => 'required']);
});

test('rejects a vaccine that does not belong to the pet species', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();

    $otherSpecies = Species::factory()->create();
    $mismatchedVaccine = Vaccine::factory()->create();
    $mismatchedVaccine->species()->attach($otherSpecies);

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    Livewire::test(VaccinationForm::class, ['pet' => $pet])
        ->set('vaccineId', (string) $mismatchedVaccine->id)
        ->set('administeredAt', '2026-01-15')
        ->call('saveVaccination')
        ->assertHasErrors(['vaccineId' => 'exists']);
});

test('rejects a next due date before the administered date', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();
    $vaccine = Vaccine::factory()->create();
    $vaccine->species()->attach($pet->species_id);

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    Livewire::test(VaccinationForm::class, ['pet' => $pet])
        ->set('vaccineId', (string) $vaccine->id)
        ->set('administeredAt', '2026-01-15')
        ->set('nextDueAt', '2026-01-10')
        ->call('saveVaccination')
        ->assertHasErrors(['nextDueAt' => 'after_or_equal']);
});

test('managers and staff can view the edit form for an existing vaccination', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();
    $vaccine = Vaccine::factory()->create();
    $vaccine->species()->attach($pet->species_id);
    $pet->vaccines()->attach($vaccine, ['administered_at' => now()]);
    $petVaccine = $pet->vaccines()->first()->pivot;

    $manager = User::factory()->create(['role' => 'manager', 'shelter_id' => $shelter->id]);
    $this->actingAs($manager);
    $this->get(route('pets.vaccinate.edit', [$pet, $petVaccine]))->assertOk();

    $staff = User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]);
    $this->actingAs($staff);
    $this->get(route('pets.vaccinate.edit', [$pet, $petVaccine]))->assertOk();
});

test('loads the existing vaccination data when editing', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();
    $vaccine = Vaccine::factory()->create();
    $vaccine->species()->attach($pet->species_id);
    $pet->vaccines()->attach($vaccine, [
        'administered_at' => '2026-01-15',
        'next_due_at' => '2027-01-15',
        'lot_number' => 'LOT-123',
        'veterinarian_name' => 'Dr. Silva',
        'notes' => 'No adverse reaction',
    ]);
    $petVaccine = $pet->vaccines()->first()->pivot;

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    Livewire::test(VaccinationForm::class, ['pet' => $pet, 'petVaccine' => $petVaccine])
        ->assertSet('vaccineId', (string) $vaccine->id)
        ->assertSet('administeredAt', '2026-01-15')
        ->assertSet('nextDueAt', '2027-01-15')
        ->assertSet('lotNumber', 'LOT-123')
        ->assertSet('veterinarianName', 'Dr. Silva')
        ->assertSet('vaccinationNotes', 'No adverse reaction');
});

test('updates an existing vaccination record', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();
    $vaccine = Vaccine::factory()->create();
    $vaccine->species()->attach($pet->species_id);
    $pet->vaccines()->attach($vaccine, ['administered_at' => '2026-01-15']);
    $petVaccine = $pet->vaccines()->first()->pivot;

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    $component = Livewire::test(VaccinationForm::class, ['pet' => $pet, 'petVaccine' => $petVaccine])
        ->set('nextDueAt', '2027-02-01')
        ->set('lotNumber', 'LOT-999')
        ->call('saveVaccination')
        ->assertHasNoErrors();

    expect($pet->vaccines()->count())->toBe(1);
    expect($petVaccine->fresh()->next_due_at->toDateString())->toBe('2027-02-01');
    expect($petVaccine->fresh()->lot_number)->toBe('LOT-999');

    $component->assertRedirect(route('pets.show', $pet));
});

test('returns 404 when editing a vaccination that does not belong to the given pet', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();
    $otherPet = Pet::factory()->for($shelter)->create();
    $vaccine = Vaccine::factory()->create();
    $vaccine->species()->attach($otherPet->species_id);
    $otherPet->vaccines()->attach($vaccine, ['administered_at' => now()]);
    $otherPetVaccine = $otherPet->vaccines()->first()->pivot;

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    $this->get(route('pets.vaccinate.edit', [$pet, $otherPetVaccine]))->assertNotFound();
});
