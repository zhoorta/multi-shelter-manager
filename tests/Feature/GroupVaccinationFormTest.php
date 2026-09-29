<?php

use App\Livewire\Pets\GroupVaccinationForm;
use App\Models\Pet;
use App\Models\PetVaccine;
use App\Models\Shelter;
use App\Models\Species;
use App\Models\User;
use App\Models\Vaccine;
use Livewire\Livewire;

/**
 * A shelter with dogs enabled and a dog rabies vaccine every 36 months.
 *
 * @return array{Shelter, Species, Vaccine}
 */
function shelterWithDogRabies(): array
{
    $shelter = Shelter::factory()->create();
    $dogs = Species::factory()->create(['name' => 'Dog']);
    $shelter->species()->attach($dogs);

    $vaccine = Vaccine::factory()->create(['name' => 'Rabies', 'frequency_months' => 36]);
    $vaccine->species()->attach($dogs);

    return [$shelter, $dogs, $vaccine];
}

/**
 * A resident dog whose last dose of the vaccine has its next one due on the given date.
 */
function dogWithVaccineDue(Shelter $shelter, Species $dogs, Vaccine $vaccine, string $dueDate): Pet
{
    $pet = Pet::factory()->for($shelter)->create(['species_id' => $dogs->id]);
    $pet->vaccines()->attach($vaccine->id, ['status' => 'administered', 'administered_date' => '2023-05-01', 'due_date' => $dueDate]);

    return $pet;
}

beforeEach(function () {
    $this->travelTo('2026-09-29');
});

test('viewers are forbidden from recording a group vaccination', function () {
    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->forShelter($shelter, 'viewer')->create());

    $this->get(route('pets.vaccinations.group'))->assertForbidden();
});

test('staff can view the group vaccination form', function () {
    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    $this->get(route('pets.vaccinations.group'))->assertOk();
});

test('opened for a month, it ticks only the residents whose vaccination falls due in that month', function () {
    [$shelter, $dogs, $vaccine] = shelterWithDogRabies();
    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    dogWithVaccineDue($shelter, $dogs, $vaccine, '2027-04-30');
    $dueInMay = dogWithVaccineDue($shelter, $dogs, $vaccine, '2027-05-31');
    dogWithVaccineDue($shelter, $dogs, $vaccine, '2027-06-01');
    Pet::factory()->for($shelter)->create(['species_id' => $dogs->id]);
    dogWithVaccineDue($shelter, $dogs, $vaccine, '2027-05-01')->update(['status' => 'adopted']);
    dogWithVaccineDue(Shelter::factory()->create(), $dogs, $vaccine, '2027-05-01');

    Livewire::withQueryParams(['vaccine' => $vaccine->id, 'due' => '2027-05'])
        ->test(GroupVaccinationForm::class)
        ->assertSet('selectedPetIds', [(string) $dueInMay->id])
        ->assertSet('dueDate', '2029-09-29')
        ->assertSee(__('due :date', ['date' => '31/05/2027']));
});

test('opened without a month, it lists the animals due this month', function () {
    [$shelter, $dogs, $vaccine] = shelterWithDogRabies();
    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    $dueThisMonth = dogWithVaccineDue($shelter, $dogs, $vaccine, '2026-09-30');
    dogWithVaccineDue($shelter, $dogs, $vaccine, '2026-10-01');

    Livewire::withQueryParams(['vaccine' => $vaccine->id])
        ->test(GroupVaccinationForm::class)
        ->assertSet('dueMonth', '2026-09')
        ->assertSet('selectedPetIds', [(string) $dueThisMonth->id]);
});

test('the overdue filter ticks the residents whose vaccination is past due', function () {
    [$shelter, $dogs, $vaccine] = shelterWithDogRabies();
    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    $overdue = dogWithVaccineDue($shelter, $dogs, $vaccine, '2025-03-01');
    dogWithVaccineDue($shelter, $dogs, $vaccine, '2026-09-29');

    Livewire::withQueryParams(['vaccine' => $vaccine->id, 'due' => 'overdue'])
        ->test(GroupVaccinationForm::class)
        ->assertSet('selectedPetIds', [(string) $overdue->id]);
});

