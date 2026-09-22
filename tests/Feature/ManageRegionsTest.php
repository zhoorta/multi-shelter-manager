<?php

use App\Livewire\Admin\ManageRegions;
use App\Models\Region;
use App\Models\Shelter;
use App\Models\User;
use Livewire\Livewire;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('admin.regions.index'));

    $response->assertRedirect(route('login'));
});

test('staff and managers are forbidden from viewing the page', function () {
    $staff = User::factory()->create();
    $this->actingAs($staff);
    $this->get(route('admin.regions.index'))->assertForbidden();

    $manager = User::factory()->create();
    $this->actingAs($manager);
    $this->get(route('admin.regions.index'))->assertForbidden();
});

test('admins can view the page', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $this->get(route('admin.regions.index'))->assertOk();
});

test('shows a placeholder message when there are no regions', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $this->get(route('admin.regions.index'))->assertSee(__('No regions registered'));
});

test('creates a new fur type and closes the modal', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    Livewire::test(ManageRegions::class)
        ->set('regionName', 'Viseu')
        ->call('saveRegion')
        ->assertHasNoErrors()
        ->assertDispatched('modal-close', name: 'region-form');

    expect(Region::query()->where('name', 'Viseu')->exists())->toBeTrue();
});

test('opening the create modal resets a stale edit state', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $region = Region::factory()->create(['name' => 'Lisbon']);

    Livewire::test(ManageRegions::class)
        ->call('editRegion', $region->id)
        ->assertSet('regionName', 'Lisbon')
        ->call('createRegion')
        ->assertSet('editingRegionId', null)
        ->assertSet('regionName', '');
});

test('requires a name to create a fur type', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    Livewire::test(ManageRegions::class)
        ->set('regionName', '')
        ->call('saveRegion')
        ->assertHasErrors(['regionName' => 'required']);
});

test('rejects a duplicate fur type name', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    Region::factory()->create(['name' => 'Lisbon']);

    Livewire::test(ManageRegions::class)
        ->set('regionName', 'Lisbon')
        ->call('saveRegion')
        ->assertHasErrors(['regionName' => 'unique']);
});

test('updates an existing fur type', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $region = Region::factory()->create(['name' => 'Lisbon']);

    Livewire::test(ManageRegions::class)
        ->call('editRegion', $region->id)
        ->assertSet('regionName', 'Lisbon')
        ->set('regionName', 'Lisboa')
        ->call('saveRegion')
        ->assertHasNoErrors();

    expect($region->fresh()->name)->toBe('Lisboa');
});

test('soft-deletes a fur type instead of removing it permanently', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $region = Region::factory()->create(['name' => 'Lisbon']);

    Livewire::test(ManageRegions::class)
        ->call('deleteRegion', $region->id)
        ->assertDontSee('Lisbon');

    expect($region->fresh()->trashed())->toBeTrue();
    expect(Region::query()->find($region->id))->toBeNull();
});

test('a shelter keeps showing its region after the region is soft-deleted', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $region = Region::factory()->create(['name' => 'Lisbon']);
    $shelter = Shelter::factory()->create(['region_id' => $region->id]);

    $region->delete();

    expect($shelter->fresh()->region->name)->toBe('Lisbon');
});

test('only admins see the regions link in the sidebar', function () {
    $this->actingAs(User::factory()->admin()->create());
    $this->get(route('dashboard'))->assertSee(route('admin.regions.index'));

    $this->actingAs(User::factory()->create());
    $this->get(route('dashboard'))->assertDontSee(route('admin.regions.index'));
});
