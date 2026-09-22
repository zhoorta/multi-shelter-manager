<?php

use App\Livewire\Pets\ManagePets;
use App\Models\Adoption;
use App\Models\Cage;
use App\Models\Facility;
use App\Models\Pet;
use App\Models\Shelter;
use App\Models\Size;
use App\Models\Species;
use App\Models\User;
use App\Models\Wing;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Livewire;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('pets.index'));

    $response->assertRedirect(route('login'));
});

test('admins are forbidden from viewing the page', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $this->get(route('pets.index'))->assertForbidden();
});

test('managers and staff can view the page', function () {
    $shelter = Shelter::factory()->create();

    $manager = User::factory()->forShelter($shelter, 'manager')->create();
    $this->actingAs($manager);
    $this->get(route('pets.index'))->assertOk();

    $staff = User::factory()->forShelter($shelter, 'staff')->create();
    $this->actingAs($staff);
    $this->get(route('pets.index'))->assertOk();
});

test('shows a placeholder message when there are no pets', function () {
    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    $this->get(route('pets.index'))->assertSee(__('No pets registered'));
});

test('lists only pets belonging to the acting user\'s shelter', function () {
    $shelter = Shelter::factory()->create();
    $otherShelter = Shelter::factory()->create();

    Pet::factory()->for($shelter)->create(['name' => 'Rex']);
    Pet::factory()->for($otherShelter)->create(['name' => 'Other Shelter Dog']);

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    Livewire::test(ManagePets::class)
        ->assertSee('Rex')
        ->assertDontSee('Other Shelter Dog');
});

test('filters pets by name', function () {
    $shelter = Shelter::factory()->create();
    Pet::factory()->for($shelter)->create(['name' => 'Rex']);
    Pet::factory()->for($shelter)->create(['name' => 'Bella']);
    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    Livewire::test(ManagePets::class)
        ->set('search', 'Rex')
        ->assertSee('Rex')
        ->assertDontSee('Bella');
});

test('filters pets by microchip', function () {
    $shelter = Shelter::factory()->create();
    Pet::factory()->for($shelter)->create(['name' => 'Rex', 'chip' => '985121000123456']);
    Pet::factory()->for($shelter)->create(['name' => 'Bella', 'chip' => '985121000987654']);
    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    Livewire::test(ManagePets::class)
        ->set('search', '000123456')
        ->assertSee('Rex')
        ->assertDontSee('Bella');
});

test('filters pets by ref', function () {
    $shelter = Shelter::factory()->create();
    $rex = Pet::factory()->for($shelter)->create(['name' => 'Rex']);
    Pet::factory()->for($shelter)->create(['name' => 'Bella']);
    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    Livewire::test(ManagePets::class)
        ->set('search', $rex->ref)
        ->assertSee('Rex')
        ->assertDontSee('Bella');
});

test('filters pets by internal notes', function () {
    $shelter = Shelter::factory()->create();
    Pet::factory()->for($shelter)->create(['name' => 'Rex', 'internal_notes' => 'Aggressive with other dogs']);
    Pet::factory()->for($shelter)->create(['name' => 'Bella', 'internal_notes' => 'Very calm']);
    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    Livewire::test(ManagePets::class)
        ->set('search', 'Aggressive')
        ->assertSee('Rex')
        ->assertDontSee('Bella');
});

test('filters pets by cage', function () {
    $shelter = Shelter::factory()->create();
    $facility = Facility::factory()->for($shelter)->create();
    $wing = Wing::factory()->for($facility)->create();
    $cage = Cage::factory()->for($wing)->create();
    $otherCage = Cage::factory()->for($wing)->create();

    Pet::factory()->for($shelter)->create(['name' => 'Rex', 'cage_id' => $cage->id]);
    Pet::factory()->for($shelter)->create(['name' => 'Bella', 'cage_id' => $otherCage->id]);

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    Livewire::test(ManagePets::class)
        ->set('locationFilter', 'cage:'.$cage->id)
        ->assertSee('Rex')
        ->assertDontSee('Bella');
});

test('filters pets by every cage under a wing', function () {
    $shelter = Shelter::factory()->create();
    $facility = Facility::factory()->for($shelter)->create();
    $wing = Wing::factory()->for($facility)->create();
    $otherWing = Wing::factory()->for($facility)->create();
    $cageOne = Cage::factory()->for($wing)->create();
    $cageTwo = Cage::factory()->for($wing)->create();
    $otherCage = Cage::factory()->for($otherWing)->create();

    Pet::factory()->for($shelter)->create(['name' => 'Rex', 'cage_id' => $cageOne->id]);
    Pet::factory()->for($shelter)->create(['name' => 'Fido', 'cage_id' => $cageTwo->id]);
    Pet::factory()->for($shelter)->create(['name' => 'Bella', 'cage_id' => $otherCage->id]);

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    Livewire::test(ManagePets::class)
        ->set('locationFilter', 'wing:'.$wing->id)
        ->assertSee('Rex')
        ->assertSee('Fido')
        ->assertDontSee('Bella');
});

