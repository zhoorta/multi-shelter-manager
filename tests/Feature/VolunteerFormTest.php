<?php

use App\Livewire\Volunteers\VolunteerForm;
use App\Models\Activity;
use App\Models\Shelter;
use App\Models\Species;
use App\Models\User;
use App\Models\Volunteer;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

test('guests are redirected to the login page', function () {
    $this->get(route('volunteers.create'))->assertRedirect(route('login'));
});

test('staff are forbidden from viewing the form', function () {
    $staff = User::factory()->create();
    $this->actingAs($staff);

    $this->get(route('volunteers.create'))->assertForbidden();
});

test('admins are forbidden from viewing the form', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $this->get(route('volunteers.create'))->assertForbidden();
});

test('managers can view the create and edit pages', function () {
    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->forShelter($shelter, 'manager')->create());

    $volunteer = Volunteer::factory()->for($shelter)->create();

    $this->get(route('volunteers.create'))->assertOk();
    $this->get(route('volunteers.edit', $volunteer))->assertOk();
});

test('creates a new volunteer scoped to the acting manager\'s shelter and redirects to its show page', function () {
    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->forShelter($shelter, 'manager')->create());

    Livewire::test(VolunteerForm::class)
        ->set('volunteerName', 'Maria Silva')
        ->set('volunteerGender', 'female')
        ->set('volunteerEmail', 'maria@example.com')
        ->call('saveVolunteer')
        ->assertHasNoErrors();

    $volunteer = Volunteer::query()->where('name', 'Maria Silva')->firstOrFail();
    expect($volunteer->shelter_id)->toBe($shelter->id);
    expect($volunteer->gender)->toBe('female');
    expect($volunteer->email)->toBe('maria@example.com');
});

test('requires a name to create a volunteer', function () {
    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->forShelter($shelter, 'manager')->create());

    Livewire::test(VolunteerForm::class)
        ->set('volunteerName', '')
        ->call('saveVolunteer')
        ->assertHasErrors(['volunteerName' => 'required']);
});

test('creates a volunteer without a gender, as volunteers imported from Portugal Zoófilo have none', function () {
    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->forShelter($shelter, 'manager')->create());

    Livewire::test(VolunteerForm::class)
        ->set('volunteerName', 'Maria Silva')
        ->set('volunteerGender', '')
        ->call('saveVolunteer')
        ->assertHasNoErrors();

    expect(Volunteer::query()->where('name', 'Maria Silva')->sole()->gender)->toBeNull();
});

test('validates email format and that the end date is not before the start date', function () {
    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->forShelter($shelter, 'manager')->create());

    Livewire::test(VolunteerForm::class)
        ->set('volunteerName', 'Maria Silva')
        ->set('volunteerGender', 'female')
        ->set('volunteerEmail', 'not-an-email')
        ->set('volunteerStartDate', '2026-02-01')
        ->set('volunteerEndDate', '2026-01-01')
        ->call('saveVolunteer')
        ->assertHasErrors(['volunteerEmail' => 'email', 'volunteerEndDate' => 'after_or_equal']);
});

test('populates the form when editing an existing volunteer', function () {
    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->forShelter($shelter, 'manager')->create());

    $volunteer = Volunteer::factory()->for($shelter)->create(['name' => 'Maria Silva', 'gender' => 'female']);

    Livewire::test(VolunteerForm::class, ['volunteer' => $volunteer])
        ->assertSet('volunteerName', 'Maria Silva')
        ->assertSet('volunteerGender', 'female');
});

test('updates an existing volunteer', function () {
    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->forShelter($shelter, 'manager')->create());

    $volunteer = Volunteer::factory()->for($shelter)->create(['name' => 'Maria Silva']);

    Livewire::test(VolunteerForm::class, ['volunteer' => $volunteer])
        ->set('volunteerName', 'Maria Santos')
        ->call('saveVolunteer')
        ->assertHasNoErrors();

    expect($volunteer->fresh()->name)->toBe('Maria Santos');
});

