<?php

use App\Livewire\Pets\PetForm;
use App\Models\Breed;
use App\Models\Cage;
use App\Models\Pet;
use App\Models\PetImage;
use App\Models\Shelter;
use App\Models\Sickness;
use App\Models\Species;
use App\Models\User;
use App\Models\Wing;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

test('guests are redirected to the login page', function () {
    $this->get(route('pets.create'))->assertRedirect(route('login'));

    $pet = Pet::factory()->create();
    $this->get(route('pets.edit', $pet))->assertRedirect(route('login'));
});

test('admins are forbidden from viewing the form', function () {
    $admin = User::factory()->create(['role' => 'admin', 'shelter_id' => null]);
    $this->actingAs($admin);

    $this->get(route('pets.create'))->assertForbidden();

    $pet = Pet::factory()->create();
    $this->get(route('pets.edit', $pet))->assertForbidden();
});

test('managers and staff can view the create and edit pages', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();

    $manager = User::factory()->create(['role' => 'manager', 'shelter_id' => $shelter->id]);
    $this->actingAs($manager);
    $this->get(route('pets.create'))->assertOk();
    $this->get(route('pets.edit', $pet))->assertOk();

    $staff = User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]);
    $this->actingAs($staff);
    $this->get(route('pets.create'))->assertOk();
    $this->get(route('pets.edit', $pet))->assertOk();
});

test('returns 404 when editing a pet belonging to another shelter', function () {
    $otherShelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($otherShelter)->create();

    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    $this->get(route('pets.edit', $pet))->assertNotFound();
});

test('the create form starts with every optional field empty', function () {
    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    Livewire::test(PetForm::class)
        ->assertSet('petPrimaryColorId', null)
        ->assertSet('petSecondaryColorId', null)
        ->assertSet('petFurTypeId', null)
        ->assertSet('petCageId', null)
        ->assertSet('petBirthDate', '')
        ->assertSet('petSpeciesId', null)
        ->assertSet('petBreedId', null);
});

test('breed dropdown reflects the selected species right after opening the create form', function () {
    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    $species = Species::factory()->create();
    Breed::factory()->for($species)->create(['name' => 'Labrador']);

    Livewire::test(PetForm::class)
        ->set('petSpeciesId', $species->id)
        ->assertSee('Labrador');
});

test('resets the selected breed when the species changes', function () {
    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    $species = Species::factory()->create();
    $breed = Breed::factory()->for($species)->create();

    Livewire::test(PetForm::class)
        ->set('petBreedId', $breed->id)
        ->set('petSpeciesId', $species->id)
        ->assertSet('petBreedId', null);
});

test('sickness toggles only list sicknesses linked to the selected species', function () {
    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    $species = Species::factory()->create();
    $otherSpecies = Species::factory()->create();
    $sickness = Sickness::factory()->create(['name' => 'Parvovirus']);
    $sickness->species()->attach($species);
    $unrelatedSickness = Sickness::factory()->create(['name' => 'Feline Leukemia']);
    $unrelatedSickness->species()->attach($otherSpecies);

    Livewire::test(PetForm::class)
        ->set('petSpeciesId', $species->id)
        ->assertSee('Parvovirus')
        ->assertDontSee('Feline Leukemia');
});

test('resets the selected sicknesses when the species changes', function () {
    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    $species = Species::factory()->create();
    $sickness = Sickness::factory()->create();
    $sickness->species()->attach($species);

    Livewire::test(PetForm::class)
        ->set('petSicknessIds', [$sickness->id])
        ->set('petSpeciesId', $species->id)
        ->assertSet('petSicknessIds', []);
});

test('creates a new pet scoped to the acting user\'s shelter and redirects to the show page', function () {
    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    $species = Species::factory()->create();
    $breed = Breed::factory()->for($species)->create();

    $component = Livewire::test(PetForm::class)
        ->set('petName', 'Rex')
        ->set('petSpeciesId', $species->id)
        ->set('petBreedId', $breed->id)
        ->set('petGender', 'male')
        ->call('savePet')
        ->assertHasNoErrors();

    $pet = Pet::query()->where('name', 'Rex')->first();
    expect($pet)->not->toBeNull();
    $component->assertRedirect(route('pets.show', $pet));
    expect($pet->shelter_id)->toBe($shelter->id);
    expect($pet->species_id)->toBe($species->id);
    expect($pet->breed_id)->toBe($breed->id);
    expect($pet->status)->toBe('available');
    expect($pet->primary_color_id)->toBeNull();
    expect($pet->secondary_color_id)->toBeNull();
    expect($pet->fur_type_id)->toBeNull();
    expect($pet->cage_id)->toBeNull();
    expect($pet->birth_date)->toBeNull();
    expect($pet->is_neutered)->toBeFalse();
    expect($pet->is_adoptable)->toBeTrue();
    expect($pet->is_sponsorable)->toBeTrue();
});