test('filters pets by every cage under a facility', function () {
    $shelter = Shelter::factory()->create();
    $facility = Facility::factory()->for($shelter)->create();
    $otherFacility = Facility::factory()->for($shelter)->create();
    $wingOne = Wing::factory()->for($facility)->create();
    $wingTwo = Wing::factory()->for($facility)->create();
    $otherWing = Wing::factory()->for($otherFacility)->create();
    $cageOne = Cage::factory()->for($wingOne)->create();
    $cageTwo = Cage::factory()->for($wingTwo)->create();
    $otherCage = Cage::factory()->for($otherWing)->create();

    Pet::factory()->for($shelter)->create(['name' => 'Rex', 'cage_id' => $cageOne->id]);
    Pet::factory()->for($shelter)->create(['name' => 'Fido', 'cage_id' => $cageTwo->id]);
    Pet::factory()->for($shelter)->create(['name' => 'Bella', 'cage_id' => $otherCage->id]);

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    Livewire::test(ManagePets::class)
        ->set('locationFilter', 'facility:'.$facility->id)
        ->assertSee('Rex')
        ->assertSee('Fido')
        ->assertDontSee('Bella');
});

test('location filter only lists facilities/wings/cages belonging to the acting user\'s shelter', function () {
    $shelter = Shelter::factory()->create();
    $facility = Facility::factory()->for($shelter)->create(['name' => 'North Campus']);
    $wing = Wing::factory()->for($facility)->create(['name' => 'Dog Wing']);
    Cage::factory()->for($wing)->create(['code' => 'D12']);

    $otherShelter = Shelter::factory()->create();
    $otherFacility = Facility::factory()->for($otherShelter)->create(['name' => 'South Campus']);
    $otherWing = Wing::factory()->for($otherFacility)->create(['name' => 'Cat Wing']);
    Cage::factory()->for($otherWing)->create(['code' => 'X99']);

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    $component = Livewire::test(ManagePets::class);

    expect($component->get('facilities')->pluck('id'))->toEqual(collect([$facility->id]));
});

test('location filter shows each cage\'s available space and color', function () {
    $shelter = Shelter::factory()->create();
    $facility = Facility::factory()->for($shelter)->create();
    $wing = Wing::factory()->for($facility)->create();

    $greenCage = Cage::factory()->for($wing)->create(['code' => 'G1', 'capacity' => 10]);
    Pet::factory()->for($greenCage, 'cage')->count(2)->create(['shelter_id' => $shelter->id, 'status' => 'available']);

    $redCage = Cage::factory()->for($wing)->create(['code' => 'R1', 'capacity' => 10]);
    Pet::factory()->for($redCage, 'cage')->count(10)->create(['shelter_id' => $shelter->id, 'status' => 'available']);

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    $cages = Livewire::test(ManagePets::class)
        ->assertSee(__(':available of :capacity free', ['available' => 8, 'capacity' => 10]))
        ->assertSee(__(':available of :capacity free', ['available' => 0, 'capacity' => 10]))
        ->get('facilities')->first()->wings->first()->cages->keyBy('id');

    expect($cages[$greenCage->id]->availability_color)->toBe('green')
        ->and($cages[$redCage->id]->availability_color)->toBe('red');
});

test('filters pets by status', function () {
    $shelter = Shelter::factory()->create();
    Pet::factory()->for($shelter)->create(['name' => 'Rex', 'status' => 'available']);
    Pet::factory()->for($shelter)->create(['name' => 'Bella', 'status' => 'not_available']);
    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    Livewire::test(ManagePets::class)
        ->set('statusFilter', 'not_available')
        ->assertSee('Bella')
        ->assertDontSee('Rex');
});

test('filters pets with no age defined', function () {
    $shelter = Shelter::factory()->create();
    Pet::factory()->for($shelter)->create(['name' => 'Rex', 'birth_date' => null]);
    Pet::factory()->for($shelter)->create(['name' => 'Bella', 'birth_date' => '2023-01-01']);
    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    Livewire::test(ManagePets::class)
        ->set('missingDataFilter', 'no_age')
        ->assertSee('Rex')
        ->assertDontSee('Bella');
});

test('filters pets with no photo', function () {
    $shelter = Shelter::factory()->create();
    $rex = Pet::factory()->for($shelter)->create(['name' => 'Rex']);
    $bella = Pet::factory()->for($shelter)->create(['name' => 'Bella']);
    $bella->images()->create(['image_path' => 'pets/bella.jpg', 'is_main' => true]);
    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    Livewire::test(ManagePets::class)
        ->set('missingDataFilter', 'no_photo')
        ->assertSee('Rex')
        ->assertDontSee('Bella');
});

