<?php

use App\Livewire\Admin\ShelterForm;
use App\Models\Region;
use App\Models\Shelter;
use App\Models\Species;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

test('guests are redirected to the login page', function () {
    $this->get(route('admin.shelters.create'))->assertRedirect(route('login'));
});

test('staff and managers are forbidden from viewing the form', function () {
    $staff = User::factory()->create();
    $this->actingAs($staff);
    $this->get(route('admin.shelters.create'))->assertForbidden();

    $manager = User::factory()->create();
    $this->actingAs($manager);
    $this->get(route('admin.shelters.create'))->assertForbidden();
});

test('admins can view the create and edit pages', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $shelter = Shelter::factory()->create();

    $this->get(route('admin.shelters.create'))->assertOk();
    $this->get(route('admin.shelters.edit', $shelter))->assertOk();
});

test('creates a new shelter and redirects to the shelters list', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $region = Region::factory()->create(['name' => 'Lisboa']);

    Livewire::test(ShelterForm::class)
        ->set('shelterName', 'Happy Paws')
        ->set('shelterShortName', 'HP')
        ->set('shelterCity', 'Lisbon')
        ->set('shelterRegionId', $region->id)
        ->call('saveShelter')
        ->assertHasNoErrors()
        ->assertRedirect(route('admin.shelters.index'));

    expect(Shelter::query()->where('name', 'Happy Paws')->where('short_name', 'HP')->where('city', 'Lisbon')->where('region_id', $region->id)->exists())->toBeTrue();
});

test('requires a name and city to create a shelter', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    Livewire::test(ShelterForm::class)
        ->set('shelterName', '')
        ->set('shelterCity', '')
        ->call('saveShelter')
        ->assertHasErrors(['shelterName' => 'required', 'shelterCity' => 'required'])
        ->assertHasNoErrors('shelterRegionId');
});

test('rejects a soft-deleted region', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $region = Region::factory()->create();
    $region->delete();

    Livewire::test(ShelterForm::class)
        ->set('shelterName', 'Happy Paws')
        ->set('shelterCity', 'Lisbon')
        ->set('shelterRegionId', $region->id)
        ->call('saveShelter')
        ->assertHasErrors(['shelterRegionId' => 'exists']);
});

test('creates a shelter without a region', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    Livewire::test(ShelterForm::class)
        ->set('shelterName', 'Happy Paws')
        ->set('shelterCity', 'Lisbon')
        ->call('saveShelter')
        ->assertHasNoErrors();

    expect(Shelter::query()->where('name', 'Happy Paws')->value('region_id'))->toBeNull();
});

test('rejects a region that does not exist', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    Livewire::test(ShelterForm::class)
        ->set('shelterName', 'Happy Paws')
        ->set('shelterCity', 'Lisbon')
        ->set('shelterRegionId', 999999)
        ->call('saveShelter')
        ->assertHasErrors(['shelterRegionId' => 'exists']);
});

test('validates email and website format', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    Livewire::test(ShelterForm::class)
        ->set('shelterName', 'Happy Paws')
        ->set('shelterCity', 'Lisbon')
        ->set('shelterEmail', 'not-an-email')
        ->set('shelterWebsite', 'not-a-url')
        ->call('saveShelter')
        ->assertHasErrors(['shelterEmail' => 'email', 'shelterWebsite' => 'url']);
});

test('populates the form when editing an existing shelter', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $shelter = Shelter::factory()->create(['name' => 'Happy Paws', 'short_name' => 'HP', 'city' => 'Lisbon']);

    Livewire::test(ShelterForm::class, ['shelter' => $shelter])
        ->assertSet('shelterName', 'Happy Paws')
        ->assertSet('shelterShortName', 'HP')
        ->assertSet('shelterCity', 'Lisbon')
        ->assertSet('shelterRegionId', $shelter->region_id);
});