test('creates a pet marked as not adoptable and not sponsorable', function () {
    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    $species = Species::factory()->create();
    $breed = Breed::factory()->for($species)->create();

    $component = Livewire::test(PetForm::class)
        ->set('petName', 'Rex')
        ->set('petSpeciesId', $species->id)
        ->set('petBreedId', $breed->id)
        ->set('petGender', 'male')
        ->set('petIsAdoptable', false)
        ->set('petIsSponsorable', false)
        ->call('savePet')
        ->assertHasNoErrors();

    $pet = Pet::query()->where('name', 'Rex')->firstOrFail();
    expect($pet->is_adoptable)->toBeFalse();
    expect($pet->is_sponsorable)->toBeFalse();
    $component->assertRedirect(route('pets.show', $pet));
});

test('creates a pet marked as neutered', function () {
    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    $species = Species::factory()->create();
    $breed = Breed::factory()->for($species)->create();

    $component = Livewire::test(PetForm::class)
        ->set('petName', 'Rex')
        ->set('petSpeciesId', $species->id)
        ->set('petBreedId', $breed->id)
        ->set('petGender', 'male')
        ->set('petIsNeutered', true)
        ->call('savePet')
        ->assertHasNoErrors();

    $pet = Pet::query()->where('name', 'Rex')->firstOrFail();
    expect($pet->is_neutered)->toBeTrue();
    $component->assertRedirect(route('pets.show', $pet));
});

test('attaches the selected sicknesses to a newly created pet', function () {
    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    $species = Species::factory()->create();
    $breed = Breed::factory()->for($species)->create();
    $sickness = Sickness::factory()->create();
    $sickness->species()->attach($species);

    Livewire::test(PetForm::class)
        ->set('petName', 'Rex')
        ->set('petSpeciesId', $species->id)
        ->set('petBreedId', $breed->id)
        ->set('petGender', 'male')
        ->set('petSicknessIds', [$sickness->id])
        ->call('savePet')
        ->assertHasNoErrors();

    $pet = Pet::query()->where('name', 'Rex')->firstOrFail();
    expect($pet->sicknesses()->pluck('sicknesses.id')->all())->toBe([$sickness->id]);
    expect($pet->sicknesses()->first()->pivot->status)->toBe('active');
});

test('rejects a sickness that does not belong to the selected species', function () {
    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    $species = Species::factory()->create();
    $breed = Breed::factory()->for($species)->create();
    $otherSpecies = Species::factory()->create();
    $mismatchedSickness = Sickness::factory()->create();
    $mismatchedSickness->species()->attach($otherSpecies);

    Livewire::test(PetForm::class)
        ->set('petName', 'Rex')
        ->set('petSpeciesId', $species->id)
        ->set('petBreedId', $breed->id)
        ->set('petGender', 'male')
        ->set('petSicknessIds', [$mismatchedSickness->id])
        ->call('savePet')
        ->assertHasErrors(['petSicknessIds.0' => 'exists']);
});

test('requires a name, species, breed, and gender to create a pet', function () {
    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    Livewire::test(PetForm::class)
        ->call('savePet')
        ->assertHasErrors([
            'petName' => 'required',
            'petSpeciesId' => 'required',
            'petBreedId' => 'required',
            'petGender' => 'required',
        ]);
});

test('rejects a breed that does not belong to the selected species', function () {
    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    $species = Species::factory()->create();
    $otherSpecies = Species::factory()->create();
    $mismatchedBreed = Breed::factory()->for($otherSpecies)->create();

    Livewire::test(PetForm::class)
        ->set('petName', 'Rex')
        ->set('petSpeciesId', $species->id)
        ->set('petBreedId', $mismatchedBreed->id)
        ->set('petGender', 'male')
        ->call('savePet')
        ->assertHasErrors(['petBreedId' => 'exists']);
});