test('filters pets with no checkin date', function () {
    $shelter = Shelter::factory()->create();
    Pet::factory()->for($shelter)->create(['name' => 'Rex', 'checkin_date' => null]);
    Pet::factory()->for($shelter)->create(['name' => 'Bella', 'checkin_date' => '2023-01-01']);
    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    Livewire::test(ManagePets::class)
        ->set('missingDataFilter', 'no_checkin_date')
        ->assertSee('Rex')
        ->assertDontSee('Bella');
});

test('filters pets with no location defined', function () {
    $shelter = Shelter::factory()->create();
    $facility = Facility::factory()->for($shelter)->create();
    $wing = Wing::factory()->for($facility)->create();
    $cage = Cage::factory()->for($wing)->create();

    Pet::factory()->for($shelter)->create(['name' => 'Rex', 'cage_id' => null]);
    Pet::factory()->for($shelter)->create(['name' => 'Bella', 'cage_id' => $cage->id]);
    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    Livewire::test(ManagePets::class)
        ->set('missingDataFilter', 'no_location')
        ->assertSee('Rex')
        ->assertDontSee('Bella');
});

test('excludes adopted and deceased pets from the no location defined filter', function () {
    $shelter = Shelter::factory()->create();

    Pet::factory()->for($shelter)->create(['name' => 'Rex', 'cage_id' => null, 'status' => 'adopted']);
    Pet::factory()->for($shelter)->create(['name' => 'Bella', 'cage_id' => null, 'status' => 'deceased']);
    Pet::factory()->for($shelter)->create(['name' => 'Fido', 'cage_id' => null, 'status' => 'available']);
    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    Livewire::test(ManagePets::class)
        ->set('missingDataFilter', 'no_location')
        ->assertSee('Fido')
        ->assertDontSee('Rex')
        ->assertDontSee('Bella');
});

test('shows the adoption date for adopted pets', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create(['name' => 'Rex', 'status' => 'adopted']);
    Adoption::factory()->for($pet)->create(['adoption_date' => '2026-02-10']);

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    Livewire::test(ManagePets::class)
        ->assertSeeInOrder(['Rex', 'Adopted', 'at', '10/02/2026'])
        ->assertSeeHtml('<strong>Adopted</strong>')
        ->assertDontSeeHtml('<strong>Deceased</strong>');
});

test('does not show an adoption date for pets that are not adopted', function () {
    $shelter = Shelter::factory()->create();
    Pet::factory()->for($shelter)->create(['name' => 'Rex', 'status' => 'available']);

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    Livewire::test(ManagePets::class)
        ->assertDontSeeHtml('<strong>Adopted</strong>')
        ->assertDontSeeHtml('<strong>Deceased</strong>');
});

test('shows the death date instead of the adoption date for deceased pets', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create([
        'name' => 'Rex',
        'status' => 'adopted',
        'date_of_death' => '2026-03-15',
    ]);
    Adoption::factory()->for($pet)->create(['adoption_date' => '2026-02-10']);

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    Livewire::test(ManagePets::class)
        ->assertSeeInOrder(['Rex', 'Deceased', 'at', '15/03/2026'])
        ->assertSeeHtml('<strong>Deceased</strong>')
        ->assertDontSeeHtml('<strong>Adopted</strong>')
        ->assertDontSee('10/02/2026');
});

test('filters pets by species', function () {
    $shelter = Shelter::factory()->create();
    $species = Species::factory()->create();
    $otherSpecies = Species::factory()->create();

    Pet::factory()->for($shelter)->for($species)->create(['name' => 'Rex']);
    Pet::factory()->for($shelter)->for($otherSpecies)->create(['name' => 'Bella']);
    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    Livewire::test(ManagePets::class)
        ->set('speciesFilter', (string) $species->id)
        ->assertSee('Rex')
        ->assertDontSee('Bella');
});

test('shows the selected species\' plural name as the heading when arriving from the sidebar', function () {
    $shelter = Shelter::factory()->create();
    $species = Species::factory()->create(['name' => 'Dog', 'name_plural' => 'Dogs']);
    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    Livewire::withQueryParams(['speciesFilter' => (string) $species->id])
        ->test(ManagePets::class)
        ->assertSee('Dogs');
});

test('carries the selected species over to the create link so a new pet starts locked to it', function () {
    $shelter = Shelter::factory()->create();
    $species = Species::factory()->create();
    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    Livewire::withQueryParams(['speciesFilter' => (string) $species->id])
        ->test(ManagePets::class)
        ->assertSeeHtml(route('pets.create', ['species' => $species->id]));
});

