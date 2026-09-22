<?php

use App\Livewire\Facilities\ManageSpaces;
use App\Models\Cage;
use App\Models\Facility;
use App\Models\Pet;
use App\Models\Shelter;
use App\Models\User;
use App\Models\Wing;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Livewire;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('facilities.index'));

    $response->assertRedirect(route('login'));
});

test('admins are forbidden from viewing the page', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $this->get(route('facilities.index'))->assertForbidden();
});

test('managers and staff can view the page', function () {
    $shelter = Shelter::factory()->create();

    $manager = User::factory()->forShelter($shelter, 'manager')->create();
    $this->actingAs($manager);
    $this->get(route('facilities.index'))->assertOk();

    $staff = User::factory()->forShelter($shelter, 'staff')->create();
    $this->actingAs($staff);
    $this->get(route('facilities.index'))->assertOk();
});

test('shows a placeholder message when there are no facilities', function () {
    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    $this->get(route('facilities.index'))->assertSee(__('No facilities registered'));
});

test('lists only facilities belonging to the acting user\'s shelter, with their wings and cages', function () {
    $shelter = Shelter::factory()->create();
    $otherShelter = Shelter::factory()->create();

    $facility = Facility::factory()->for($shelter)->create(['name' => 'Main Building']);
    $wing = Wing::factory()->for($facility)->create(['name' => 'North Wing']);
    Cage::factory()->for($wing)->create(['code' => 'C-01', 'capacity' => 3]);

    Facility::factory()->for($otherShelter)->create(['name' => 'Other Shelter Building']);

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    Livewire::test(ManageSpaces::class)
        ->assertSee('Main Building')
        ->assertSee('North Wing')
        ->assertSee('C-01')
        ->assertDontSee('Other Shelter Building');
});

test('creates a new facility scoped to the acting user\'s shelter and closes the modal', function () {
    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->forShelter($shelter, 'manager')->create());

    Livewire::test(ManageSpaces::class)
        ->set('facilityName', 'Main Building')
        ->set('facilityAddress', 'Rua das Flores 1')
        ->set('facilityPostalCode', '1000-001')
        ->set('facilityCity', 'Lisboa')
        ->set('facilityNotes', 'Main shelter building')
        ->call('saveFacility')
        ->assertHasNoErrors()
        ->assertDispatched('modal-close', name: 'facility-form');

    $facility = Facility::query()->where('name', 'Main Building')->first();
    expect($facility)->not->toBeNull();
    expect($facility->shelter_id)->toBe($shelter->id);
    expect($facility->address)->toBe('Rua das Flores 1');
    expect($facility->city)->toBe('Lisboa');
});

test('requires a name to create a facility', function () {
    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->forShelter($shelter, 'manager')->create());

    Livewire::test(ManageSpaces::class)
        ->set('facilityName', '')
        ->call('saveFacility')
        ->assertHasErrors(['facilityName' => 'required']);
});

test('rejects a duplicate facility name within the same shelter', function () {
    $shelter = Shelter::factory()->create();
    Facility::factory()->for($shelter)->create(['name' => 'Main Building']);
    $this->actingAs(User::factory()->forShelter($shelter, 'manager')->create());

    Livewire::test(ManageSpaces::class)
        ->set('facilityName', 'Main Building')
        ->call('saveFacility')
        ->assertHasErrors(['facilityName' => 'unique']);
});

test('allows the same facility name across different shelters', function () {
    $otherShelter = Shelter::factory()->create();
    Facility::factory()->for($otherShelter)->create(['name' => 'Main Building']);

    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->forShelter($shelter, 'manager')->create());

    Livewire::test(ManageSpaces::class)
        ->set('facilityName', 'Main Building')
        ->call('saveFacility')
        ->assertHasNoErrors();
});

test('updates an existing facility', function () {
    $shelter = Shelter::factory()->create();
    $facility = Facility::factory()->for($shelter)->create(['name' => 'Main Building']);
    $this->actingAs(User::factory()->forShelter($shelter, 'manager')->create());

    Livewire::test(ManageSpaces::class)
        ->call('editFacility', $facility->id)
        ->assertSet('facilityName', 'Main Building')
        ->set('facilityName', 'Main Building (Renovated)')
        ->call('saveFacility')
        ->assertHasNoErrors();

    expect($facility->fresh()->name)->toBe('Main Building (Renovated)');
});

test('soft-deletes a facility instead of removing it permanently', function () {
    $shelter = Shelter::factory()->create();
    $facility = Facility::factory()->for($shelter)->create(['name' => 'Main Building']);
    $this->actingAs(User::factory()->forShelter($shelter, 'manager')->create());

    Livewire::test(ManageSpaces::class)
        ->call('deleteFacility', $facility->id)
        ->assertDontSee('Main Building');

    expect($facility->fresh()->trashed())->toBeTrue();
});