test('cannot assign a pet to a cage belonging to another shelter', function () {
    $otherShelter = Shelter::factory()->create();
    $wing = Wing::factory()->for($otherShelter)->create();
    $cage = Cage::factory()->for($wing)->create();

    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    $species = Species::factory()->create();
    $breed = Breed::factory()->for($species)->create();

    expect(fn () => Livewire::test(PetForm::class)
        ->set('petName', 'Rex')
        ->set('petSpeciesId', $species->id)
        ->set('petBreedId', $breed->id)
        ->set('petGender', 'male')
        ->set('petCageId', $cage->id)
        ->call('savePet'))
        ->toThrow(ModelNotFoundException::class);

    expect(Pet::query()->where('name', 'Rex')->exists())->toBeFalse();
});

test('stores an uploaded photo as the pet\'s main image', function () {
    Storage::fake('public');

    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    $species = Species::factory()->create();
    $breed = Breed::factory()->for($species)->create();
    $photo = UploadedFile::fake()->image('rex.jpg');

    Livewire::test(PetForm::class)
        ->set('petName', 'Rex')
        ->set('petSpeciesId', $species->id)
        ->set('petBreedId', $breed->id)
        ->set('petGender', 'male')
        ->set('petPhotos', [$photo])
        ->call('savePet')
        ->assertHasNoErrors();

    $pet = Pet::query()->where('name', 'Rex')->firstOrFail();
    $image = PetImage::query()->where('pet_id', $pet->id)->first();

    expect($image)->not->toBeNull();
    expect($image->is_main)->toBeTrue();
    Storage::disk('public')->assertExists($image->image_path);
});

test('stores multiple uploaded photos, flagging only the first as main', function () {
    Storage::fake('public');

    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    $species = Species::factory()->create();
    $breed = Breed::factory()->for($species)->create();
    $photos = [
        UploadedFile::fake()->image('rex-1.jpg'),
        UploadedFile::fake()->image('rex-2.jpg'),
        UploadedFile::fake()->image('rex-3.jpg'),
    ];

    Livewire::test(PetForm::class)
        ->set('petName', 'Rex')
        ->set('petSpeciesId', $species->id)
        ->set('petBreedId', $breed->id)
        ->set('petGender', 'male')
        ->set('petPhotos', $photos)
        ->call('savePet')
        ->assertHasNoErrors();

    $pet = Pet::query()->where('name', 'Rex')->firstOrFail();
    $images = PetImage::query()->where('pet_id', $pet->id)->orderBy('id')->get();

    expect($images)->toHaveCount(3);
    expect($images->where('is_main', true))->toHaveCount(1);
    expect($images->first()->is_main)->toBeTrue();
    $images->each(fn (PetImage $image) => Storage::disk('public')->assertExists($image->image_path));
});

test('rejects a photo larger than 2MB', function () {
    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    $species = Species::factory()->create();
    $breed = Breed::factory()->for($species)->create();
    $photo = UploadedFile::fake()->image('rex.jpg')->size(2049);

    Livewire::test(PetForm::class)
        ->set('petName', 'Rex')
        ->set('petSpeciesId', $species->id)
        ->set('petBreedId', $breed->id)
        ->set('petGender', 'male')
        ->set('petPhotos', [$photo])
        ->call('savePet')
        ->assertHasErrors(['petPhotos.0' => 'max']);
});

test('presets the species when arriving from a species-scoped pets list', function () {
    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    $species = Species::factory()->create(['name' => 'Dog', 'name_plural' => 'Dogs']);

    Livewire::withQueryParams(['species' => $species->id])
        ->test(PetForm::class)
        ->assertSet('petSpeciesId', $species->id)
        ->assertSee('Dogs')
        ->assertDontSee('wire:model.live="petSpeciesId"', false);
});

test('does not preset a species when creating a pet without one in the query string', function () {
    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    Livewire::test(PetForm::class)
        ->assertSet('petSpeciesId', null)
        ->assertDontSee('wire:model.live="petSpeciesId"', false);
});

