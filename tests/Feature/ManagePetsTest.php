<?php

use App\Livewire\Pets\ManagePets;
use App\Models\Pet;
use App\Models\Shelter;
use App\Models\Species;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Livewire;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('pets.index'));

    $response->assertRedirect(route('login'));
});

test('admins are forbidden from viewing the page', function () {
    $admin = User::factory()->create(['role' => 'admin', 'shelter_id' => null]);
    $this->actingAs($admin);

    $this->get(route('pets.index'))->assertForbidden();
});

test('managers and staff can view the page', function () {
    $shelter = Shelter::factory()->create();

    $manager = User::factory()->create(['role' => 'manager', 'shelter_id' => $shelter->id]);
    $this->actingAs($manager);
    $this->get(route('pets.index'))->assertOk();

    $staff = User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]);
    $this->actingAs($staff);
    $this->get(route('pets.index'))->assertOk();
});

test('shows a placeholder message when there are no pets', function () {
    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    $this->get(route('pets.index'))->assertSee(__('No pets registered'));
});

test('lists only pets belonging to the acting user\'s shelter', function () {
    $shelter = Shelter::factory()->create();
    $otherShelter = Shelter::factory()->create();

    Pet::factory()->for($shelter)->create(['name' => 'Rex']);
    Pet::factory()->for($otherShelter)->create(['name' => 'Other Shelter Dog']);

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    Livewire::test(ManagePets::class)
        ->assertSee('Rex')
        ->assertDontSee('Other Shelter Dog');
});

test('filters pets by name', function () {
    $shelter = Shelter::factory()->create();
    Pet::factory()->for($shelter)->create(['name' => 'Rex']);
    Pet::factory()->for($shelter)->create(['name' => 'Bella']);
    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    Livewire::test(ManagePets::class)
        ->set('search', 'Rex')
        ->assertSee('Rex')
        ->assertDontSee('Bella');
});

test('filters pets by microchip', function () {
    $shelter = Shelter::factory()->create();
    Pet::factory()->for($shelter)->create(['name' => 'Rex', 'chip' => '985121000123456']);
    Pet::factory()->for($shelter)->create(['name' => 'Bella', 'chip' => '985121000987654']);
    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    Livewire::test(ManagePets::class)
        ->set('search', '000123456')
        ->assertSee('Rex')
        ->assertDontSee('Bella');
});

test('filters pets by status', function () {
    $shelter = Shelter::factory()->create();
    Pet::factory()->for($shelter)->create(['name' => 'Rex', 'status' => 'available']);
    Pet::factory()->for($shelter)->create(['name' => 'Bella', 'status' => 'quarantine']);
    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    Livewire::test(ManagePets::class)
        ->set('statusFilter', 'quarantine')
        ->assertSee('Bella')
        ->assertDontSee('Rex');
});

test('filters pets by species', function () {
    $shelter = Shelter::factory()->create();
    $species = Species::factory()->create();
    $otherSpecies = Species::factory()->create();

    Pet::factory()->for($shelter)->for($species)->create(['name' => 'Rex']);
    Pet::factory()->for($shelter)->for($otherSpecies)->create(['name' => 'Bella']);
    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    Livewire::test(ManagePets::class)
        ->set('speciesFilter', (string) $species->id)
        ->assertSee('Rex')
        ->assertDontSee('Bella');
});

test('soft-deletes a pet instead of removing it permanently', function () {
    $shelter = Shelter::factory()->create();
    $user = User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]);
    $this->actingAs($user);

    $pet = Pet::factory()->for($shelter)->create(['name' => 'Rex']);

    Livewire::test(ManagePets::class)
        ->call('deletePet', $pet->id)
        ->assertDontSee('Rex');

    expect($pet->fresh()->trashed())->toBeTrue();
    expect($pet->fresh()->deleted_by)->toBe($user->id);
});

test('cannot delete a pet belonging to another shelter', function () {
    $otherShelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($otherShelter)->create(['name' => 'Rex']);

    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    expect(fn () => Livewire::test(ManagePets::class)->call('deletePet', $pet->id))
        ->toThrow(ModelNotFoundException::class);

    expect($pet->fresh()->trashed())->toBeFalse();
});
