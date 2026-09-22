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
    $staff = User::factory()->create();
    $this->actingAs($staff);
    $this->get(route('admin.species.index'))->assertForbidden();

    $manager = User::factory()->create();
    $this->actingAs($manager);
    $this->get(route('admin.species.index'))->assertForbidden();
});

test('admins can view the page', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $this->get(route('admin.species.index'))->assertOk();
});

test('shows a placeholder message when there are no species', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $this->get(route('admin.species.index'))->assertSee(__('No species registered'));
});

test('lists species with their plural name and breeds count, excluding soft-deleted breeds', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $species = Species::factory()->create(['name' => 'Dog', 'name_plural' => 'Dogs']);
    Breed::factory()->count(2)->create(['species_id' => $species->id]);
    Breed::factory()->create(['species_id' => $species->id])->delete();

    Livewire::test(ManageSpecies::class)
        ->assertSee('Dog')
        ->assertSee('Dogs')
        ->assertSee('2');
});

test('creates a new species and closes the modal', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    Livewire::test(ManageSpecies::class)
        ->set('speciesName', 'Cat')
        ->set('speciesNamePlural', 'Cats')
        ->set('speciesHasPureBreedField', true)
        ->call('saveSpecies')
        ->assertHasNoErrors()
        ->assertDispatched('modal-close', name: 'species-form');

    $species = Species::query()->where('name', 'Cat')->where('name_plural', 'Cats')->first();
    expect($species)->not->toBeNull();
    expect($species->has_pure_breed_field)->toBeTrue();
});

test('opening the create modal resets a stale edit state', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $species = Species::factory()->create(['name' => 'Dog', 'name_plural' => 'Dogs', 'has_pure_breed_field' => true]);

    Livewire::test(ManageSpecies::class)
        ->call('editSpecies', $species->id)
        ->assertSet('speciesName', 'Dog')
        ->assertSet('speciesNamePlural', 'Dogs')
        ->assertSet('speciesHasPureBreedField', true)
        ->call('createSpecies')
        ->assertSet('editingSpeciesId', null)
        ->assertSet('speciesName', '')
        ->assertSet('speciesNamePlural', '')
        ->assertSet('speciesHasPureBreedField', false);
});

test('requires a name and plural name to create a species', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    Livewire::test(ManageSpecies::class)
        ->set('speciesName', '')
        ->set('speciesNamePlural', '')
        ->call('saveSpecies')
        ->assertHasErrors(['speciesName' => 'required', 'speciesNamePlural' => 'required']);
});

test('rejects a duplicate species name', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    Species::factory()->create(['name' => 'Dog']);

    Livewire::test(ManageSpecies::class)
        ->set('speciesName', 'Dog')
        ->set('speciesNamePlural', 'Something Else')
        ->call('saveSpecies')
        ->assertHasErrors(['speciesName' => 'unique']);
});

test('rejects a duplicate species plural name', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    Species::factory()->create(['name_plural' => 'Dogs']);

    Livewire::test(ManageSpecies::class)
        ->set('speciesName', 'Something Else')
        ->set('speciesNamePlural', 'Dogs')
        ->call('saveSpecies')
        ->assertHasErrors(['speciesNamePlural' => 'unique']);
});

test('updates an existing species', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $species = Species::factory()->create(['name' => 'Dog', 'name_plural' => 'Dogs', 'has_pure_breed_field' => false]);

    Livewire::test(ManageSpecies::class)
        ->call('editSpecies', $species->id)
        ->assertSet('speciesName', 'Dog')
        ->set('speciesName', 'Canine')
        ->set('speciesNamePlural', 'Canines')
        ->set('speciesHasPureBreedField', true)
        ->call('saveSpecies')
        ->assertHasNoErrors();

    expect($species->fresh()->name)->toBe('Canine');
    expect($species->fresh()->name_plural)->toBe('Canines');
    expect($species->fresh()->has_pure_breed_field)->toBeTrue();
});

test('soft-deletes a species instead of removing it permanently', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $species = Species::factory()->create(['name' => 'Dog']);

    Livewire::test(ManageSpecies::class)
        ->call('deleteSpecies', $species->id)
        ->assertDontSee('Dog');

    expect($species->fresh()->trashed())->toBeTrue();
    expect(Species::query()->find($species->id))->toBeNull();
});