test('creates a pet with the species carried over from a species-scoped pets list', function () {
    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    $species = Species::factory()->create();
    $breed = Breed::factory()->for($species)->create();

    $component = Livewire::withQueryParams(['species' => $species->id])
        ->test(PetForm::class)
        ->set('petName', 'Rex')
        ->set('petBreedId', $breed->id)
        ->set('petGender', 'male')
        ->call('savePet')
        ->assertHasNoErrors();

    $pet = Pet::query()->where('name', 'Rex')->firstOrFail();
    expect($pet->species_id)->toBe($species->id);
    $component->assertRedirect(route('pets.show', $pet));
});

test('does not show an editable species field when editing an existing pet', function () {
    $shelter = Shelter::factory()->create();
    $species = Species::factory()->create(['name' => 'Dog', 'name_plural' => 'Dogs']);
    $pet = Pet::factory()->for($shelter)->for($species)->create();

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    Livewire::test(PetForm::class, ['pet' => $pet])
        ->assertSee('Dogs')
        ->assertDontSee('wire:model.live="petSpeciesId"', false);
});

test('populates the form with the pet\'s current data when editing', function () {
    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    $species = Species::factory()->create();
    $breed = Breed::factory()->for($species)->create();
    $pet = Pet::factory()->for($shelter)->for($species)->for($breed)->create([
        'name' => 'Rex',
        'gender' => 'male',
        'chip' => '985121000123456',
        'status' => 'quarantine',
        'is_neutered' => true,
        'is_adoptable' => false,
        'is_sponsorable' => false,
    ]);

    Livewire::test(PetForm::class, ['pet' => $pet])
        ->assertSet('petName', 'Rex')
        ->assertSet('petSpeciesId', $species->id)
        ->assertSet('petBreedId', $breed->id)
        ->assertSet('petGender', 'male')
        ->assertSet('petChip', '985121000123456')
        ->assertSet('petStatus', 'quarantine')
        ->assertSet('petIsNeutered', true)
        ->assertSet('petIsAdoptable', false)
        ->assertSet('petIsSponsorable', false);
});

test('populates the form with the pet\'s currently diagnosed sicknesses when editing', function () {
    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    $species = Species::factory()->create();
    $breed = Breed::factory()->for($species)->create();
    $pet = Pet::factory()->for($shelter)->for($species)->for($breed)->create();
    $sickness = Sickness::factory()->create();
    $sickness->species()->attach($species);
    $pet->sicknesses()->attach($sickness, ['diagnosed_at' => now(), 'status' => 'active']);

    Livewire::test(PetForm::class, ['pet' => $pet])
        ->assertSet('petSicknessIds', [$sickness->id]);
});

test('links back to the pet show page when editing', function () {
    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    $pet = Pet::factory()->for($shelter)->create();

    $this->get(route('pets.edit', $pet))
        ->assertSee(route('pets.show', $pet), false);
});

test('updates an existing pet and redirects to the show page', function () {
    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    $species = Species::factory()->create();
    $breed = Breed::factory()->for($species)->create();
    $pet = Pet::factory()->for($shelter)->for($species)->for($breed)->create([
        'name' => 'Rex',
        'status' => 'available',
        'is_neutered' => false,
        'is_adoptable' => true,
        'is_sponsorable' => true,
    ]);

    Livewire::test(PetForm::class, ['pet' => $pet])
        ->set('petName', 'Rex Renamed')
        ->set('petStatus', 'adopted')
        ->set('petIsNeutered', true)
        ->set('petIsAdoptable', false)
        ->set('petIsSponsorable', false)
        ->call('savePet')
        ->assertHasNoErrors()
        ->assertRedirect(route('pets.show', $pet));

    expect($pet->fresh()->name)->toBe('Rex Renamed');
    expect($pet->fresh()->status)->toBe('adopted');
    expect($pet->fresh()->shelter_id)->toBe($shelter->id);
    expect($pet->fresh()->is_neutered)->toBeTrue();
    expect($pet->fresh()->is_adoptable)->toBeFalse();
    expect($pet->fresh()->is_sponsorable)->toBeFalse();
});

test('removes a sickness from pet_sickness when its toggle is switched off', function () {
    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    $species = Species::factory()->create();
    $breed = Breed::factory()->for($species)->create();
    $pet = Pet::factory()->for($shelter)->for($species)->for($breed)->create(['gender' => 'male']);
    $sickness = Sickness::factory()->create();
    $sickness->species()->attach($species);
    $pet->sicknesses()->attach($sickness, ['diagnosed_at' => now(), 'status' => 'active']);

    Livewire::test(PetForm::class, ['pet' => $pet])
        ->set('petSicknessIds', [])
        ->call('savePet')
        ->assertHasNoErrors();

    expect($pet->sicknesses()->count())->toBe(0);
});

