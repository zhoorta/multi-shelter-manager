<?php

use App\Livewire\Pets\AdoptionForm;
use App\Livewire\Pets\ManageAdoptionApplications;
use App\Models\AdoptionApplication;
use App\Models\Pet;
use App\Models\Shelter;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Livewire;

test('guests are redirected to the login page', function () {
    $this->get(route('pets.applications.index'))->assertRedirect(route('login'));
});

test('viewers are forbidden because applications hold personal data', function () {
    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->forShelter($shelter, 'viewer')->create());

    $this->get(route('pets.applications.index'))->assertForbidden();
});

test('lists pending applications of the current shelter only', function () {
    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());
    AdoptionApplication::factory()->for(Pet::factory()->for($shelter))->create(['name' => 'Ana Costa']);
    AdoptionApplication::factory()->for(Pet::factory()->for($shelter))->rejected()->create(['name' => 'Rui Rejected']);
    AdoptionApplication::factory()->create(['name' => 'Other Shelter Applicant']);

    $this->get(route('pets.applications.index'))
        ->assertOk()
        ->assertSee('Ana Costa')
        ->assertDontSee('Rui Rejected')
        ->assertDontSee('Other Shelter Applicant');
});

test('rejecting an application records who reviewed it', function () {
    $shelter = Shelter::factory()->create();
    $staff = User::factory()->forShelter($shelter, 'staff')->create();
    $this->actingAs($staff);
    $application = AdoptionApplication::factory()->for(Pet::factory()->for($shelter))->create();

    Livewire::test(ManageAdoptionApplications::class)->call('rejectApplication', $application->id);

    $application->refresh();
    expect($application->status)->toBe('rejected')
        ->and($application->reviewed_by)->toBe($staff->id)
        ->and($application->reviewed_at)->not->toBeNull();
});

test('cannot reject an application from another shelter', function () {
    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());
    $otherApplication = AdoptionApplication::factory()->create();

    expect(fn () => Livewire::test(ManageAdoptionApplications::class)->call('rejectApplication', $otherApplication->id))
        ->toThrow(ModelNotFoundException::class);

    expect($otherApplication->fresh()->status)->toBe('pending');
});

test('rejects in bulk the pending applications of pets no longer available', function () {
    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->forShelter($shelter, 'manager')->create());
    $staleApplication = AdoptionApplication::factory()->for(Pet::factory()->for($shelter)->state(['status' => 'adopted']))->create();
    $openApplication = AdoptionApplication::factory()->for(Pet::factory()->for($shelter))->create();

    Livewire::test(ManageAdoptionApplications::class)
        ->assertSet('staleApplicationsCount', 1)
        ->call('rejectStaleApplications');

    expect($staleApplication->fresh()->status)->toBe('rejected')
        ->and($openApplication->fresh()->status)->toBe('pending');
});

test('approving prefills the adoption form and links the saved adoption', function () {
    $shelter = Shelter::factory()->create();
    $staff = User::factory()->forShelter($shelter, 'staff')->create();
    $this->actingAs($staff);
    $pet = Pet::factory()->for($shelter)->create(['is_adoptable' => true]);
    $application = AdoptionApplication::factory()->for($pet)->create(['name' => 'Ana Costa', 'email' => 'ana@example.com']);

    Livewire::withQueryParams(['application' => $application->id])
        ->test(AdoptionForm::class, ['pet' => $pet])
        ->assertSet('adopterName', 'Ana Costa')
        ->assertSet('adopterEmail', 'ana@example.com')
        ->call('saveAdoption')
        ->assertHasNoErrors();

    $application->refresh();
    expect($application->status)->toBe('approved')
        ->and($application->adoption_id)->toBe($pet->adoptions()->sole()->id)
        ->and($application->reviewed_by)->toBe($staff->id)
        ->and($pet->fresh()->status)->toBe('adopted');
});

test('the adoption form refuses an application of another pet', function () {
    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());
    $pet = Pet::factory()->for($shelter)->create();
    $otherPetApplication = AdoptionApplication::factory()->for(Pet::factory()->for($shelter))->create();

    expect(fn () => Livewire::withQueryParams(['application' => $otherPetApplication->id])
        ->test(AdoptionForm::class, ['pet' => $pet]))
        ->toThrow(ModelNotFoundException::class);
});

test('applications are pruned after the retention period', function () {
    AdoptionApplication::factory()->create(['updated_at' => now()->subMonths(AdoptionApplication::RETENTION_MONTHS)->subDay()]);
    AdoptionApplication::factory()->create(['updated_at' => now()->subMonths(AdoptionApplication::RETENTION_MONTHS)->subDay(), 'deleted_at' => now()->subYear()]);
    $recent = AdoptionApplication::factory()->create();

    $this->artisan('model:prune', ['--model' => [AdoptionApplication::class]])->assertSuccessful();

    expect(AdoptionApplication::withTrashed()->pluck('id')->all())->toBe([$recent->id]);
});
