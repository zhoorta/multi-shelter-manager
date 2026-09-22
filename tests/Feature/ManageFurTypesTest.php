<?php

use App\Livewire\Admin\ManageFurTypes;
use App\Models\FurType;
use App\Models\User;
use Livewire\Livewire;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('admin.fur-types.index'));

    $response->assertRedirect(route('login'));
});

test('staff and managers are forbidden from viewing the page', function () {
    $staff = User::factory()->create();
    $this->actingAs($staff);
    $this->get(route('admin.fur-types.index'))->assertForbidden();

    $manager = User::factory()->create();
    $this->actingAs($manager);
    $this->get(route('admin.fur-types.index'))->assertForbidden();
});

test('admins can view the page', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $this->get(route('admin.fur-types.index'))->assertOk();
});

test('shows a placeholder message when there are no fur types', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $this->get(route('admin.fur-types.index'))->assertSee(__('No fur types registered'));
});

test('creates a new fur type and closes the modal', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    Livewire::test(ManageFurTypes::class)
        ->set('furTypeName', 'Curly')
        ->call('saveFurType')
        ->assertHasNoErrors()
        ->assertDispatched('modal-close', name: 'fur-type-form');

    expect(FurType::query()->where('name', 'Curly')->exists())->toBeTrue();
});

test('opening the create modal resets a stale edit state', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $furType = FurType::factory()->create(['name' => 'Short']);

    Livewire::test(ManageFurTypes::class)
        ->call('editFurType', $furType->id)
        ->assertSet('furTypeName', 'Short')
        ->call('createFurType')
        ->assertSet('editingFurTypeId', null)
        ->assertSet('furTypeName', '');
});

test('requires a name to create a fur type', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    Livewire::test(ManageFurTypes::class)
        ->set('furTypeName', '')
        ->call('saveFurType')
        ->assertHasErrors(['furTypeName' => 'required']);
});

test('rejects a duplicate fur type name', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    FurType::factory()->create(['name' => 'Short']);

    Livewire::test(ManageFurTypes::class)
        ->set('furTypeName', 'Short')
        ->call('saveFurType')
        ->assertHasErrors(['furTypeName' => 'unique']);
});

test('updates an existing fur type', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $furType = FurType::factory()->create(['name' => 'Short']);

    Livewire::test(ManageFurTypes::class)
        ->call('editFurType', $furType->id)
        ->assertSet('furTypeName', 'Short')
        ->set('furTypeName', 'Shorthair')
        ->call('saveFurType')
        ->assertHasNoErrors();

    expect($furType->fresh()->name)->toBe('Shorthair');
});

test('soft-deletes a fur type instead of removing it permanently', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $furType = FurType::factory()->create(['name' => 'Short']);

    Livewire::test(ManageFurTypes::class)
        ->call('deleteFurType', $furType->id)
        ->assertDontSee('Short');

    expect($furType->fresh()->trashed())->toBeTrue();
    expect(FurType::query()->find($furType->id))->toBeNull();
});
