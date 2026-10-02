<?php

use App\Livewire\Admin\ManageUsers;
use App\Livewire\Admin\UserForm;
use App\Livewire\Members\MemberForm;
use App\Livewire\Pets\AdoptionForm;
use App\Livewire\Pets\ManageAdoptions;
use App\Livewire\Pets\ManagePets;
use App\Livewire\Pets\PetForm;
use App\Livewire\Pets\SponsorshipForm;
use App\Livewire\Pets\VaccinationForm;
use App\Models\Adoption;
use App\Models\Pet;
use App\Models\Shelter;
use App\Models\ShelterUser;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->shelter = Shelter::factory()->create();
});

test('managers edit every area, staff the areas they are limited to and viewers none', function () {
    $manager = User::factory()->forShelter($this->shelter, 'manager')->create();
    $unlimited = User::factory()->forShelter($this->shelter, 'staff')->create();
    $limited = User::factory()->forShelter($this->shelter, 'staff', editAreas: ['health'])->create();
    $viewer = User::factory()->forShelter($this->shelter, 'viewer')->create();

    foreach (ShelterUser::EDIT_AREAS as $area) {
        expect($manager->canEditArea($area))->toBeTrue()
            ->and($unlimited->canEditArea($area))->toBeTrue()
            ->and($viewer->canEditArea($area))->toBeFalse()
            ->and($limited->canEditArea($area))->toBe($area === 'health');
    }
});

test('a staff member limited to health can record vaccinations but not edit animals or adoptions', function () {
    $pet = Pet::factory()->for($this->shelter)->create();
    $this->actingAs(User::factory()->forShelter($this->shelter, 'staff', editAreas: ['health'])->create());

    Livewire::test(VaccinationForm::class, ['pet' => $pet])->assertOk();
    Livewire::test(PetForm::class)->assertForbidden();
    Livewire::test(PetForm::class, ['pet' => $pet])->assertForbidden();
    Livewire::test(AdoptionForm::class, ['pet' => $pet])->assertForbidden();
    Livewire::test(ManagePets::class)->call('deletePet', $pet->id)->assertForbidden();

    expect($pet->fresh())->not->toBeNull();
});

test('a staff member limited to animals cannot touch the health records', function () {
    $pet = Pet::factory()->for($this->shelter)->create();
    $this->actingAs(User::factory()->forShelter($this->shelter, 'staff', editAreas: ['pets'])->create());

    Livewire::test(PetForm::class, ['pet' => $pet])->assertOk();
    Livewire::test(VaccinationForm::class, ['pet' => $pet])->assertForbidden();
});

test('the pet page only offers the actions of the areas the user can edit', function () {
    $pet = Pet::factory()->for($this->shelter)->create(['is_adoptable' => true]);

    $this->actingAs(User::factory()->forShelter($this->shelter, 'staff', editAreas: ['health'])->create());
    $this->get(route('pets.show', $pet))
        ->assertOk()
        ->assertSee(route('pets.vaccinate', $pet), false)
        ->assertDontSee(route('pets.edit', $pet), false)
        ->assertDontSee(route('pets.adopt', $pet), false);

    $this->actingAs(User::factory()->forShelter($this->shelter, 'staff', editAreas: ['pets', 'adoptions'])->create());
    $this->get(route('pets.show', $pet))
        ->assertOk()
        ->assertSee(route('pets.edit', $pet), false)
        ->assertSee(route('pets.adopt', $pet), false)
        ->assertDontSee(route('pets.vaccinate', $pet), false);
});

test('inviting a staff member limited to some areas stores them, and all areas stores no limit', function () {
    $this->actingAs(User::factory()->forShelter($this->shelter, 'manager')->create());

    Livewire::test(UserForm::class)
        ->set('userName', 'Vera Vet')
        ->set('userEmail', 'vera@example.com')
        ->set('userMemberships.0.edit_areas', ['pets' => true, 'health' => true, 'adoptions' => false, 'sponsorships' => false, 'members' => false])
        ->call('saveUser')
        ->assertHasNoErrors();

    $membership = User::query()->where('email', 'vera@example.com')->firstOrFail()->shelters()->first()->pivot;
    expect($membership->edit_areas)->toBe(['pets', 'health']);

    Livewire::test(UserForm::class)
        ->set('userName', 'Sam Staff')
        ->set('userEmail', 'sam@example.com')
        ->call('saveUser')
        ->assertHasNoErrors();

    expect(User::query()->where('email', 'sam@example.com')->firstOrFail()->shelters()->first()->pivot->edit_areas)->toBeNull();
});

test('a staff member needs at least one editable area, and other roles ignore the limit', function () {
    $this->actingAs(User::factory()->forShelter($this->shelter, 'manager')->create());

    Livewire::test(UserForm::class)
        ->set('userName', 'Nobody')
        ->set('userEmail', 'nobody@example.com')
        ->set('userMemberships.0.edit_areas', ['pets' => false, 'health' => false, 'adoptions' => false, 'sponsorships' => false, 'members' => false])
        ->call('saveUser')
        ->assertHasErrors('userMemberships.0.edit_areas');

    expect(User::query()->where('email', 'nobody@example.com')->exists())->toBeFalse();

    Livewire::test(UserForm::class)
        ->set('userName', 'Boss')
        ->set('userEmail', 'boss@example.com')
        ->set('userMemberships.0.role', 'manager')
        ->set('userMemberships.0.edit_areas', ['pets' => false, 'health' => true, 'adoptions' => false, 'sponsorships' => false, 'members' => false])
        ->call('saveUser')
        ->assertHasNoErrors();

    expect(User::query()->where('email', 'boss@example.com')->firstOrFail()->shelters()->first()->pivot->edit_areas)->toBeNull();
});

test('editing a limited staff member shows their areas and the users list names them', function () {
    $limited = User::factory()->forShelter($this->shelter, 'staff', editAreas: ['health', 'adoptions'])->create(['name' => 'Vera Vet']);
    $this->actingAs(User::factory()->forShelter($this->shelter, 'manager')->create());

    Livewire::test(UserForm::class, ['user' => $limited])
        ->assertSet('userMemberships.0.edit_areas', ['pets' => false, 'health' => true, 'adoptions' => true, 'sponsorships' => false, 'members' => false])
        ->assertSee('Turn off an area to make it view-only for this user.');

    Livewire::test(ManageUsers::class)->assertSee('Health, Adoptions');
});

test('a staff member limited to animals cannot edit sponsorships, members or adoption records', function () {
    $pet = Pet::factory()->for($this->shelter)->create();
    $adoption = Adoption::factory()->for($pet)->create();
    $this->actingAs(User::factory()->forShelter($this->shelter, 'staff', editAreas: ['pets'])->create());

    Livewire::test(SponsorshipForm::class, ['pet' => $pet])->assertForbidden();
    Livewire::test(MemberForm::class)->assertForbidden();
    Livewire::test(ManageAdoptions::class)->call('deleteAdoption', $adoption->id)->assertForbidden();

    expect($adoption->fresh()->trashed())->toBeFalse();
});
