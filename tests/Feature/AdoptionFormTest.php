<?php

use App\Livewire\Pets\AdoptionForm;
use App\Models\Pet;
use App\Models\Shelter;
use App\Models\User;
use Livewire\Livewire;

test('guests are redirected to the login page', function () {
    $pet = Pet::factory()->create();

    $this->get(route('pets.adopt', $pet))->assertRedirect(route('login'));
});

test('admins are forbidden from viewing the form', function () {
    $admin = User::factory()->create(['role' => 'admin', 'shelter_id' => null]);
    $this->actingAs($admin);

    $pet = Pet::factory()->create();

    $this->get(route('pets.adopt', $pet))->assertForbidden();
});

test('managers and staff can view the adoption form for a pet in their shelter', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();

    $manager = User::factory()->create(['role' => 'manager', 'shelter_id' => $shelter->id]);
    $this->actingAs($manager);
    $this->get(route('pets.adopt', $pet))->assertOk();

    $staff = User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]);
    $this->actingAs($staff);
    $this->get(route('pets.adopt', $pet))->assertOk();
});

test('returns 404 when the pet belongs to another shelter', function () {
    $otherShelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($otherShelter)->create();

    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    $this->get(route('pets.adopt', $pet))->assertNotFound();
});

test('is forbidden when the pet is already adopted', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create(['status' => 'adopted']);

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    $this->get(route('pets.adopt', $pet))->assertForbidden();
});

test('creates an adoption record, marks the pet as adopted, and sets its checkout date', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create(['status' => 'available', 'checkout_date' => null]);

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    $component = Livewire::test(AdoptionForm::class, ['pet' => $pet])
        ->set('adopterName', 'Maria Silva')
        ->set('adopterEmail', 'maria@example.com')
        ->set('adopterPhone', '912345678')
        ->set('adoptionDate', '2026-01-15')
        ->set('adoptionFee', '25.50')
        ->set('applicationStatus', 'Approved')
        ->call('saveAdoption')
        ->assertHasNoErrors();

    $adoption = $pet->adoptions()->first();
    expect($adoption)->not->toBeNull();
    expect($adoption->name)->toBe('Maria Silva');
    expect($adoption->email)->toBe('maria@example.com');
    expect((string) $adoption->adoption_fee)->toBe('25.50');
    expect($adoption->adoption_date->toDateString())->toBe('2026-01-15');
    expect($adoption->application_status)->toBe('Approved');

    $pet->refresh();
    expect($pet->status)->toBe('adopted');
    expect($pet->checkout_date->toDateString())->toBe('2026-01-15');

    $component->assertRedirect(route('pets.show', $pet));
});

test('requires an adopter name and an adoption date', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    Livewire::test(AdoptionForm::class, ['pet' => $pet])
        ->set('adopterName', '')
        ->set('adoptionDate', '')
        ->call('saveAdoption')
        ->assertHasErrors(['adopterName' => 'required', 'adoptionDate' => 'required']);
});
