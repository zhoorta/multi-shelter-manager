<?php

use App\Livewire\Wings\ManageSpaces;
use App\Models\Cage;
use App\Models\Shelter;
use App\Models\User;
use App\Models\Wing;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Livewire;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('wings.index'));

    $response->assertRedirect(route('login'));
});

test('admins are forbidden from viewing the page', function () {
    $admin = User::factory()->create(['role' => 'admin', 'shelter_id' => null]);
    $this->actingAs($admin);

    $this->get(route('wings.index'))->assertForbidden();
});

test('managers and staff can view the page', function () {
    $shelter = Shelter::factory()->create();

    $manager = User::factory()->create(['role' => 'manager', 'shelter_id' => $shelter->id]);
    $this->actingAs($manager);
    $this->get(route('wings.index'))->assertOk();

    $staff = User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]);
    $this->actingAs($staff);
    $this->get(route('wings.index'))->assertOk();
});

test('shows a placeholder message when there are no wings', function () {
    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    $this->get(route('wings.index'))->assertSee(__('No wings registered'));
});

test('lists only wings belonging to the acting user\'s shelter, with their cages', function () {
    $shelter = Shelter::factory()->create();
    $otherShelter = Shelter::factory()->create();

    $wing = Wing::factory()->for($shelter)->create(['name' => 'North Wing']);
    Cage::factory()->for($wing)->create(['code' => 'C-01', 'capacity' => 3]);

    Wing::factory()->for($otherShelter)->create(['name' => 'Other Shelter Wing']);

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    Livewire::test(ManageSpaces::class)
        ->assertSee('North Wing')
        ->assertSee('C-01')
        ->assertDontSee('Other Shelter Wing');
});

test('creates a new wing scoped to the acting user\'s shelter and closes the modal', function () {
    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    Livewire::test(ManageSpaces::class)
        ->set('wingName', 'North Wing')
        ->set('wingDescription', 'Quiet area for recovering pets')
        ->call('saveWing')
        ->assertHasNoErrors()
        ->assertDispatched('modal-close', name: 'wing-form');

    $wing = Wing::query()->where('name', 'North Wing')->first();
    expect($wing)->not->toBeNull();
    expect($wing->shelter_id)->toBe($shelter->id);
    expect($wing->description)->toBe('Quiet area for recovering pets');
});

test('requires a name to create a wing', function () {
    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    Livewire::test(ManageSpaces::class)
        ->set('wingName', '')
        ->call('saveWing')
        ->assertHasErrors(['wingName' => 'required']);
});

test('rejects a duplicate wing name within the same shelter', function () {
    $shelter = Shelter::factory()->create();
    Wing::factory()->for($shelter)->create(['name' => 'North Wing']);
    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    Livewire::test(ManageSpaces::class)
        ->set('wingName', 'North Wing')
        ->call('saveWing')
        ->assertHasErrors(['wingName' => 'unique']);
});

test('allows the same wing name across different shelters', function () {
    $otherShelter = Shelter::factory()->create();
    Wing::factory()->for($otherShelter)->create(['name' => 'North Wing']);

    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    Livewire::test(ManageSpaces::class)
        ->set('wingName', 'North Wing')
        ->call('saveWing')
        ->assertHasNoErrors();
});

test('updates an existing wing', function () {
    $shelter = Shelter::factory()->create();
    $wing = Wing::factory()->for($shelter)->create(['name' => 'North Wing']);
    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    Livewire::test(ManageSpaces::class)
        ->call('editWing', $wing->id)
        ->assertSet('wingName', 'North Wing')
        ->set('wingName', 'North Wing (Renovated)')
        ->call('saveWing')
        ->assertHasNoErrors();

    expect($wing->fresh()->name)->toBe('North Wing (Renovated)');
});

test('soft-deletes a wing instead of removing it permanently', function () {
    $shelter = Shelter::factory()->create();
    $wing = Wing::factory()->for($shelter)->create(['name' => 'North Wing']);
    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    Livewire::test(ManageSpaces::class)
        ->call('deleteWing', $wing->id)
        ->assertDontSee('North Wing');

    expect($wing->fresh()->trashed())->toBeTrue();
});

test('soft-deletes a wing\'s cages along with it, stamping deleted_by', function () {
    $shelter = Shelter::factory()->create();
    $wing = Wing::factory()->for($shelter)->create();
    $cageOne = Cage::factory()->for($wing)->create();
    $cageTwo = Cage::factory()->for($wing)->create();
    $user = User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]);
    $this->actingAs($user);

    Livewire::test(ManageSpaces::class)->call('deleteWing', $wing->id);

    expect($cageOne->fresh()->trashed())->toBeTrue();
    expect($cageOne->fresh()->deleted_by)->toBe($user->id);
    expect($cageTwo->fresh()->trashed())->toBeTrue();
    expect($cageTwo->fresh()->deleted_by)->toBe($user->id);
});