test('soft-deletes a facility\'s wings and cages along with it, stamping deleted_by', function () {
    $shelter = Shelter::factory()->create();
    $facility = Facility::factory()->for($shelter)->create();
    $wing = Wing::factory()->for($facility)->create();
    $cage = Cage::factory()->for($wing)->create();
    $user = User::factory()->forShelter($shelter, 'manager')->create();
    $this->actingAs($user);

    Livewire::test(ManageSpaces::class)->call('deleteFacility', $facility->id);

    expect($wing->fresh()->trashed())->toBeTrue();
    expect($wing->fresh()->deleted_by)->toBe($user->id);
    expect($cage->fresh()->trashed())->toBeTrue();
    expect($cage->fresh()->deleted_by)->toBe($user->id);
});

test('cannot edit or delete a facility belonging to another shelter', function () {
    $otherShelter = Shelter::factory()->create();
    $facility = Facility::factory()->for($otherShelter)->create();

    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->forShelter($shelter, 'manager')->create());

    expect(fn () => Livewire::test(ManageSpaces::class)->call('editFacility', $facility->id))
        ->toThrow(ModelNotFoundException::class);

    expect(fn () => Livewire::test(ManageSpaces::class)->call('deleteFacility', $facility->id))
        ->toThrow(ModelNotFoundException::class);
});

test('adds a wing to a facility and closes the modal', function () {
    $shelter = Shelter::factory()->create();
    $facility = Facility::factory()->for($shelter)->create();
    $this->actingAs(User::factory()->forShelter($shelter, 'manager')->create());

    Livewire::test(ManageSpaces::class)
        ->call('createWing', $facility->id)
        ->assertSet('wingFacilityId', $facility->id)
        ->set('wingName', 'North Wing')
        ->set('wingDescription', 'Quiet area for recovering pets')
        ->call('saveWing')
        ->assertHasNoErrors()
        ->assertDispatched('modal-close', name: 'wing-form');

    $wing = Wing::query()->where('name', 'North Wing')->first();
    expect($wing)->not->toBeNull();
    expect($wing->facility_id)->toBe($facility->id);
    expect($wing->description)->toBe('Quiet area for recovering pets');
});

test('requires a name to create a wing', function () {
    $shelter = Shelter::factory()->create();
    $facility = Facility::factory()->for($shelter)->create();
    $this->actingAs(User::factory()->forShelter($shelter, 'manager')->create());

    Livewire::test(ManageSpaces::class)
        ->call('createWing', $facility->id)
        ->set('wingName', '')
        ->call('saveWing')
        ->assertHasErrors(['wingName' => 'required']);
});

test('rejects a duplicate wing name within the same facility', function () {
    $shelter = Shelter::factory()->create();
    $facility = Facility::factory()->for($shelter)->create();
    Wing::factory()->for($facility)->create(['name' => 'North Wing']);
    $this->actingAs(User::factory()->forShelter($shelter, 'manager')->create());

    Livewire::test(ManageSpaces::class)
        ->call('createWing', $facility->id)
        ->set('wingName', 'North Wing')
        ->call('saveWing')
        ->assertHasErrors(['wingName' => 'unique']);
});

test('allows the same wing name across different facilities', function () {
    $shelter = Shelter::factory()->create();
    $otherFacility = Facility::factory()->for($shelter)->create();
    Wing::factory()->for($otherFacility)->create(['name' => 'North Wing']);

    $facility = Facility::factory()->for($shelter)->create();
    $this->actingAs(User::factory()->forShelter($shelter, 'manager')->create());

    Livewire::test(ManageSpaces::class)
        ->call('createWing', $facility->id)
        ->set('wingName', 'North Wing')
        ->call('saveWing')
        ->assertHasNoErrors();
});

test('cannot add a wing to a facility belonging to another shelter', function () {
    $otherShelter = Shelter::factory()->create();
    $facility = Facility::factory()->for($otherShelter)->create();

    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->forShelter($shelter, 'manager')->create());

    expect(fn () => Livewire::test(ManageSpaces::class)->call('createWing', $facility->id))
        ->toThrow(ModelNotFoundException::class);

    // Even if the modal's hidden field is tampered with directly, saving must
    // still fail rather than silently creating a wing on another shelter's facility.
    expect(fn () => Livewire::test(ManageSpaces::class)
        ->set('wingFacilityId', $facility->id)
        ->set('wingName', 'North Wing')
        ->call('saveWing'))
        ->toThrow(ModelNotFoundException::class);

    expect(Wing::query()->where('name', 'North Wing')->exists())->toBeFalse();
});

