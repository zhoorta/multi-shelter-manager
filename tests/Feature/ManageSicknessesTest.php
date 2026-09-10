<?php

use App\Livewire\Admin\ManageSicknesses;
use App\Models\Sickness;
use App\Models\Species;
use App\Models\User;
use Livewire\Livewire;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('admin.sicknesses.index'));

    $response->assertRedirect(route('login'));
});

test('staff and managers are forbidden from viewing the page', function () {
    $staff = User::factory()->create(['role' => 'staff']);
    $this->actingAs($staff);
    $this->get(route('admin.sicknesses.index'))->assertForbidden();

    $manager = User::factory()->create(['role' => 'manager']);
    $this->actingAs($manager);
    $this->get(route('admin.sicknesses.index'))->assertForbidden();
});

test('admins can view the page', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    $this->get(route('admin.sicknesses.index'))->assertOk();
});

test('shows a placeholder message when there are no sicknesses', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    $this->get(route('admin.sicknesses.index'))->assertSee(__('No sicknesses registered'));
});

test('lists sicknesses with their description and assigned species', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    $dog = Species::factory()->create(['name' => 'Dog']);
    $sickness = Sickness::factory()->create(['name' => 'Parvovirus', 'description' => 'Highly contagious viral illness']);
    $sickness->species()->attach($dog->id);

    Livewire::test(ManageSicknesses::class)
        ->assertSee('Parvovirus')
        ->assertSee('Highly contagious viral illness')
        ->assertSee('Dog');
});

test('creates a new sickness with assigned species and closes the modal', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    $dog = Species::factory()->create(['name' => 'Dog']);
    $cat = Species::factory()->create(['name' => 'Cat']);

    Livewire::test(ManageSicknesses::class)
        ->set('sicknessName', 'Parvovirus')
        ->set('sicknessDescription', 'Highly contagious viral illness')
        ->set('sicknessSpeciesIds', [$dog->id, $cat->id])
        ->call('saveSickness')
        ->assertHasNoErrors()
        ->assertDispatched('modal-close', name: 'sickness-form');

    $sickness = Sickness::query()->where('name', 'Parvovirus')->firstOrFail();
    expect($sickness->description)->toBe('Highly contagious viral illness');
    expect($sickness->species->pluck('id')->sort()->values()->all())->toBe([$dog->id, $cat->id]);
    expect($sickness->species->first()->pivot->created_at)->not->toBeNull();
    expect($sickness->species->first()->pivot->updated_at)->not->toBeNull();
});

test('creates a sickness without a description', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    Livewire::test(ManageSicknesses::class)
        ->set('sicknessName', 'Parvovirus')
        ->set('sicknessDescription', '')
        ->call('saveSickness')
        ->assertHasNoErrors();

    $sickness = Sickness::query()->where('name', 'Parvovirus')->firstOrFail();
    expect($sickness->description)->toBeNull();
});

test('opening the create modal resets a stale edit state', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    $dog = Species::factory()->create(['name' => 'Dog']);
    $sickness = Sickness::factory()->create(['name' => 'Parvovirus', 'description' => 'Some description']);
    $sickness->species()->attach($dog->id);

    Livewire::test(ManageSicknesses::class)
        ->call('editSickness', $sickness->id)
        ->assertSet('sicknessName', 'Parvovirus')
        ->assertSet('sicknessDescription', 'Some description')
        ->assertSet('sicknessSpeciesIds', [$dog->id])
        ->call('createSickness')
        ->assertSet('editingSicknessId', null)
        ->assertSet('sicknessName', '')
        ->assertSet('sicknessDescription', '')
        ->assertSet('sicknessSpeciesIds', []);
});

test('requires a name to create a sickness', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    Livewire::test(ManageSicknesses::class)
        ->set('sicknessName', '')
        ->call('saveSickness')
        ->assertHasErrors(['sicknessName' => 'required']);
});

test('rejects an unknown species id', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    Livewire::test(ManageSicknesses::class)
        ->set('sicknessName', 'Parvovirus')
        ->set('sicknessSpeciesIds', [999999])
        ->call('saveSickness')
        ->assertHasErrors(['sicknessSpeciesIds.0' => 'exists']);
});

test('updates an existing sickness and its species', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    $dog = Species::factory()->create(['name' => 'Dog']);
    $cat = Species::factory()->create(['name' => 'Cat']);
    $sickness = Sickness::factory()->create(['name' => 'Parvovirus']);
    $sickness->species()->attach($dog->id);

    Livewire::test(ManageSicknesses::class)
        ->call('editSickness', $sickness->id)
        ->set('sicknessName', 'Parvovirose')
        ->set('sicknessDescription', 'Updated description')
        ->set('sicknessSpeciesIds', [$cat->id])
        ->call('saveSickness')
        ->assertHasNoErrors();

    $sickness->refresh();
    expect($sickness->name)->toBe('Parvovirose');
    expect($sickness->description)->toBe('Updated description');
    expect($sickness->species->pluck('id')->all())->toBe([$cat->id]);
});

test('soft-deletes a sickness instead of removing it permanently', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    $sickness = Sickness::factory()->create(['name' => 'Parvovirus']);

    Livewire::test(ManageSicknesses::class)
        ->call('deleteSickness', $sickness->id)
        ->assertDontSee('Parvovirus');

    expect($sickness->fresh()->trashed())->toBeTrue();
    expect(Sickness::query()->find($sickness->id))->toBeNull();
});