test('the all residents filter ticks every resident of the vaccine\'s species', function () {
    [$shelter, $dogs, $vaccine] = shelterWithDogRabies();
    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    $neverVaccinated = Pet::factory()->for($shelter)->create(['species_id' => $dogs->id]);
    $dueLater = dogWithVaccineDue($shelter, $dogs, $vaccine, '2028-01-01');
    Pet::factory()->for($shelter)->create();

    Livewire::withQueryParams(['vaccine' => $vaccine->id])
        ->test(GroupVaccinationForm::class)
        ->set('dueMonth', 'all')
        ->assertSet('selectedPetIds', fn (array $selectedPetIds): bool => collect($selectedPetIds)->sort()->values()->all() === collect([(string) $neverVaccinated->id, (string) $dueLater->id])->sort()->values()->all());
});

test('records the dose with its lot and vet for the ticked animals, closing their open vaccination', function () {
    [$shelter, $dogs, $vaccine] = shelterWithDogRabies();
    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    $vaccinated = dogWithVaccineDue($shelter, $dogs, $vaccine, '2026-10-01');
    $scheduled = Pet::factory()->for($shelter)->create(['species_id' => $dogs->id]);
    $scheduled->vaccines()->attach($vaccine->id, ['status' => 'scheduled', 'due_date' => '2026-10-01']);
    $unticked = dogWithVaccineDue($shelter, $dogs, $vaccine, '2026-10-01');

    Livewire::withQueryParams(['vaccine' => $vaccine->id, 'due' => '2026-10'])
        ->test(GroupVaccinationForm::class)
        ->set('selectedPetIds', [(string) $vaccinated->id, (string) $scheduled->id])
        ->set('lotNumber', 'LOT-42')
        ->set('veterinarianName', 'Dr. Campos')
        ->call('saveGroupVaccination')
        ->assertHasNoErrors()
        ->assertRedirect(route('pets.vaccinations.index'));

    $newDose = PetVaccine::query()->where('pet_id', $vaccinated->id)->whereDate('administered_date', '2026-09-29')->sole();
    $fulfilled = PetVaccine::query()->where('pet_id', $scheduled->id)->sole();

    expect($newDose->lot_number)->toBe('LOT-42')
        ->and($newDose->veterinarian_name)->toBe('Dr. Campos')
        ->and($newDose->due_date->toDateString())->toBe('2029-09-29')
        ->and($fulfilled->status)->toBe('administered')
        ->and($fulfilled->administered_date->toDateString())->toBe('2026-09-29')
        ->and(PetVaccine::query()->pending()->pluck('pet_id')->sort()->values()->all())
        ->toBe(collect([$vaccinated->id, $scheduled->id, $unticked->id])->sort()->values()->all())
        ->and(PetVaccine::query()->pending()->where('pet_id', $unticked->id)->sole()->due_date->toDateString())->toBe('2026-10-01');
});

test('ignores ticked ids of animals from another shelter', function () {
    [$shelter, $dogs, $vaccine] = shelterWithDogRabies();
    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    $ownDog = Pet::factory()->for($shelter)->create(['species_id' => $dogs->id]);
    $otherShelterDog = Pet::factory()->create(['species_id' => $dogs->id]);

    Livewire::withQueryParams(['vaccine' => $vaccine->id, 'due' => 'all'])
        ->test(GroupVaccinationForm::class)
        ->set('selectedPetIds', [(string) $ownDog->id, (string) $otherShelterDog->id])
        ->call('saveGroupVaccination')
        ->assertHasNoErrors();

    expect(PetVaccine::query()->pluck('pet_id')->all())->toBe([$ownDog->id]);
});

test('requires at least one animal', function () {
    [$shelter, , $vaccine] = shelterWithDogRabies();
    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    Livewire::test(GroupVaccinationForm::class)
        ->set('vaccineId', (string) $vaccine->id)
        ->call('saveGroupVaccination')
        ->assertHasErrors(['selectedPetIds' => 'required']);
});
