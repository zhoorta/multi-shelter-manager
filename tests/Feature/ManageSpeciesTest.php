<?php

use App\Livewire\Admin\ManageSpecies;
use App\Models\Breed;
use App\Models\Species;
use App\Models\User;
use Livewire\Livewire;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('admin.species.index'));

    $response->assertRedirect(route('login'));
});

test('staff and managers are forbidden from viewing the page', function () {
    $staff = User::factory()->create(['role' => 'staff']);
    $this->actingAs($staff);
    $this->get(route('admin.species.index'))->assertForbidden();

    $manager = User::factory()->create(['role' => 'manager']);
    $this->actingAs($manager);
    $this->get(route('admin.species.index'))->assertForbidden();
});

test('admins can view the page', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    $this->get(route('admin.species.index'))->assertOk();
});

test('shows a placeholder message when there are no species', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    $this->get(route('admin.species.index'))->assertSee(__('No species registered'));
});

test('lists species with their breeds count, excluding soft-deleted breeds', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    $species = Species::factory()->create(['name' => 'Dog']);
    Breed::factory()->count(2)->create(['species_id' => $species->id]);
    Breed::factory()->create(['species_id' => $species->id])->delete();

    Livewire::test(ManageSpecies::class)
        ->assertSee('Dog')
        ->assertSee('2');
});

test('creates a new species and closes the modal', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    Livewire::test(ManageSpecies::class)
        ->set('speciesName', 'Cat')
        ->call('saveSpecies')
        ->assertHasNoErrors()
        ->assertDispatched('modal-close', name: 'species-form');

    expect(Species::query()->where('name', 'Cat')->exists())->toBeTrue();
});

test('opening the create modal resets a stale edit state', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    $species = Species::factory()->create(['name' => 'Dog']);

    Livewire::test(ManageSpecies::class)
        ->call('editSpecies', $species->id)
        ->assertSet('speciesName', 'Dog')
        ->call('createSpecies')
        ->assertSet('editingSpeciesId', null)
        ->assertSet('speciesName', '');
});

test('requires a name to create a species', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    Livewire::test(ManageSpecies::class)
        ->set('speciesName', '')
        ->call('saveSpecies')
        ->assertHasErrors(['speciesName' => 'required']);
});

test('rejects a duplicate species name', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    Species::factory()->create(['name' => 'Dog']);

    Livewire::test(ManageSpecies::class)
        ->set('speciesName', 'Dog')
        ->call('saveSpecies')
        ->assertHasErrors(['speciesName' => 'unique']);
});

test('updates an existing species', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    $species = Species::factory()->create(['name' => 'Dog']);

    Livewire::test(ManageSpecies::class)
        ->call('editSpecies', $species->id)
        ->assertSet('speciesName', 'Dog')
        ->set('speciesName', 'Canine')
        ->call('saveSpecies')
        ->assertHasNoErrors();

    expect($species->fresh()->name)->toBe('Canine');
});

test('soft-deletes a species instead of removing it permanently', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    $species = Species::factory()->create(['name' => 'Dog']);

    Livewire::test(ManageSpecies::class)
        ->call('deleteSpecies', $species->id)
        ->assertDontSee('Dog');

    expect($species->fresh()->trashed())->toBeTrue();
    expect(Species::query()->find($species->id))->toBeNull();
});