test('updates an existing shelter', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $shelter = Shelter::factory()->create(['name' => 'Happy Paws', 'city' => 'Lisbon']);
    $porto = Region::factory()->create(['name' => 'Porto']);

    Livewire::test(ShelterForm::class, ['shelter' => $shelter])
        ->set('shelterName', 'Happier Paws')
        ->set('shelterShortName', 'HPP')
        ->set('shelterCity', 'Porto')
        ->set('shelterRegionId', $porto->id)
        ->call('saveShelter')
        ->assertHasNoErrors()
        ->assertRedirect(route('admin.shelters.index'));

    expect($shelter->fresh()->name)->toBe('Happier Paws');
    expect($shelter->fresh()->short_name)->toBe('HPP');
    expect($shelter->fresh()->city)->toBe('Porto');
    expect($shelter->fresh()->region_id)->toBe($porto->id);
});

test('clears the region of an existing shelter', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $shelter = Shelter::factory()->create();

    Livewire::test(ShelterForm::class, ['shelter' => $shelter])
        ->set('shelterRegionId', '')
        ->call('saveShelter')
        ->assertHasNoErrors();

    expect($shelter->fresh()->region_id)->toBeNull();
});

test('uploads and replaces a shelter logo', function () {
    Storage::fake('public');

    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $shelter = Shelter::factory()->create(['logo_path' => null]);

    Livewire::test(ShelterForm::class, ['shelter' => $shelter])
        ->set('shelterLogo', UploadedFile::fake()->image('logo.png'))
        ->call('saveShelter')
        ->assertHasNoErrors();

    $firstLogoPath = $shelter->fresh()->logo_path;
    expect($firstLogoPath)->not->toBeNull();
    Storage::disk('public')->assertExists($firstLogoPath);

    Livewire::test(ShelterForm::class, ['shelter' => $shelter->fresh()])
        ->set('shelterLogo', UploadedFile::fake()->image('logo-2.png'))
        ->call('saveShelter')
        ->assertHasNoErrors();

    $secondLogoPath = $shelter->fresh()->logo_path;
    expect($secondLogoPath)->not->toBe($firstLogoPath);
    Storage::disk('public')->assertExists($secondLogoPath);
    Storage::disk('public')->assertMissing($firstLogoPath);
});

test('toggles species on and off before saving', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $dog = Species::factory()->create(['name' => 'Dog']);
    $cat = Species::factory()->create(['name' => 'Cat']);

    Livewire::test(ShelterForm::class)
        ->call('toggleSpecies', $dog->id)
        ->assertSet('shelterSpeciesIds', [$dog->id])
        ->call('toggleSpecies', $cat->id)
        ->assertSet('shelterSpeciesIds', [$dog->id, $cat->id])
        ->call('toggleSpecies', $dog->id)
        ->assertSet('shelterSpeciesIds', [$cat->id]);
});

test('saves the enabled species for the shelter', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $dog = Species::factory()->create(['name' => 'Dog']);
    $cat = Species::factory()->create(['name' => 'Cat']);

    Livewire::test(ShelterForm::class)
        ->set('shelterName', 'Happy Paws')
        ->set('shelterCity', 'Lisbon')
        ->set('shelterRegionId', Region::factory()->create()->id)
        ->call('toggleSpecies', $dog->id)
        ->call('saveShelter')
        ->assertHasNoErrors();

    $shelter = Shelter::query()->where('name', 'Happy Paws')->firstOrFail();

    expect($shelter->species()->pluck('species.id')->all())->toBe([$dog->id]);
});

test('editing an existing shelter reflects its currently enabled species and can change them', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $dog = Species::factory()->create(['name' => 'Dog']);
    $cat = Species::factory()->create(['name' => 'Cat']);

    $shelter = Shelter::factory()->create();
    $shelter->species()->attach($dog->id);

    Livewire::test(ShelterForm::class, ['shelter' => $shelter])
        ->assertSet('shelterSpeciesIds', [$dog->id])
        ->call('toggleSpecies', $dog->id)
        ->call('toggleSpecies', $cat->id)
        ->call('saveShelter')
        ->assertHasNoErrors();

    expect($shelter->species()->pluck('species.id')->all())->toBe([$cat->id]);
});
