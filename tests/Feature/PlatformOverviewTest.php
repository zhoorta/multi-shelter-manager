<?php

use App\Livewire\Admin\PlatformOverview;
use App\Models\Adoption;
use App\Models\Breed;
use App\Models\Cage;
use App\Models\Facility;
use App\Models\Pet;
use App\Models\Shelter;
use App\Models\Species;
use App\Models\User;
use App\Models\Wing;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->admin()->create(['last_login' => now()]));
});

/**
 * A shelter its users can work in: a species with breeds, a cage and a user.
 */
function readyShelter(array $attributes = []): Shelter
{
    $shelter = Shelter::factory()->create($attributes);
    $shelter->species()->attach(Breed::factory()->create()->species_id);
    Cage::factory()->for(Wing::factory()->for(Facility::factory()->for($shelter)))->create();
    User::factory()->forShelter($shelter)->create(['last_login' => now()]);

    return $shelter;
}

test('admins see the platform overview on the dashboard instead of the shelter lists', function () {
    $this->get(route('dashboard'))
        ->assertOk()
        ->assertSeeLivewire(PlatformOverview::class)
        ->assertDontSee([__('Recent activity'), __('Pending Applications')]);
});

test('shelter users do not get the platform overview', function () {
    $this->actingAs(User::factory()->forShelter(Shelter::factory()->create(), 'manager')->create());

    $this->get(route('dashboard'))->assertOk()->assertDontSeeLivewire(PlatformOverview::class);

    Livewire::test(PlatformOverview::class)->assertForbidden();
});

test('counts shelters, active users, pets in care and this year\'s adoptions across the platform', function () {
    $shelter = Shelter::factory()->create();
    $otherShelter = Shelter::factory()->create();

    User::factory()->forShelter($shelter)->create(['last_login' => now()->subDays(5)]);
    User::factory()->forShelter($otherShelter)->create(['last_login' => now()->subDays(40)]);

    Pet::factory()->for($shelter)->create();
    Pet::factory()->for($otherShelter)->create();
    Pet::factory()->for($otherShelter)->create(['date_of_death' => now()]);
    Adoption::factory()->for(Pet::factory()->for($shelter)->create(['status' => 'adopted']))->create(['application_status' => 'Approved', 'adoption_date' => now()]);
    Adoption::factory()->for(Pet::factory()->for($shelter)->create(['status' => 'adopted']))->create(['application_status' => 'Approved', 'adoption_date' => now()->subYear()]);

    expect(Livewire::test(PlatformOverview::class)->instance()->totals)->toBe([
        'shelters' => 2,
        'activeUsers' => 2,
        'petsInCare' => 2,
        'adoptionsThisYear' => 1,
    ]);
});

test('lists each shelter with its usage, least recently used first', function () {
    $active = readyShelter(['name' => 'Active Shelter']);
    Pet::factory()->for($active)->count(2)->create();
    Adoption::factory()->for(Pet::factory()->for($active)->create(['status' => 'adopted']))->create(['application_status' => 'Approved', 'adoption_date' => now()]);
    $active->pets()->update(['updated_at' => '2026-09-20 10:00:00']);
    $unused = readyShelter(['name' => 'Unused Shelter']);
    $unused->users()->update(['last_login' => null]);

    Livewire::test(PlatformOverview::class)
        ->assertSeeInOrder(['Unused Shelter', __('Never'), 'Active Shelter', '2', '1', now()->format('d/m/Y'), '20/09/2026'])
        ->assertSee(route('admin.shelters.edit', $active));
});

test('lists the shelters and species that still need setting up', function () {
    readyShelter(['name' => 'Ready Shelter']);
    Shelter::factory()->create(['name' => 'Empty Shelter']);
    $withoutBreeds = Species::factory()->create(['name' => 'Ferret']);
    readyShelter()->species()->attach($withoutBreeds);
    Species::factory()->create(['name' => 'Unused Species']);

    $component = Livewire::test(PlatformOverview::class)
        ->assertSee([__('Setup to complete'), 'Ferret', __('Species without breeds')])
        ->assertDontSee('Unused Species');

    $setup = $component->instance()->sheltersToSetUp;
    expect($setup['withoutSpecies']->pluck('name')->all())->toBe(['Empty Shelter'])
        ->and($setup['withoutCages']->pluck('name')->all())->toBe(['Empty Shelter'])
        ->and($setup['withoutUsers']->pluck('name')->all())->toBe(['Empty Shelter']);
});

test('hides the setup section when everything is set up', function () {
    readyShelter();

    Livewire::test(PlatformOverview::class)->assertDontSee(__('Setup to complete'));
});

test('lists invited users who never logged in, oldest invitation first', function () {
    $shelter = readyShelter(['name' => 'Faial Shelter']);
    User::factory()->forShelter($shelter)->create(['name' => 'Newer Invite', 'created_at' => now()->subDay()]);
    User::factory()->forShelter($shelter)->create(['name' => 'Older Invite', 'email' => 'older@example.com', 'created_at' => '2026-09-01']);

    Livewire::test(PlatformOverview::class)
        ->assertSeeInOrder([__('Invitations not accepted'), 'Older Invite', 'older@example.com', 'Faial Shelter', '01/09/2026', 'Newer Invite']);
});
