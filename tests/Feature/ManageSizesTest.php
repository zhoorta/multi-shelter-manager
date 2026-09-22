<?php

use App\Livewire\Admin\ManageSizes;
use App\Models\Size;
use App\Models\Species;
use App\Models\User;
use Livewire\Livewire;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('admin.sizes.index'));

    $response->assertRedirect(route('login'));
});

test('staff and managers are forbidden from viewing the page', function () {
    $staff = User::factory()->create();
    $this->actingAs($staff);
    $this->get(route('admin.sizes.index'))->assertForbidden();

    $manager = User::factory()->create();
    $this->actingAs($manager);
    $this->get(route('admin.sizes.index'))->assertForbidden();
});

test('admins can view the page', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $this->get(route('admin.sizes.index'))->assertOk();
});

test('shows a placeholder message when there are no sizes', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $this->get(route('admin.sizes.index'))->assertSee(__('No sizes registered'));
});

test('lists sizes with their species name', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $species = Species::factory()->create(['name' => 'Dog']);
    Size::factory()->create(['species_id' => $species->id, 'name' => 'Médio']);

    Livewire::test(ManageSizes::class)
        ->assertSee('Médio')
        ->assertSee('Dog');
});

test('creates a new size for the selected species and closes the modal', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $species = Species::factory()->create(['name' => 'Dog']);

    Livewire::test(ManageSizes::class)
        ->set('sizeSpeciesId', $species->id)
        ->set('sizeName', 'Grande')
        ->call('saveSize')
        ->assertHasNoErrors()
        ->assertDispatched('modal-close', name: 'size-form');

    $size = Size::query()->where('name', 'Grande')->first();
    expect($size)->not->toBeNull();
    expect($size->species_id)->toBe($species->id);
});

test('creating a size defaults its species to the active filter', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $species = Species::factory()->create(['name' => 'Dog']);

    Livewire::test(ManageSizes::class)
        ->set('filterSpeciesId', (string) $species->id)
        ->call('createSize')
        ->assertSet('sizeSpeciesId', $species->id);
});

test('filters the sizes list by species', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $dog = Species::factory()->create(['name' => 'Dog']);
    $cat = Species::factory()->create(['name' => 'Cat']);
    Size::factory()->create(['species_id' => $dog->id, 'name' => 'Grande']);
    Size::factory()->create(['species_id' => $cat->id, 'name' => 'Pequeno']);

    Livewire::test(ManageSizes::class)
        ->assertSee('Grande')
        ->assertSee('Pequeno')
        ->set('filterSpeciesId', (string) $dog->id)
        ->assertSee('Grande')
        ->assertDontSee('Pequeno');
});

test('requires a species to create a size', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    Livewire::test(ManageSizes::class)
        ->set('sizeSpeciesId', null)
        ->set('sizeName', 'Grande')
        ->call('saveSize')
        ->assertHasErrors(['sizeSpeciesId' => 'required']);
});

test('requires a name to create a size', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $species = Species::factory()->create(['name' => 'Dog']);

    Livewire::test(ManageSizes::class)
        ->set('sizeSpeciesId', $species->id)
        ->set('sizeName', '')
        ->call('saveSize')
        ->assertHasErrors(['sizeName' => 'required']);
});

test('updates an existing size, including reassigning its species', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $dog = Species::factory()->create(['name' => 'Dog']);
    $cat = Species::factory()->create(['name' => 'Cat']);
    $size = Size::factory()->create(['species_id' => $dog->id, 'name' => 'Grande']);

    Livewire::test(ManageSizes::class)
        ->call('editSize', $size->id)
        ->assertSet('sizeName', 'Grande')
        ->assertSet('sizeSpeciesId', $dog->id)
        ->set('sizeSpeciesId', $cat->id)
        ->set('sizeName', 'Pequeno')
        ->call('saveSize')
        ->assertHasNoErrors();

    expect($size->fresh()->name)->toBe('Pequeno');
    expect($size->fresh()->species_id)->toBe($cat->id);
});

test('soft-deletes a size instead of removing it permanently', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $species = Species::factory()->create(['name' => 'Dog']);
    $size = Size::factory()->create(['species_id' => $species->id, 'name' => 'Grande']);

    Livewire::test(ManageSizes::class)
        ->call('deleteSize', $size->id)
        ->assertDontSee('Grande');

    expect($size->fresh()->trashed())->toBeTrue();
});

test('hides sizes whose species has been soft-deleted', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $species = Species::factory()->create(['name' => 'Dog']);
    $size = Size::factory()->create(['species_id' => $species->id, 'name' => 'Grande']);
    $species->delete();

    $this->get(route('admin.sizes.index'))
        ->assertOk()
        ->assertDontSee('Grande');
});

test('soft-deleted species do not appear in the species dropdowns', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $active = Species::factory()->create(['name' => 'Dog']);
    $deleted = Species::factory()->create(['name' => 'Extinct Species']);
    $deleted->delete();

    Livewire::test(ManageSizes::class)
        ->assertSee('Dog')
        ->assertDontSee('Extinct Species');
});

test('still saves a size whose species was soft-deleted after it was assigned', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $species = Species::factory()->create(['name' => 'Dog']);
    $size = Size::factory()->create(['species_id' => $species->id, 'name' => 'Grande']);
    $species->delete();

    Livewire::test(ManageSizes::class)
        ->call('editSize', $size->id)
        ->assertSet('sizeSpeciesId', $species->id)
        ->set('sizeName', 'Extra Grande')
        ->call('saveSize')
        ->assertHasNoErrors();

    expect($size->fresh()->name)->toBe('Extra Grande');
});