test('updates an existing wing', function () {
    $shelter = Shelter::factory()->create();
    $facility = Facility::factory()->for($shelter)->create();
    $wing = Wing::factory()->for($facility)->create(['name' => 'North Wing']);
    $this->actingAs(User::factory()->forShelter($shelter, 'manager')->create());

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
    $facility = Facility::factory()->for($shelter)->create();
    $wing = Wing::factory()->for($facility)->create(['name' => 'North Wing']);
    $this->actingAs(User::factory()->forShelter($shelter, 'manager')->create());

    Livewire::test(ManageSpaces::class)
        ->call('deleteWing', $wing->id)
        ->assertDontSee('North Wing');

    expect($wing->fresh()->trashed())->toBeTrue();
});

test('soft-deletes a wing\'s cages along with it, stamping deleted_by', function () {
    $shelter = Shelter::factory()->create();
    $facility = Facility::factory()->for($shelter)->create();
    $wing = Wing::factory()->for($facility)->create();
    $cageOne = Cage::factory()->for($wing)->create();
    $cageTwo = Cage::factory()->for($wing)->create();
    $user = User::factory()->forShelter($shelter, 'manager')->create();
    $this->actingAs($user);

    Livewire::test(ManageSpaces::class)->call('deleteWing', $wing->id);

    expect($cageOne->fresh()->trashed())->toBeTrue();
    expect($cageOne->fresh()->deleted_by)->toBe($user->id);
    expect($cageTwo->fresh()->trashed())->toBeTrue();
    expect($cageTwo->fresh()->deleted_by)->toBe($user->id);
});

test('cannot edit or delete a wing belonging to another shelter\'s facility', function () {
    $otherShelter = Shelter::factory()->create();
    $otherFacility = Facility::factory()->for($otherShelter)->create();
    $wing = Wing::factory()->for($otherFacility)->create();

    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->forShelter($shelter, 'manager')->create());

    expect(fn () => Livewire::test(ManageSpaces::class)->call('editWing', $wing->id))
        ->toThrow(ModelNotFoundException::class);

    expect(fn () => Livewire::test(ManageSpaces::class)->call('deleteWing', $wing->id))
        ->toThrow(ModelNotFoundException::class);
});

test('adds a cage to a wing and closes the modal', function () {
    $shelter = Shelter::factory()->create();
    $facility = Facility::factory()->for($shelter)->create();
    $wing = Wing::factory()->for($facility)->create();
    $this->actingAs(User::factory()->forShelter($shelter, 'manager')->create());

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
    $facility = Facility::factory()->for($shelter)->create();
    $wing = Wing::factory()->for($facility)->create();
    $this->actingAs(User::factory()->forShelter($shelter, 'manager')->create());

    Livewire::test(ManageSpaces::class)
        ->call('createCage', $wing->id)
        ->set('cageCode', '')
        ->set('cageCapacity', 0)
        ->call('saveCage')
        ->assertHasErrors(['cageCode' => 'required', 'cageCapacity' => 'min']);
});

test('cannot add a cage to a wing belonging to another shelter', function () {
    $otherShelter = Shelter::factory()->create();
    $otherFacility = Facility::factory()->for($otherShelter)->create();
    $wing = Wing::factory()->for($otherFacility)->create();

    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->forShelter($shelter, 'manager')->create());

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
    $facility = Facility::factory()->for($shelter)->create();
    $wing = Wing::factory()->for($facility)->create(['name' => 'North Wing']);
    $cage = Cage::factory()->for($wing)->create(['code' => 'C-01', 'capacity' => 2]);
    $this->actingAs(User::factory()->forShelter($shelter, 'manager')->create());

    Livewire::test(ManageSpaces::class)
        ->call('editCage', $cage->id)
        ->assertSet('cageWingId', $wing->id)
        ->assertSet('cageCode', 'C-01')
        ->assertSet('cageCapacity', 2)
        ->assertSee('North Wing');
});

test('updates an existing cage and closes the modal', function () {
    $shelter = Shelter::factory()->create();
    $facility = Facility::factory()->for($shelter)->create();
    $wing = Wing::factory()->for($facility)->create();
    $cage = Cage::factory()->for($wing)->create(['code' => 'C-01', 'capacity' => 2]);
    $this->actingAs(User::factory()->forShelter($shelter, 'manager')->create());

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
    $facility = Facility::factory()->for($shelter)->create();
    $wing = Wing::factory()->for($facility)->create();
    $cage = Cage::factory()->for($wing)->create(['code' => 'C-01']);
    $this->actingAs(User::factory()->forShelter($shelter, 'manager')->create());

    Livewire::test(ManageSpaces::class)
        ->call('deleteCage', $cage->id)
        ->assertDontSee('C-01');

    expect($cage->fresh()->trashed())->toBeTrue();
});