test('uploads and replaces a volunteer photo', function () {
    Storage::fake('public');

    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->forShelter($shelter, 'manager')->create());

    $volunteer = Volunteer::factory()->for($shelter)->create(['image_path' => null]);

    Livewire::test(VolunteerForm::class, ['volunteer' => $volunteer])
        ->set('volunteerImage', UploadedFile::fake()->image('photo.png'))
        ->call('saveVolunteer')
        ->assertHasNoErrors();

    $firstImagePath = $volunteer->fresh()->image_path;
    expect($firstImagePath)->not->toBeNull();
    Storage::disk('public')->assertExists($firstImagePath);

    Livewire::test(VolunteerForm::class, ['volunteer' => $volunteer->fresh()])
        ->set('volunteerImage', UploadedFile::fake()->image('photo-2.png'))
        ->call('saveVolunteer')
        ->assertHasNoErrors();

    $secondImagePath = $volunteer->fresh()->image_path;
    expect($secondImagePath)->not->toBe($firstImagePath);
    Storage::disk('public')->assertExists($secondImagePath);
    Storage::disk('public')->assertMissing($firstImagePath);
});

test('attaches the selected activities to a newly created volunteer', function () {
    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->forShelter($shelter, 'manager')->create());

    $activity = Activity::factory()->create();

    Livewire::test(VolunteerForm::class)
        ->set('volunteerName', 'Maria Silva')
        ->set('volunteerGender', 'female')
        ->set('volunteerActivityIds', [$activity->id])
        ->call('saveVolunteer')
        ->assertHasNoErrors();

    $volunteer = Volunteer::query()->where('name', 'Maria Silva')->firstOrFail();
    expect($volunteer->activities()->pluck('activities.id')->all())->toBe([$activity->id]);
});

test('rejects an activity id that does not exist', function () {
    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->forShelter($shelter, 'manager')->create());

    Livewire::test(VolunteerForm::class)
        ->set('volunteerName', 'Maria Silva')
        ->set('volunteerGender', 'female')
        ->set('volunteerActivityIds', [99999])
        ->call('saveVolunteer')
        ->assertHasErrors(['volunteerActivityIds.0' => 'exists']);
});

test('populates the form with the volunteer\'s currently selected activities when editing', function () {
    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->forShelter($shelter, 'manager')->create());

    $volunteer = Volunteer::factory()->for($shelter)->create();
    $activity = Activity::factory()->create();
    $volunteer->activities()->attach($activity);

    Livewire::test(VolunteerForm::class, ['volunteer' => $volunteer])
        ->assertSet('volunteerActivityIds', [$activity->id]);
});

test('removes an activity from volunteer_activities when its toggle is switched off', function () {
    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->forShelter($shelter, 'manager')->create());

    $volunteer = Volunteer::factory()->for($shelter)->create();
    $activity = Activity::factory()->create();
    $volunteer->activities()->attach($activity);

    Livewire::test(VolunteerForm::class, ['volunteer' => $volunteer])
        ->call('toggleActivity', $activity->id)
        ->call('saveVolunteer')
        ->assertHasNoErrors();

    expect($volunteer->activities()->pluck('activities.id')->all())->toBe([]);
});

test('attaches the selected species to a newly created volunteer', function () {
    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->forShelter($shelter, 'manager')->create());

    $species = Species::factory()->create();

    Livewire::test(VolunteerForm::class)
        ->set('volunteerName', 'Maria Silva')
        ->set('volunteerGender', 'female')
        ->set('volunteerSpeciesIds', [$species->id])
        ->call('saveVolunteer')
        ->assertHasNoErrors();

    $volunteer = Volunteer::query()->where('name', 'Maria Silva')->firstOrFail();
    expect($volunteer->species()->pluck('species.id')->all())->toBe([$species->id]);
});

test('rejects a species id that does not exist', function () {
    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->forShelter($shelter, 'manager')->create());

    Livewire::test(VolunteerForm::class)
        ->set('volunteerName', 'Maria Silva')
        ->set('volunteerGender', 'female')
        ->set('volunteerSpeciesIds', [99999])
        ->call('saveVolunteer')
        ->assertHasErrors(['volunteerSpeciesIds.0' => 'exists']);
});

