<?php

use App\Livewire\Admin\ShelterForm;
use App\Livewire\Settings\ShelterProfile;
use App\Models\Pet;
use App\Models\Shelter;
use App\Models\Species;
use App\Models\User;
use App\Models\Vaccine;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

beforeEach(function () {
    $this->shelter = Shelter::factory()->create();
    $this->manager = User::factory()->forShelter($this->shelter, 'manager')->create();
});

test('every module is on for a shelter that never chose', function () {
    foreach (Shelter::MODULES as $module) {
        expect($this->shelter->hasModule($module))->toBeTrue();
    }
});

test('a module switched off hides its sidebar link and answers 404', function (string $module, string $routeName, string $menuLabel) {
    $this->actingAs($this->manager)->get(route('dashboard'))->assertSee(route($routeName), false);
    $this->actingAs($this->manager)->get(route($routeName))->assertOk();

    $this->shelter->update(['modules' => [$module => false]]);

    $this->actingAs($this->manager->fresh())->get(route('dashboard'))->assertDontSee(route($routeName), false);
    $this->actingAs($this->manager->fresh())->get(route($routeName))->assertNotFound();
})->with([
    'members' => ['members', 'members.index', 'Members'],
    'volunteers' => ['volunteers', 'volunteers.index', 'Volunteers'],
    'sponsorships' => ['sponsorships', 'pets.sponsorships.index', 'Sponsorships'],
    'adoption applications' => ['adoption_applications', 'pets.applications.index', 'Adoption Applications'],
    'reports' => ['reports', 'reports.index', 'Reports'],
    'vaccinations' => ['health_records', 'pets.vaccinations.index', 'Vaccinations'],
    'treatments' => ['health_records', 'pets.treatments.index', 'Treatments'],
]);

test('switching a module off for one shelter leaves other shelters untouched', function () {
    $this->shelter->update(['modules' => ['members' => false]]);

    $other = Shelter::factory()->create();
    $otherManager = User::factory()->forShelter($other, 'manager')->create();

    $this->actingAs($otherManager)->get(route('members.index'))->assertOk();
});

test('managers switch modules off and on from the shelter settings', function () {
    Livewire::actingAs($this->manager)
        ->test(ShelterProfile::class)
        ->assertSet('shelterModules.members', true)
        ->set('shelterModules.members', false)
        ->call('saveShelter')
        ->assertHasNoErrors();

    expect($this->shelter->fresh()->hasModule('members'))->toBeFalse()
        ->and($this->shelter->fresh()->hasModule('volunteers'))->toBeTrue();

    Livewire::actingAs($this->manager->fresh())
        ->test(ShelterProfile::class)
        ->assertSet('shelterModules.members', false)
        ->set('shelterModules.members', true)
        ->call('saveShelter');

    expect($this->shelter->fresh()->hasModule('members'))->toBeTrue();
});

test('admins switch modules off from the shelter form', function () {
    $admin = User::factory()->admin()->create();

    Livewire::actingAs($admin)
        ->test(ShelterForm::class, ['shelter' => $this->shelter])
        ->set('shelterModules.volunteers', false)
        ->call('saveShelter')
        ->assertHasNoErrors();

    expect($this->shelter->fresh()->hasModule('volunteers'))->toBeFalse();
});

test('a new shelter made by an admin starts with every module on', function () {
    $admin = User::factory()->admin()->create();

    Livewire::actingAs($admin)
        ->test(ShelterForm::class)
        ->set('shelterName', 'Novo Abrigo')
        ->set('shelterCity', 'Horta')
        ->set('shelterEmail', 'geral@novo.pt')
        ->call('saveShelter')
        ->assertHasNoErrors();

    $created = Shelter::query()->where('name', 'Novo Abrigo')->firstOrFail();

    foreach (Shelter::MODULES as $module) {
        expect($created->hasModule($module))->toBeTrue();
    }
});

test('the public adoption form and button are gone when applications are off', function () {
    config(['app.public_portal_enabled' => true]);

    $pet = Pet::factory()->publishedToPortal()->for($this->shelter)->for(Species::factory()->create())->create();

    $this->get($pet->publicPageUrl())->assertSee(route('adoption-applications.create', $pet->ref), false);
    $this->get(route('adoption-applications.create', $pet->ref))->assertOk();

    $this->shelter->update(['modules' => ['adoption_applications' => false]]);

    $this->get($pet->publicPageUrl())->assertOk()->assertDontSee(route('adoption-applications.create', $pet->ref), false);
    $this->get(route('adoption-applications.create', $pet->ref))->assertNotFound();
});

test('the health records switch hides vaccinations and treatments on the pet page', function () {
    $pet = Pet::factory()->for($this->shelter)->for(Species::factory()->create())->create();

    $this->actingAs($this->manager)->get(route('pets.show', $pet))->assertSee(route('pets.vaccinate', $pet), false);

    $this->shelter->update(['modules' => ['health_records' => false]]);

    $this->actingAs($this->manager->fresh())->get(route('pets.show', $pet))
        ->assertOk()
        ->assertDontSee(route('pets.vaccinate', $pet), false)
        ->assertDontSee(route('pets.treat', $pet), false);
    $this->actingAs($this->manager->fresh())->get(route('pets.vaccinate', $pet))->assertNotFound();
    $this->actingAs($this->manager->fresh())->get(route('pets.treat', $pet))->assertNotFound();
});

test('reminder commands skip shelters with health records switched off', function () {
    $this->shelter->update(['modules' => ['health_records' => false]]);
    $vaccine = Vaccine::factory()->create();
    $pet = Pet::factory()->for($this->shelter)->for(Species::factory()->create())->create();
    $this->manager->shelters()->updateExistingPivot($this->shelter->id, ['vaccination_notifications' => true]);
    $pet->vaccines()->attach($vaccine->id, ['due_date' => today()->addDays(2)->toDateString()]);

    Notification::fake();
    $this->artisan('app:send-vaccination-due-notifications')->assertSuccessful();

    Notification::assertNothingSent();
});
