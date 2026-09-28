<?php

use App\Livewire\Pets\GroupTreatmentForm;
use App\Models\Cage;
use App\Models\Facility;
use App\Models\Pet;
use App\Models\PetTreatment;
use App\Models\Shelter;
use App\Models\Species;
use App\Models\Treatment;
use App\Models\User;
use App\Models\Wing;
use Livewire\Livewire;

/**
 * A shelter with dogs enabled and a dog deworming treatment every 3 months.
 *
 * @return array{Shelter, Species, Treatment}
 */
function shelterWithDogDeworming(): array
{
    $shelter = Shelter::factory()->create();
    $dogs = Species::factory()->create(['name' => 'Dog']);
    $shelter->species()->attach($dogs);

    $treatment = Treatment::factory()->create(['name' => 'Internal deworming', 'frequency_months' => 3]);
    $treatment->species()->attach($dogs);

    return [$shelter, $dogs, $treatment];
}

test('guests are redirected to the login page', function () {
    $this->get(route('pets.treatments.group'))->assertRedirect(route('login'));
});

test('viewers are forbidden from recording a group treatment', function () {
    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->forShelter($shelter, 'viewer')->create());

    $this->get(route('pets.treatments.group'))->assertForbidden();
});

test('staff can view the group treatment form', function () {
    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    $this->get(route('pets.treatments.group'))->assertOk();
});

test('choosing a treatment ticks every resident of its species in the shelter and fills the next date', function () {
    [$shelter, $dogs, $treatment] = shelterWithDogDeworming();
    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    $resident = Pet::factory()->for($shelter)->create(['species_id' => $dogs->id]);
    $notAvailable = Pet::factory()->for($shelter)->create(['species_id' => $dogs->id, 'status' => 'not_available']);
    Pet::factory()->for($shelter)->create(['species_id' => $dogs->id, 'status' => 'adopted']);
    Pet::factory()->for($shelter)->create(['species_id' => $dogs->id, 'status' => 'deceased']);
    Pet::factory()->for($shelter)->create();
    Pet::factory()->create(['species_id' => $dogs->id]);

    Livewire::test(GroupTreatmentForm::class)
        ->set('administeredDate', '2026-10-01')
        ->set('treatmentId', (string) $treatment->id)
        ->assertSet('selectedPetIds', fn (array $selectedPetIds): bool => collect($selectedPetIds)->sort()->values()->all() === [(string) $resident->id, (string) $notAvailable->id])
        ->assertSet('dueDate', '2027-01-01');
});

test('filtering by wing only ticks the residents of that wing', function () {
    [$shelter, $dogs, $treatment] = shelterWithDogDeworming();
    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    $facility = Facility::factory()->for($shelter)->create();
    $wing = Wing::factory()->for($facility)->create();
    $otherWing = Wing::factory()->for($facility)->create();

    $inWing = Pet::factory()->for($shelter)->create(['species_id' => $dogs->id, 'cage_id' => Cage::factory()->for($wing)->create()->id]);
    Pet::factory()->for($shelter)->create(['species_id' => $dogs->id, 'cage_id' => Cage::factory()->for($otherWing)->create()->id]);

    Livewire::test(GroupTreatmentForm::class)
        ->set('treatmentId', (string) $treatment->id)
        ->set('locationFilter', 'wing:'.$wing->id)
        ->assertSet('selectedPetIds', [(string) $inWing->id]);
});

test('records the treatment for the ticked animals only', function () {
    [$shelter, $dogs, $treatment] = shelterWithDogDeworming();
    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    $treated = Pet::factory()->for($shelter)->create(['species_id' => $dogs->id]);
    $unticked = Pet::factory()->for($shelter)->create(['species_id' => $dogs->id]);

    Livewire::test(GroupTreatmentForm::class)
        ->set('treatmentId', (string) $treatment->id)
        ->set('administeredDate', '2026-10-01')
        ->set('selectedPetIds', [(string) $treated->id])
        ->set('product', 'Drontal')
        ->call('saveGroupTreatment')
        ->assertHasNoErrors()
        ->assertRedirect(route('pets.treatments.index'));

    $petTreatment = PetTreatment::query()->sole();
    expect($petTreatment->pet_id)->toBe($treated->id)
        ->and($petTreatment->treatment_id)->toBe($treatment->id)
        ->and($petTreatment->administered_date->toDateString())->toBe('2026-10-01')
        ->and($petTreatment->due_date->toDateString())->toBe('2027-01-01')
        ->and($petTreatment->status)->toBe('administered')
        ->and($petTreatment->product)->toBe('Drontal');

    expect(PetTreatment::query()->where('pet_id', $unticked->id)->exists())->toBeFalse();
});

test('a group round fulfils each animal\'s open scheduled treatment', function () {
    [$shelter, $dogs, $treatment] = shelterWithDogDeworming();
    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    $pet = Pet::factory()->for($shelter)->create(['species_id' => $dogs->id]);
    $scheduled = PetTreatment::factory()->for($pet)->for($treatment)->scheduled('2026-10-01')->create();

    Livewire::test(GroupTreatmentForm::class)
        ->set('treatmentId', (string) $treatment->id)
        ->set('administeredDate', '2026-10-02')
        ->call('saveGroupTreatment')
        ->assertHasNoErrors();

    expect(PetTreatment::query()->count())->toBe(1)
        ->and($scheduled->fresh()->status)->toBe('administered')
        ->and($scheduled->fresh()->administered_date->toDateString())->toBe('2026-10-02');
});

test('ignores ticked ids of animals from another shelter', function () {
    [$shelter, $dogs, $treatment] = shelterWithDogDeworming();
    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    $otherShelterPet = Pet::factory()->create(['species_id' => $dogs->id]);

    Livewire::test(GroupTreatmentForm::class)
        ->set('treatmentId', (string) $treatment->id)
        ->set('selectedPetIds', [(string) $otherShelterPet->id])
        ->call('saveGroupTreatment');

    expect(PetTreatment::query()->exists())->toBeFalse();
});

test('requires at least one animal', function () {
    [$shelter, , $treatment] = shelterWithDogDeworming();
    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    Livewire::test(GroupTreatmentForm::class)
        ->set('treatmentId', (string) $treatment->id)
        ->call('saveGroupTreatment')
        ->assertHasErrors(['selectedPetIds' => 'required'])
        ->assertSee(__('Select at least one animal.'));
});