test('shows the accommodation as facility, then wing, then cage code', function () {
    $shelter = Shelter::factory()->create();
    $facility = Facility::factory()->for($shelter)->create(['name' => 'North Campus']);
    $wing = Wing::factory()->for($facility)->create(['name' => 'Dog Wing']);
    $cage = Cage::factory()->for($wing)->create(['code' => 'D12']);
    Pet::factory()->for($shelter)->create(['name' => 'Rex', 'cage_id' => $cage->id]);

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    Livewire::test(ManagePets::class)
        ->assertSeeInOrder(['Rex', 'North Campus', 'Dog Wing', 'D12']);
});

test('shows a dash in the accommodation column when the pet has no cage assigned', function () {
    $shelter = Shelter::factory()->create();
    Pet::factory()->for($shelter)->create(['name' => 'Rex', 'cage_id' => null]);

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    Livewire::test(ManagePets::class)
        ->assertSeeInOrder(['Rex', '-'])
        ->assertDontSee('No Facility Assigned')
        ->assertDontSee('No Wing Assigned')
        ->assertDontSee('No Cage Assigned');
});

test('shows the size after the breed name in the characteristics column', function () {
    $shelter = Shelter::factory()->create();
    $species = Species::factory()->create();
    $size = Size::factory()->for($species)->create(['name' => 'Grande']);
    $pet = Pet::factory()->for($shelter)->for($species)->create(['name' => 'Rex', 'size_id' => $size->id]);

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    Livewire::test(ManagePets::class)
        ->assertSeeInOrder(['Rex', $pet->breed->name, 'Grande']);
});

test('still shows the breed name and does not error after the breed and species are soft-deleted', function () {
    $shelter = Shelter::factory()->create();
    $species = Species::factory()->create();
    $pet = Pet::factory()->for($shelter)->for($species)->create(['name' => 'Rex']);
    $breedName = $pet->breed->name;

    $pet->breed->delete();
    $species->delete();

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    Livewire::test(ManagePets::class)
        ->assertOk()
        ->assertSee('Rex')
        ->assertSee($breedName);
});

test('shows "Pure breed" right after the breed name when the pet is a pure breed', function () {
    $shelter = Shelter::factory()->create();
    $species = Species::factory()->create(['has_pure_breed_field' => true]);
    $pet = Pet::factory()->for($shelter)->for($species)->create(['name' => 'Rex', 'is_pure_breed' => true]);

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    Livewire::test(ManagePets::class)
        ->assertSeeInOrder(['Rex', $pet->breed->name, 'Pure breed']);
});

test('does not show "Pure breed" when the pet is not a pure breed', function () {
    $shelter = Shelter::factory()->create();
    $species = Species::factory()->create(['has_pure_breed_field' => true]);
    Pet::factory()->for($shelter)->for($species)->create(['name' => 'Rex', 'is_pure_breed' => false]);

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    Livewire::test(ManagePets::class)
        ->assertDontSee('Pure breed');
});

test('does not show "Pure breed" when the species has the field disabled, even if the pet is flagged as pure breed', function () {
    $shelter = Shelter::factory()->create();
    $species = Species::factory()->create(['has_pure_breed_field' => false]);
    Pet::factory()->for($shelter)->for($species)->create(['name' => 'Rex', 'is_pure_breed' => true]);

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    Livewire::test(ManagePets::class)
        ->assertDontSee('Pure breed');
});

test('paginates pets 20 per page', function () {
    $shelter = Shelter::factory()->create();
    $species = Species::factory()->create();
    Pet::factory()->for($shelter)->for($species)->count(25)->sequence(fn ($sequence) => ['name' => 'Pet '.$sequence->index])->create();
    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    $component = Livewire::test(ManagePets::class);

    expect($component->get('pets')->count())->toBe(20);
    expect($component->get('pets')->total())->toBe(25);

    $component->call('nextPage');

    expect($component->get('pets')->count())->toBe(5);
});

test('soft-deletes a pet instead of removing it permanently', function () {
    $shelter = Shelter::factory()->create();
    $user = User::factory()->forShelter($shelter, 'staff')->create();
    $this->actingAs($user);

    $pet = Pet::factory()->for($shelter)->create(['name' => 'Rex']);

    Livewire::test(ManagePets::class)
        ->call('deletePet', $pet->id)
        ->assertDontSee('Rex');

    expect($pet->fresh()->trashed())->toBeTrue();
    expect($pet->fresh()->deleted_by)->toBe($user->id);
});

test('cannot delete a pet belonging to another shelter', function () {
    $otherShelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($otherShelter)->create(['name' => 'Rex']);

    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    expect(fn () => Livewire::test(ManagePets::class)->call('deletePet', $pet->id))
        ->toThrow(ModelNotFoundException::class);

    expect($pet->fresh()->trashed())->toBeFalse();
});
