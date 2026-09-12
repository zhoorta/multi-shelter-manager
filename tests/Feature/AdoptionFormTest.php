<?php

use App\Livewire\Pets\AdoptionForm;
use App\Models\Adoption;
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

test('creates an adoption record with a return date', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create(['status' => 'available']);

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    Livewire::test(AdoptionForm::class, ['pet' => $pet])
        ->set('adopterName', 'Maria Silva')
        ->set('adoptionDate', '2026-01-15')
        ->set('returnDate', '2026-02-01')
        ->call('saveAdoption')
        ->assertHasNoErrors();

    $adoption = $pet->adoptions()->first();
    expect($adoption->return_date->toDateString())->toBe('2026-02-01');
});

test('rejects a return date before the adoption date', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create(['status' => 'available']);

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    Livewire::test(AdoptionForm::class, ['pet' => $pet])
        ->set('adopterName', 'Maria Silva')
        ->set('adoptionDate', '2026-01-15')
        ->set('returnDate', '2026-01-10')
        ->call('saveAdoption')
        ->assertHasErrors(['returnDate' => 'after_or_equal']);
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

test('managers and staff can view the edit form for an already-adopted pet', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create(['status' => 'adopted']);
    $adoption = Adoption::factory()->for($pet)->create();

    $manager = User::factory()->create(['role' => 'manager', 'shelter_id' => $shelter->id]);
    $this->actingAs($manager);
    $this->get(route('pets.adopt.edit', [$pet, $adoption]))->assertOk();

    $staff = User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]);
    $this->actingAs($staff);
    $this->get(route('pets.adopt.edit', [$pet, $adoption]))->assertOk();
});

test('loads the existing adoption data when editing', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create(['status' => 'adopted']);
    $adoption = Adoption::factory()->for($pet)->create(['name' => 'Ana Costa', 'return_date' => '2026-02-01']);

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    Livewire::test(AdoptionForm::class, ['pet' => $pet, 'adoption' => $adoption])
        ->assertSet('adopterName', 'Ana Costa')
        ->assertSet('adopterEmail', $adoption->email)
        ->assertSet('returnDate', '2026-02-01');
});

test('updates an existing adoption record and keeps the pet checkout date in sync', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create(['status' => 'adopted', 'checkout_date' => '2026-01-15']);
    $adoption = Adoption::factory()->for($pet)->create(['name' => 'Ana Costa', 'adoption_date' => '2026-01-15']);

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    $component = Livewire::test(AdoptionForm::class, ['pet' => $pet, 'adoption' => $adoption])
        ->set('adopterName', 'Ana Costa Silva')
        ->set('adoptionDate', '2026-01-20')
        ->call('saveAdoption')
        ->assertHasNoErrors();

    expect($pet->adoptions()->count())->toBe(1);
    expect($adoption->refresh()->name)->toBe('Ana Costa Silva');
    expect($adoption->adoption_date->toDateString())->toBe('2026-01-20');

    $pet->refresh();
    expect($pet->status)->toBe('adopted');
    expect($pet->checkout_date->toDateString())->toBe('2026-01-20');

    $component->assertRedirect(route('pets.show', $pet));
});

test('setting a return date marks the pet as available again and clears its checkout date', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create(['status' => 'adopted', 'checkout_date' => '2026-01-15']);
    $adoption = Adoption::factory()->for($pet)->create(['adoption_date' => '2026-01-15']);

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    Livewire::test(AdoptionForm::class, ['pet' => $pet, 'adoption' => $adoption])
        ->set('returnDate', '2026-03-01')
        ->call('saveAdoption')
        ->assertHasNoErrors();

    expect($adoption->refresh()->return_date->toDateString())->toBe('2026-03-01');

    $pet->refresh();
    expect($pet->status)->toBe('available');
    expect($pet->checkout_date)->toBeNull();
});

test('a returned pet can be registered for a new adoption', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create(['status' => 'adopted', 'checkout_date' => '2026-01-15']);
    $adoption = Adoption::factory()->for($pet)->create(['adoption_date' => '2026-01-15']);

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    Livewire::test(AdoptionForm::class, ['pet' => $pet, 'adoption' => $adoption])
        ->set('returnDate', '2026-03-01')
        ->call('saveAdoption');

    $pet->refresh();

    $this->get(route('pets.adopt', $pet))->assertOk();

    Livewire::test(AdoptionForm::class, ['pet' => $pet])
        ->set('adopterName', 'Nova Adotante')
        ->set('adoptionDate', '2026-03-10')
        ->call('saveAdoption')
        ->assertHasNoErrors();

    expect($pet->adoptions()->count())->toBe(2);

    $pet->refresh();
    expect($pet->status)->toBe('adopted');
    expect($pet->checkout_date->toDateString())->toBe('2026-03-10');
});

test('rejects clearing the return date when the pet has since been adopted again', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create(['status' => 'adopted', 'checkout_date' => '2026-03-10']);

    $returnedAdoption = Adoption::factory()->for($pet)->create([
        'adoption_date' => '2026-01-15',
        'return_date' => '2026-03-01',
    ]);
    Adoption::factory()->for($pet)->create([
        'adoption_date' => '2026-03-10',
        'return_date' => null,
    ]);

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    Livewire::test(AdoptionForm::class, ['pet' => $pet, 'adoption' => $returnedAdoption])
        ->set('returnDate', '')
        ->call('saveAdoption')
        ->assertHasErrors(['returnDate']);

    expect($returnedAdoption->refresh()->return_date->toDateString())->toBe('2026-03-01');
});

test('editing a past adoption does not touch the pet status or checkout date when it has since been re-adopted', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create(['status' => 'adopted', 'checkout_date' => '2026-03-10']);

    $returnedAdoption = Adoption::factory()->for($pet)->create([
        'name' => 'Ana Costa',
        'adoption_date' => '2026-01-15',
        'return_date' => '2026-03-01',
    ]);
    Adoption::factory()->for($pet)->create([
        'adoption_date' => '2026-03-10',
        'return_date' => null,
    ]);

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    Livewire::test(AdoptionForm::class, ['pet' => $pet, 'adoption' => $returnedAdoption])
        ->set('adopterName', 'Ana Costa Silva')
        ->call('saveAdoption')
        ->assertHasNoErrors();

    expect($returnedAdoption->refresh()->name)->toBe('Ana Costa Silva');
    expect($returnedAdoption->return_date->toDateString())->toBe('2026-03-01');

    $pet->refresh();
    expect($pet->status)->toBe('adopted');
    expect($pet->checkout_date->toDateString())->toBe('2026-03-10');
});

test('allows clearing the return date when it is the only or most recent adoption', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create(['status' => 'available', 'checkout_date' => null]);

    $adoption = Adoption::factory()->for($pet)->create([
        'adoption_date' => '2026-01-15',
        'return_date' => '2026-03-01',
    ]);

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    Livewire::test(AdoptionForm::class, ['pet' => $pet, 'adoption' => $adoption])
        ->set('returnDate', '')
        ->call('saveAdoption')
        ->assertHasNoErrors();

    expect($adoption->refresh()->return_date)->toBeNull();

    $pet->refresh();
    expect($pet->status)->toBe('adopted');
    expect($pet->checkout_date->toDateString())->toBe('2026-01-15');
});

test('returns 404 when the adoption does not belong to the given pet', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create(['status' => 'adopted']);
    $otherPet = Pet::factory()->for($shelter)->create(['status' => 'adopted']);
    $adoption = Adoption::factory()->for($otherPet)->create();

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    $this->get(route('pets.adopt.edit', [$pet, $adoption]))->assertNotFound();
});
