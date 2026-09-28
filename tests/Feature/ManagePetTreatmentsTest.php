<?php

use App\Livewire\Pets\ManagePetTreatments;
use App\Models\Pet;
use App\Models\PetTreatment;
use App\Models\Shelter;
use App\Models\Treatment;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Livewire;

test('guests are redirected to the login page', function () {
    $this->get(route('pets.treatments.index'))->assertRedirect(route('login'));
});

test('lists only the treatments of the current shelter', function () {
    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    $ownPet = Pet::factory()->for($shelter)->create(['name' => 'Bolinhas']);
    PetTreatment::factory()->for($ownPet)->create();
    $otherPet = Pet::factory()->create(['name' => 'Faruk']);
    PetTreatment::factory()->for($otherPet)->create();

    $this->get(route('pets.treatments.index'))
        ->assertOk()
        ->assertSee('Bolinhas')
        ->assertDontSee('Faruk');
});

test('the overdue filter lists only treatments still pending past their due date', function () {
    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());
    $treatment = Treatment::factory()->create();

    $overduePet = Pet::factory()->for($shelter)->create(['name' => 'Overdue']);
    PetTreatment::factory()->for($overduePet)->for($treatment)->create(['administered_date' => today()->subMonths(4), 'due_date' => today()->subMonth()]);

    $treatedAgainPet = Pet::factory()->for($shelter)->create(['name' => 'TreatedAgain']);
    PetTreatment::factory()->for($treatedAgainPet)->for($treatment)->create(['administered_date' => today()->subMonths(4), 'due_date' => today()->subMonth()]);
    PetTreatment::factory()->for($treatedAgainPet)->for($treatment)->create(['administered_date' => today()->subDay(), 'due_date' => today()->addMonths(3)]);

    Livewire::test(ManagePetTreatments::class)
        ->set('nextDueFilter', 'overdue')
        ->assertSee('Overdue')
        ->assertDontSee('TreatedAgain');
});

test('deletes a treatment record of the current shelter', function () {
    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());
    $petTreatment = PetTreatment::factory()->for(Pet::factory()->for($shelter))->create();

    Livewire::test(ManagePetTreatments::class)->call('deleteTreatment', $petTreatment->id);

    expect($petTreatment->fresh()->trashed())->toBeTrue();
});

test('cannot delete a treatment record of another shelter', function () {
    $this->actingAs(User::factory()->forShelter(Shelter::factory()->create(), 'staff')->create());
    $petTreatment = PetTreatment::factory()->create();

    Livewire::test(ManagePetTreatments::class)->call('deleteTreatment', $petTreatment->id);
})->throws(ModelNotFoundException::class);
