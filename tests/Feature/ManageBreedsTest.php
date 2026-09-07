<?php

use App\Livewire\Admin\ManageBreeds;
use App\Models\Breed;
use App\Models\Species;
use App\Models\User;
use Livewire\Livewire;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('admin.breeds.index'));

    $response->assertRedirect(route('login'));
});

test('staff and managers are forbidden from viewing the page', function () {
    $staff = User::factory()->create(['role' => 'staff']);
    $this->actingAs($staff);
    $this->get(route('admin.breeds.index'))->assertForbidden();

    $manager = User::factory()->create(['role' => 'manager']);
    $this->actingAs($manager);
    $this->get(route('admin.breeds.index'))->assertForbidden();
});

test('admins can view the page', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    $this->get(route('admin.breeds.index'))->assertOk();
});

test('shows a placeholder message when there are no breeds', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    $this->get(route('admin.breeds.index'))->assertSee(__('No breeds registered'));
});

test('lists breeds with their species name', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    $species = Species::factory()->create(['name' => 'Dog']);
    Breed::factory()->create(['species_id' => $species->id, 'name' => 'Labrador']);

    Livewire::test(ManageBreeds::class)
        ->assertSee('Labrador')
        ->assertSee('Dog');
});

test('creates a new breed for the selected species and closes the modal', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    $species = Species::factory()->create(['name' => 'Dog']);

    Livewire::test(ManageBreeds::class)
        ->set('breedSpeciesId', $species->id)
        ->set('breedName', 'Poodle')
        ->set('breedIsDefault', true)
        ->call('saveBreed')
        ->assertHasNoErrors()
        ->assertDispatched('modal-close', name: 'breed-form');

    $breed = Breed::query()->where('name', 'Poodle')->first();
    expect($breed)->not->toBeNull();
    expect($breed->species_id)->toBe($species->id);
    expect($breed->is_default)->toBeTrue();
});

test('creating a breed defaults its species to the active filter', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    $species = Species::factory()->create(['name' => 'Dog']);

    Livewire::test(ManageBreeds::class)
        ->set('filterSpeciesId', (string) $species->id)
        ->call('createBreed')
        ->assertSet('breedSpeciesId', $species->id);
});

test('filters the breeds list by species', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    $dog = Species::factory()->create(['name' => 'Dog']);
    $cat = Species::factory()->create(['name' => 'Cat']);
    Breed::factory()->create(['species_id' => $dog->id, 'name' => 'Labrador']);
    Breed::factory()->create(['species_id' => $cat->id, 'name' => 'Siamese']);

    Livewire::test(ManageBreeds::class)
        ->assertSee('Labrador')
        ->assertSee('Siamese')
        ->set('filterSpeciesId', (string) $dog->id)
        ->assertSee('Labrador')
        ->assertDontSee('Siamese');
});

test('requires a species to create a breed', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    Livewire::test(ManageBreeds::class)
        ->set('breedSpeciesId', null)
        ->set('breedName', 'Poodle')
        ->call('saveBreed')
        ->assertHasErrors(['breedSpeciesId' => 'required']);
});

test('requires a name to create a breed', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    $species = Species::factory()->create(['name' => 'Dog']);

    Livewire::test(ManageBreeds::class)
        ->set('breedSpeciesId', $species->id)
        ->set('breedName', '')
        ->call('saveBreed')
        ->assertHasErrors(['breedName' => 'required']);
});

test('updates an existing breed, including reassigning its species', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    $dog = Species::factory()->create(['name' => 'Dog']);
    $cat = Species::factory()->create(['name' => 'Cat']);
    $breed = Breed::factory()->create(['species_id' => $dog->id, 'name' => 'Labrador']);

    Livewire::test(ManageBreeds::class)
        ->call('editBreed', $breed->id)
        ->assertSet('breedName', 'Labrador')
        ->assertSet('breedSpeciesId', $dog->id)
        ->set('breedSpeciesId', $cat->id)
        ->set('breedName', 'Siamese')
        ->call('saveBreed')
        ->assertHasNoErrors();

    expect($breed->fresh()->name)->toBe('Siamese');
    expect($breed->fresh()->species_id)->toBe($cat->id);
});

test('soft-deletes a breed instead of removing it permanently', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    $species = Species::factory()->create(['name' => 'Dog']);
    $breed = Breed::factory()->create(['species_id' => $species->id, 'name' => 'Labrador']);

    Livewire::test(ManageBreeds::class)
        ->call('deleteBreed', $breed->id)
        ->assertDontSee('Labrador');

    expect($breed->fresh()->trashed())->toBeTrue();
});

test('hides breeds whose species has been soft-deleted', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    $species = Species::factory()->create(['name' => 'Dog']);
    $breed = Breed::factory()->create(['species_id' => $species->id, 'name' => 'Labrador']);
    $species->delete();

    $this->get(route('admin.breeds.index'))
        ->assertOk()
        ->assertDontSee('Labrador');
});

test('soft-deleted species do not appear in the species dropdowns', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    $active = Species::factory()->create(['name' => 'Dog']);
    $deleted = Species::factory()->create(['name' => 'Extinct Species']);
    $deleted->delete();

    Livewire::test(ManageBreeds::class)
        ->assertSee('Dog')
        ->assertDontSee('Extinct Species');
});

test('still saves a breed whose species was soft-deleted after it was assigned', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    $species = Species::factory()->create(['name' => 'Dog']);
    $breed = Breed::factory()->create(['species_id' => $species->id, 'name' => 'Labrador']);
    $species->delete();

    Livewire::test(ManageBreeds::class)
        ->call('editBreed', $breed->id)
        ->assertSet('breedSpeciesId', $species->id)
        ->set('breedName', 'Labrador Retriever')
        ->call('saveBreed')
        ->assertHasNoErrors();

    expect($breed->fresh()->name)->toBe('Labrador Retriever');
});
