<?php

use App\Livewire\Pets\TreatmentForm;
use App\Models\Pet;
use App\Models\PetTreatment;
use App\Models\Shelter;
use App\Models\Treatment;
use App\Models\User;
use Livewire\Livewire;

test('guests are redirected to the login page', function () {
    $pet = Pet::factory()->create();

    $this->get(route('pets.treat', $pet))->assertRedirect(route('login'));
});

test('viewers are forbidden from viewing the form', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();
    $this->actingAs(User::factory()->forShelter($shelter, 'viewer')->create());

    $this->get(route('pets.treat', $pet))->assertForbidden();
});

test('returns 404 when the pet belongs to another shelter', function () {
    $pet = Pet::factory()->create();
    $this->actingAs(User::factory()->forShelter(Shelter::factory()->create(), 'staff')->create());

    $this->get(route('pets.treat', $pet))->assertNotFound();
});

test('creates a treatment record with the next date filled in from the frequency', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();
    $treatment = Treatment::factory()->create(['frequency_months' => 3]);
    $treatment->species()->attach($pet->species_id);
    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    Livewire::test(TreatmentForm::class, ['pet' => $pet])
        ->set('treatmentId', (string) $treatment->id)
        ->set('administeredDate', '2026-11-30')
        ->assertSet('dueDate', '2027-02-28')
        ->set('product', 'Milbemax')
        ->set('treatmentNotes', 'Weighed 12 kg')
        ->call('saveTreatment')
        ->assertHasNoErrors()
        ->assertRedirect(route('pets.show', $pet));

    $petTreatment = PetTreatment::query()->sole();
    expect($petTreatment->pet_id)->toBe($pet->id)
        ->and($petTreatment->status)->toBe('administered')
        ->and($petTreatment->due_date->toDateString())->toBe('2027-02-28')
        ->and($petTreatment->product)->toBe('Milbemax')
        ->and($petTreatment->notes)->toBe('Weighed 12 kg');
});

test('logging a dose fulfils the open scheduled treatment instead of adding a row', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();
    $treatment = Treatment::factory()->create();
    $treatment->species()->attach($pet->species_id);
    $scheduled = PetTreatment::factory()->for($pet)->for($treatment)->scheduled('2026-10-01')->create();
    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    Livewire::test(TreatmentForm::class, ['pet' => $pet])
        ->set('treatmentId', (string) $treatment->id)
        ->set('administeredDate', '2026-10-03')
        ->call('saveTreatment')
        ->assertHasNoErrors();

    expect(PetTreatment::query()->count())->toBe(1)
        ->and($scheduled->fresh()->status)->toBe('administered');
});

test('requires either the administered date or the due date', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();
    $treatment = Treatment::factory()->create();
    $treatment->species()->attach($pet->species_id);
    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    Livewire::test(TreatmentForm::class, ['pet' => $pet])
        ->set('treatmentId', (string) $treatment->id)
        ->call('saveTreatment')
        ->assertHasErrors(['administeredDate']);
});

test('rejects a treatment that does not apply to the pet species', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();
    $treatment = Treatment::factory()->create();
    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    Livewire::test(TreatmentForm::class, ['pet' => $pet])
        ->set('treatmentId', (string) $treatment->id)
        ->set('administeredDate', '2026-10-01')
        ->call('saveTreatment')
        ->assertHasErrors(['treatmentId' => 'exists']);
});

test('updates an existing treatment record', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();
    $treatment = Treatment::factory()->create();
    $treatment->species()->attach($pet->species_id);
    $petTreatment = PetTreatment::factory()->for($pet)->for($treatment)->create(['product' => 'Drontal']);
    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    Livewire::test(TreatmentForm::class, ['pet' => $pet, 'petTreatment' => $petTreatment])
        ->assertSet('product', 'Drontal')
        ->set('product', 'Endogard')
        ->call('saveTreatment')
        ->assertHasNoErrors();

    expect($petTreatment->fresh()->product)->toBe('Endogard');
});

test('returns 404 when editing a treatment of another pet', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();
    $petTreatment = PetTreatment::factory()->for(Pet::factory()->for($shelter))->create();
    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    $this->get(route('pets.treat.edit', [$pet, $petTreatment]))->assertNotFound();
});