test('cannot edit or delete a cage belonging to another shelter\'s wing', function () {
    $otherShelter = Shelter::factory()->create();
    $otherFacility = Facility::factory()->for($otherShelter)->create();
    $otherWing = Wing::factory()->for($otherFacility)->create();
    $cage = Cage::factory()->for($otherWing)->create();

    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->forShelter($shelter, 'manager')->create());

    expect(fn () => Livewire::test(ManageSpaces::class)->call('editCage', $cage->id))
        ->toThrow(ModelNotFoundException::class);

    expect(fn () => Livewire::test(ManageSpaces::class)->call('deleteCage', $cage->id))
        ->toThrow(ModelNotFoundException::class);

    expect($cage->fresh()->trashed())->toBeFalse();
});

test('staff cannot create, edit, or delete facilities, wings, or cages', function () {
    $shelter = Shelter::factory()->create();
    $facility = Facility::factory()->for($shelter)->create();
    $wing = Wing::factory()->for($facility)->create();
    $cage = Cage::factory()->for($wing)->create();
    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    Livewire::test(ManageSpaces::class)->call('createFacility')->assertForbidden();
    Livewire::test(ManageSpaces::class)->call('editFacility', $facility->id)->assertForbidden();
    Livewire::test(ManageSpaces::class)->call('saveFacility')->assertForbidden();
    Livewire::test(ManageSpaces::class)->call('deleteFacility', $facility->id)->assertForbidden();

    Livewire::test(ManageSpaces::class)->call('createWing', $facility->id)->assertForbidden();
    Livewire::test(ManageSpaces::class)->call('editWing', $wing->id)->assertForbidden();
    Livewire::test(ManageSpaces::class)->call('saveWing')->assertForbidden();
    Livewire::test(ManageSpaces::class)->call('deleteWing', $wing->id)->assertForbidden();

    Livewire::test(ManageSpaces::class)->call('createCage', $wing->id)->assertForbidden();
    Livewire::test(ManageSpaces::class)->call('editCage', $cage->id)->assertForbidden();
    Livewire::test(ManageSpaces::class)->call('saveCage')->assertForbidden();
    Livewire::test(ManageSpaces::class)->call('deleteCage', $cage->id)->assertForbidden();

    expect($facility->fresh()->trashed())->toBeFalse();
    expect($wing->fresh()->trashed())->toBeFalse();
    expect($cage->fresh()->trashed())->toBeFalse();
});

test('staff do not see create, edit, or delete controls on the facilities page', function () {
    $shelter = Shelter::factory()->create();
    $facility = Facility::factory()->for($shelter)->create();
    $wing = Wing::factory()->for($facility)->create();
    Cage::factory()->for($wing)->create();
    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    $this->get(route('facilities.index'))
        ->assertDontSee(__('Create'))
        ->assertDontSee(__('Add Wing'))
        ->assertDontSee(__('Add Cage'));
});

test('shows the available space of each cage, counting only pets still housed there', function () {
    $shelter = Shelter::factory()->create();
    $facility = Facility::factory()->for($shelter)->create();
    $wing = Wing::factory()->for($facility)->create();
    $cage = Cage::factory()->for($wing)->create(['capacity' => 3]);

    Pet::factory()->for($shelter)->create(['cage_id' => $cage->id]);
    Pet::factory()->for($shelter)->create(['cage_id' => $cage->id, 'status' => 'adopted']);
    Pet::factory()->for($shelter)->create(['cage_id' => $cage->id, 'date_of_death' => now()]);

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    Livewire::test(ManageSpaces::class)
        ->assertSee('2 of 3 free');
});

test('staff can view the pets still housed in a cage', function () {
    $shelter = Shelter::factory()->create();
    $facility = Facility::factory()->for($shelter)->create();
    $wing = Wing::factory()->for($facility)->create();
    $cage = Cage::factory()->for($wing)->create();

    Pet::factory()->for($shelter)->create(['cage_id' => $cage->id, 'name' => 'Rexinho']);
    Pet::factory()->for($shelter)->create(['cage_id' => $cage->id, 'name' => 'Adotadinho', 'status' => 'adopted']);

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    Livewire::test(ManageSpaces::class)
        ->assertDontSee('Rexinho')
        ->call('viewCagePets', $cage->id)
        ->assertSee('Rexinho')
        ->assertDontSee('Adotadinho');
});

test('cannot view the pets of a cage belonging to another shelter\'s wing', function () {
    $otherShelter = Shelter::factory()->create();
    $otherFacility = Facility::factory()->for($otherShelter)->create();
    $otherWing = Wing::factory()->for($otherFacility)->create();
    $cage = Cage::factory()->for($otherWing)->create();

    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    expect(fn () => Livewire::test(ManageSpaces::class)->call('viewCagePets', $cage->id))
        ->toThrow(ModelNotFoundException::class);
});