test('cannot edit or delete a wing belonging to another shelter', function () {
    $otherShelter = Shelter::factory()->create();
    $wing = Wing::factory()->for($otherShelter)->create();

    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    expect(fn () => Livewire::test(ManageSpaces::class)->call('editWing', $wing->id))
        ->toThrow(ModelNotFoundException::class);

    expect(fn () => Livewire::test(ManageSpaces::class)->call('deleteWing', $wing->id))
        ->toThrow(ModelNotFoundException::class);
});

test('adds a cage to a wing and closes the modal', function () {
    $shelter = Shelter::factory()->create();
    $wing = Wing::factory()->for($shelter)->create();
    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    Livewire::test(ManageSpaces::class)
        ->call('createCage', $wing->id)
        ->assertSet('cageWingId', $wing->id)
        ->set('cageCode', 'C-01')
        ->set('cageCapacity', 3)
        ->call('saveCage')
        ->assertHasNoErrors()
        ->assertDispatched('modal-close', name: 'cage-form');

    $cage = Cage::query()->where('code', 'C-01')->first();
    expect($cage)->not->toBeNull();
    expect($cage->wing_id)->toBe($wing->id);
    expect($cage->capacity)->toBe(3);
});

test('requires a code and a valid capacity to add a cage', function () {
    $shelter = Shelter::factory()->create();
    $wing = Wing::factory()->for($shelter)->create();
    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    Livewire::test(ManageSpaces::class)
        ->call('createCage', $wing->id)
        ->set('cageCode', '')
        ->set('cageCapacity', 0)
        ->call('saveCage')
        ->assertHasErrors(['cageCode' => 'required', 'cageCapacity' => 'min']);
});

test('cannot add a cage to a wing belonging to another shelter', function () {
    $otherShelter = Shelter::factory()->create();
    $wing = Wing::factory()->for($otherShelter)->create();

    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    expect(fn () => Livewire::test(ManageSpaces::class)->call('createCage', $wing->id))
        ->toThrow(ModelNotFoundException::class);

    // Even if the modal's hidden field is tampered with directly, saving must
    // still fail rather than silently creating a cage on another shelter's wing.
    expect(fn () => Livewire::test(ManageSpaces::class)
        ->set('cageWingId', $wing->id)
        ->set('cageCode', 'C-01')
        ->set('cageCapacity', 1)
        ->call('saveCage'))
        ->toThrow(ModelNotFoundException::class);

    expect(Cage::query()->where('code', 'C-01')->exists())->toBeFalse();
});

test('editing a cage populates the form and displays its wing name for reference', function () {
    $shelter = Shelter::factory()->create();
    $wing = Wing::factory()->for($shelter)->create(['name' => 'North Wing']);
    $cage = Cage::factory()->for($wing)->create(['code' => 'C-01', 'capacity' => 2]);
    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    Livewire::test(ManageSpaces::class)
        ->call('editCage', $cage->id)
        ->assertSet('cageWingId', $wing->id)
        ->assertSet('cageCode', 'C-01')
        ->assertSet('cageCapacity', 2)
        ->assertSee('North Wing');
});

test('updates an existing cage and closes the modal', function () {
    $shelter = Shelter::factory()->create();
    $wing = Wing::factory()->for($shelter)->create();
    $cage = Cage::factory()->for($wing)->create(['code' => 'C-01', 'capacity' => 2]);
    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    Livewire::test(ManageSpaces::class)
        ->call('editCage', $cage->id)
        ->set('cageCode', 'C-02')
        ->set('cageCapacity', 5)
        ->call('saveCage')
        ->assertHasNoErrors()
        ->assertDispatched('modal-close', name: 'cage-form');

    expect($cage->fresh()->code)->toBe('C-02');
    expect($cage->fresh()->capacity)->toBe(5);
});

test('soft-deletes a cage instead of removing it permanently', function () {
    $shelter = Shelter::factory()->create();
    $wing = Wing::factory()->for($shelter)->create();
    $cage = Cage::factory()->for($wing)->create(['code' => 'C-01']);
    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    Livewire::test(ManageSpaces::class)
        ->call('deleteCage', $cage->id)
        ->assertDontSee('C-01');

    expect($cage->fresh()->trashed())->toBeTrue();
});

test('cannot edit or delete a cage belonging to another shelter\'s wing', function () {
    $otherShelter = Shelter::factory()->create();
    $otherWing = Wing::factory()->for($otherShelter)->create();
    $cage = Cage::factory()->for($otherWing)->create();

    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    expect(fn () => Livewire::test(ManageSpaces::class)->call('editCage', $cage->id))
        ->toThrow(ModelNotFoundException::class);

    expect(fn () => Livewire::test(ManageSpaces::class)->call('deleteCage', $cage->id))
        ->toThrow(ModelNotFoundException::class);

    expect($cage->fresh()->trashed())->toBeFalse();
});
