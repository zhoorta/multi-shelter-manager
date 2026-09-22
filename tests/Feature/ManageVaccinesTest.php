<?php

use App\Livewire\Admin\ManageVaccines;
use App\Models\Species;
use App\Models\User;
use App\Models\Vaccine;
use Livewire\Livewire;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('admin.vaccines.index'));

    $response->assertRedirect(route('login'));
});

test('staff and managers are forbidden from viewing the page', function () {
    $staff = User::factory()->create();
    $this->actingAs($staff);
    $this->get(route('admin.vaccines.index'))->assertForbidden();

    $manager = User::factory()->create();
    $this->actingAs($manager);
    $this->get(route('admin.vaccines.index'))->assertForbidden();
});

test('admins can view the page', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $this->get(route('admin.vaccines.index'))->assertOk();
});

test('shows a placeholder message when there are no vaccines', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $this->get(route('admin.vaccines.index'))->assertSee(__('No vaccines registered'));
});

test('lists vaccines with their assigned species', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $dog = Species::factory()->create(['name' => 'Dog']);
    $vaccine = Vaccine::factory()->create(['name' => 'Rabies']);
    $vaccine->species()->attach($dog->id);

    Livewire::test(ManageVaccines::class)
        ->assertSee('Rabies')
        ->assertSee('Dog');
});

test('creates a new vaccine with assigned species and closes the modal', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $dog = Species::factory()->create(['name' => 'Dog']);
    $cat = Species::factory()->create(['name' => 'Cat']);

    Livewire::test(ManageVaccines::class)
        ->set('vaccineName', 'Rabies')
        ->set('vaccineSpeciesIds', [$dog->id, $cat->id])
        ->call('saveVaccine')
        ->assertHasNoErrors()
        ->assertDispatched('modal-close', name: 'vaccine-form');

    $vaccine = Vaccine::query()->where('name', 'Rabies')->firstOrFail();
    expect($vaccine->species->pluck('id')->sort()->values()->all())->toBe([$dog->id, $cat->id]);
    expect($vaccine->species->first()->pivot->created_at)->not->toBeNull();
    expect($vaccine->species->first()->pivot->updated_at)->not->toBeNull();
});

test('opening the create modal resets a stale edit state', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $dog = Species::factory()->create(['name' => 'Dog']);
    $vaccine = Vaccine::factory()->create(['name' => 'Rabies']);
    $vaccine->species()->attach($dog->id);

    Livewire::test(ManageVaccines::class)
        ->call('editVaccine', $vaccine->id)
        ->assertSet('vaccineName', 'Rabies')
        ->assertSet('vaccineSpeciesIds', [$dog->id])
        ->call('createVaccine')
        ->assertSet('editingVaccineId', null)
        ->assertSet('vaccineName', '')
        ->assertSet('vaccineSpeciesIds', []);
});

test('requires a name to create a vaccine', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    Livewire::test(ManageVaccines::class)
        ->set('vaccineName', '')
        ->call('saveVaccine')
        ->assertHasErrors(['vaccineName' => 'required']);
});

test('rejects an unknown species id', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    Livewire::test(ManageVaccines::class)
        ->set('vaccineName', 'Rabies')
        ->set('vaccineSpeciesIds', [999999])
        ->call('saveVaccine')
        ->assertHasErrors(['vaccineSpeciesIds.0' => 'exists']);
});

test('updates an existing vaccine and its species', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $dog = Species::factory()->create(['name' => 'Dog']);
    $cat = Species::factory()->create(['name' => 'Cat']);
    $vaccine = Vaccine::factory()->create(['name' => 'Rabies']);
    $vaccine->species()->attach($dog->id);

    Livewire::test(ManageVaccines::class)
        ->call('editVaccine', $vaccine->id)
        ->set('vaccineName', 'Antirrabica')
        ->set('vaccineSpeciesIds', [$cat->id])
        ->call('saveVaccine')
        ->assertHasNoErrors();

    $vaccine->refresh();
    expect($vaccine->name)->toBe('Antirrabica');
    expect($vaccine->species->pluck('id')->all())->toBe([$cat->id]);
});

test('soft-deletes a vaccine instead of removing it permanently', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $vaccine = Vaccine::factory()->create(['name' => 'Rabies']);

    Livewire::test(ManageVaccines::class)
        ->call('deleteVaccine', $vaccine->id)
        ->assertDontSee('Rabies');

    expect($vaccine->fresh()->trashed())->toBeTrue();
    expect(Vaccine::query()->find($vaccine->id))->toBeNull();
});