test('populates the form with the volunteer\'s currently selected species when editing', function () {
    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->forShelter($shelter, 'manager')->create());

    $volunteer = Volunteer::factory()->for($shelter)->create();
    $species = Species::factory()->create();
    $volunteer->species()->attach($species);

    Livewire::test(VolunteerForm::class, ['volunteer' => $volunteer])
        ->assertSet('volunteerSpeciesIds', [$species->id]);
});

test('removes a species from volunteer_species when its toggle is switched off', function () {
    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->forShelter($shelter, 'manager')->create());

    $volunteer = Volunteer::factory()->for($shelter)->create();
    $species = Species::factory()->create();
    $volunteer->species()->attach($species);

    Livewire::test(VolunteerForm::class, ['volunteer' => $volunteer])
        ->call('toggleSpecies', $species->id)
        ->call('saveVolunteer')
        ->assertHasNoErrors();

    expect($volunteer->species()->pluck('species.id')->all())->toBe([]);
});

test('saves availability for a day when morning or afternoon is checked', function () {
    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->forShelter($shelter, 'manager')->create());

    Livewire::test(VolunteerForm::class)
        ->set('volunteerName', 'Maria Silva')
        ->set('volunteerGender', 'female')
        ->set('availabilities.0.mornings', true)
        ->set('availabilities.0.frequency', 'weekly')
        ->call('saveVolunteer')
        ->assertHasNoErrors();

    $volunteer = Volunteer::query()->where('name', 'Maria Silva')->firstOrFail();
    $monday = $volunteer->availabilities()->where('day_index', 0)->firstOrFail();
    expect($monday->mornings)->toBeTrue();
    expect($monday->afternoons)->toBeFalse();
    expect($monday->frequency)->toBe('weekly');
});

test('does not save availability for a day when neither morning nor afternoon is checked', function () {
    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->forShelter($shelter, 'manager')->create());

    Livewire::test(VolunteerForm::class)
        ->set('volunteerName', 'Maria Silva')
        ->set('volunteerGender', 'female')
        ->call('saveVolunteer')
        ->assertHasNoErrors();

    $volunteer = Volunteer::query()->where('name', 'Maria Silva')->firstOrFail();
    expect($volunteer->availabilities()->count())->toBe(0);
});

test('deletes an existing availability record when it is unchecked', function () {
    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->forShelter($shelter, 'manager')->create());

    $volunteer = Volunteer::factory()->for($shelter)->create();
    $volunteer->availabilities()->create(['day_index' => 0, 'mornings' => true, 'afternoons' => false, 'frequency' => 'weekly']);

    Livewire::test(VolunteerForm::class, ['volunteer' => $volunteer])
        ->set('availabilities.0.mornings', false)
        ->call('saveVolunteer')
        ->assertHasNoErrors();

    expect($volunteer->availabilities()->where('day_index', 0)->exists())->toBeFalse();
});

test('populates the form with the volunteer\'s existing availability when editing', function () {
    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->forShelter($shelter, 'manager')->create());

    $volunteer = Volunteer::factory()->for($shelter)->create();
    $volunteer->availabilities()->create(['day_index' => 3, 'mornings' => false, 'afternoons' => true, 'frequency' => 'biweekly']);

    Livewire::test(VolunteerForm::class, ['volunteer' => $volunteer])
        ->assertSet('availabilities.3.mornings', false)
        ->assertSet('availabilities.3.afternoons', true)
        ->assertSet('availabilities.3.frequency', 'biweekly');
});

test('returns 404 when editing a volunteer belonging to another shelter', function () {
    $otherShelter = Shelter::factory()->create();
    $volunteer = Volunteer::factory()->for($otherShelter)->create();

    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->forShelter($shelter, 'manager')->create());

    $this->get(route('volunteers.edit', $volunteer))->assertNotFound();
});