test('leaves an already-diagnosed sickness untouched when its toggle stays on', function () {
    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    $species = Species::factory()->create();
    $breed = Breed::factory()->for($species)->create();
    $pet = Pet::factory()->for($shelter)->for($species)->for($breed)->create(['gender' => 'male']);
    $sickness = Sickness::factory()->create();
    $sickness->species()->attach($species);
    $pet->sicknesses()->attach($sickness, ['diagnosed_at' => '2020-01-01', 'status' => 'chronic', 'treatment_notes' => 'Ongoing care']);

    Livewire::test(PetForm::class, ['pet' => $pet])
        ->set('petSicknessIds', [$sickness->id])
        ->call('savePet')
        ->assertHasNoErrors();

    $pivot = $pet->sicknesses()->first()->pivot;
    expect($pivot->diagnosed_at->toDateString())->toBe('2020-01-01');
    expect($pivot->status)->toBe('chronic');
    expect($pivot->treatment_notes)->toBe('Ongoing care');
});

test('adds a new photo during edit without touching the existing main photo', function () {
    Storage::fake('public');

    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    $species = Species::factory()->create();
    $breed = Breed::factory()->for($species)->create();
    $pet = Pet::factory()->for($shelter)->for($species)->for($breed)->create(['name' => 'Rex']);

    $originalImage = PetImage::factory()->for($pet)->create([
        'image_path' => 'pets/original.jpg',
        'is_main' => true,
    ]);
    Storage::disk('public')->put('pets/original.jpg', 'fake-contents');

    $newPhoto = UploadedFile::fake()->image('new-rex.jpg');

    Livewire::test(PetForm::class, ['pet' => $pet])
        ->set('petPhotos', [$newPhoto])
        ->call('savePet')
        ->assertHasNoErrors();

    expect($originalImage->fresh())->not->toBeNull();
    expect($originalImage->fresh()->is_main)->toBeTrue();
    Storage::disk('public')->assertExists('pets/original.jpg');

    $newImage = PetImage::query()->where('pet_id', $pet->id)->where('id', '!=', $originalImage->id)->first();
    expect($newImage)->not->toBeNull();
    expect($newImage->is_main)->toBeFalse();
    Storage::disk('public')->assertExists($newImage->image_path);
});

test('sets a different photo as the pet\'s main photo', function () {
    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    $pet = Pet::factory()->for($shelter)->create();
    $mainImage = PetImage::factory()->for($pet)->create(['is_main' => true]);
    $otherImage = PetImage::factory()->for($pet)->create(['is_main' => false]);

    Livewire::test(PetForm::class, ['pet' => $pet])
        ->call('setMainPetImage', $otherImage->id);

    expect($mainImage->fresh()->is_main)->toBeFalse();
    expect($otherImage->fresh()->is_main)->toBeTrue();
});

test('deletes a pet photo and its stored file', function () {
    Storage::fake('public');

    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    $pet = Pet::factory()->for($shelter)->create();
    $image = PetImage::factory()->for($pet)->create(['image_path' => 'pets/to-delete.jpg']);
    Storage::disk('public')->put('pets/to-delete.jpg', 'fake-contents');

    Livewire::test(PetForm::class, ['pet' => $pet])
        ->call('deletePetImage', $image->id);

    expect(PetImage::query()->find($image->id))->toBeNull();
    Storage::disk('public')->assertMissing('pets/to-delete.jpg');
});

test('cannot set as main or delete a photo belonging to another shelter\'s pet', function () {
    $otherShelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($otherShelter)->create();
    $image = PetImage::factory()->for($pet)->create();

    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    expect(fn () => Livewire::test(PetForm::class)->call('setMainPetImage', $image->id))
        ->toThrow(ModelNotFoundException::class);

    expect(fn () => Livewire::test(PetForm::class)->call('deletePetImage', $image->id))
        ->toThrow(ModelNotFoundException::class);

    expect(PetImage::query()->find($image->id))->not->toBeNull();
});
