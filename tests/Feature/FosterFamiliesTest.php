<?php

use App\Livewire\Dashboard;
use App\Livewire\Facilities\ManageSpaces;
use App\Livewire\Reports\ShelterReports;
use App\Models\Cage;
use App\Models\Facility;
use App\Models\Pet;
use App\Models\Shelter;
use App\Models\User;
use App\Models\Volunteer;
use App\Models\Wing;
use Livewire\Livewire;

beforeEach(function () {
    $this->shelter = Shelter::factory()->create();
    $this->manager = User::factory()->forShelter($this->shelter, 'manager')->create();
    $this->facility = Facility::factory()->for($this->shelter)->create(['name' => 'Foster homes']);
});

test('managers can mark a wing as a foster families wing', function () {
    Livewire::actingAs($this->manager)
        ->test(ManageSpaces::class)
        ->call('createWing', $this->facility->id)
        ->set('wingName', 'FAT')
        ->set('wingIsFoster', true)
        ->call('saveWing')
        ->assertHasNoErrors()
        ->assertSee('Foster families');

    expect(Wing::query()->where('name', 'FAT')->value('is_foster'))->toBeTrue();
});

test('a foster family cage keeps its contact volunteer, which must belong to the shelter', function () {
    $wing = Wing::factory()->foster()->for($this->facility)->create();
    $volunteer = Volunteer::factory()->for($this->shelter)->create(['name' => 'Sónia Melo']);
    $otherShelterVolunteer = Volunteer::factory()->create();

    Livewire::actingAs($this->manager)
        ->test(ManageSpaces::class)
        ->call('createCage', $wing->id)
        ->assertSee('Contact (volunteer)')
        ->set('cageCode', 'Sónia Melo')
        ->set('cageVolunteerId', $otherShelterVolunteer->id)
        ->call('saveCage')
        ->assertHasErrors(['cageVolunteerId'])
        ->set('cageVolunteerId', $volunteer->id)
        ->call('saveCage')
        ->assertHasNoErrors();

    expect(Cage::query()->where('code', 'Sónia Melo')->value('volunteer_id'))->toBe($volunteer->id);
});

test('a contact volunteer is not kept on cages of a regular wing', function () {
    $wing = Wing::factory()->for($this->facility)->create();
    $volunteer = Volunteer::factory()->for($this->shelter)->create();

    Livewire::actingAs($this->manager)
        ->test(ManageSpaces::class)
        ->call('createCage', $wing->id)
        ->assertDontSee('Contact (volunteer)')
        ->set('cageCode', 'A1')
        ->set('cageVolunteerId', $volunteer->id)
        ->call('saveCage')
        ->assertHasNoErrors();

    expect(Cage::query()->where('code', 'A1')->value('volunteer_id'))->toBeNull();
});

test('the pet page shows the foster family and its contact to staff but not to viewers', function () {
    $volunteer = Volunteer::factory()->for($this->shelter)->create(['name' => 'Sónia Melo', 'phone' => '912345678']);
    $cage = Cage::factory()->for(Wing::factory()->foster()->for($this->facility))->create(['code' => 'Família Melo', 'volunteer_id' => $volunteer->id]);
    $pet = Pet::factory()->for($this->shelter)->create(['cage_id' => $cage->id]);

    $this->actingAs(User::factory()->forShelter($this->shelter, 'staff')->create())
        ->get(route('pets.show', $pet))
        ->assertOk()
        ->assertSeeText('Foster family')
        ->assertSeeText('Família Melo')
        ->assertSeeText('Sónia Melo')
        ->assertSee('tel:912345678', false);

    $this->actingAs(User::factory()->forShelter($this->shelter, 'viewer')->create())
        ->get(route('pets.show', $pet))
        ->assertOk()
        ->assertSeeText('Família Melo')
        ->assertDontSeeText('Sónia Melo')
        ->assertDontSee('912345678');
});

test('the public portal shows a foster badge without the family', function () {
    config(['app.public_portal_enabled' => true]);
    $cage = Cage::factory()->for(Wing::factory()->foster()->for($this->facility))->create(['code' => 'Família Melo']);
    Pet::factory()->for($this->shelter)->create(['name' => 'Bolinha', 'cage_id' => $cage->id, 'publish_to_portal' => true, 'is_adoptable' => true]);

    $this->get(route('home'))
        ->assertOk()
        ->assertSeeText('Bolinha')
        ->assertSeeText('In a foster family')
        ->assertDontSeeText('Família Melo');
});

test('foster families are not shelter capacity on the dashboard or in the occupancy report', function () {
    $shelterCage = Cage::factory()->for(Wing::factory()->for($this->facility))->create(['capacity' => 4]);
    $fosterCage = Cage::factory()->for(Wing::factory()->foster()->for($this->facility))->create(['capacity' => 2]);
    Pet::factory()->for($this->shelter)->create(['cage_id' => $shelterCage->id]);
    Pet::factory()->for($this->shelter)->count(2)->create(['cage_id' => $fosterCage->id]);

    Livewire::actingAs($this->manager)
        ->test(Dashboard::class)
        ->assertSet('activePetsCount', 3)
        ->assertSet('availableCapacity', 3);

    $occupancy = Livewire::actingAs($this->manager)
        ->test(ShelterReports::class)
        ->get('occupancyReport');

    expect($occupancy['totals'])->toMatchArray(['capacity' => 4, 'housed' => 1, 'inFosterFamilies' => 2, 'rate' => 25])
        ->and($occupancy['wings'])->toHaveCount(1);
});

test('the volunteer page lists the animals with their foster family', function () {
    $volunteer = Volunteer::factory()->for($this->shelter)->create();
    $cage = Cage::factory()->for(Wing::factory()->foster()->for($this->facility))->create(['code' => 'Família Melo', 'volunteer_id' => $volunteer->id]);
    Pet::factory()->for($this->shelter)->create(['name' => 'Bolinha', 'cage_id' => $cage->id]);
    Pet::factory()->for($this->shelter)->create(['name' => 'Adoptado', 'cage_id' => $cage->id, 'status' => 'adopted']);

    $this->actingAs($this->manager)
        ->get(route('volunteers.show', $volunteer))
        ->assertOk()
        ->assertSeeText('Família Melo')
        ->assertSeeText('Bolinha')
        ->assertDontSeeText('Adoptado');
});
