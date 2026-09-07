<?php

use App\Livewire\Pets\PetForm;
use App\Models\Breed;
use App\Models\Cage;
use App\Models\Pet;
use App\Models\PetImage;
use App\Models\Shelter;
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

test('creates a new pet scoped to the acting user\'s shelter and redirects to the index', function () {
    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    $species = Species::factory()->create();
    $breed = Breed::factory()->for($species)->create();

    Livewire::test(PetForm::class)
        ->set('petName', 'Rex')
        ->set('petSpeciesId', $species->id)
        ->set('petBreedId', $breed->id)
        ->set('petGender', 'male')
        ->call('savePet')
        ->assertHasNoErrors()
        ->assertRedirect(route('pets.index'));

    $pet = Pet::query()->where('name', 'Rex')->first();
    expect($pet)->not->toBeNull();
    expect($pet->shelter_id)->toBe($shelter->id);
    expect($pet->species_id)->toBe($species->id);
    expect($pet->breed_id)->toBe($breed->id);
    expect($pet->status)->toBe('available');
    expect($pet->primary_color_id)->toBeNull();
    expect($pet->secondary_color_id)->toBeNull();
    expect($pet->fur_type_id)->toBeNull();
    expect($pet->cage_id)->toBeNull();
    expect($pet->birth_date)->toBeNull();
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
    ]);

    Livewire::test(PetForm::class, ['pet' => $pet])
        ->assertSet('petName', 'Rex')
        ->assertSet('petSpeciesId', $species->id)
        ->assertSet('petBreedId', $breed->id)
        ->assertSet('petGender', 'male')
        ->assertSet('petChip', '985121000123456')
        ->assertSet('petStatus', 'quarantine');
});

test('updates an existing pet and redirects to the index', function () {
    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    $species = Species::factory()->create();
    $breed = Breed::factory()->for($species)->create();
    $pet = Pet::factory()->for($shelter)->for($species)->for($breed)->create([
        'name' => 'Rex',
        'status' => 'available',
    ]);

    Livewire::test(PetForm::class, ['pet' => $pet])
        ->set('petName', 'Rex Renamed')
        ->set('petStatus', 'adopted')
        ->call('savePet')
        ->assertHasNoErrors()
        ->assertRedirect(route('pets.index'));

    expect($pet->fresh()->name)->toBe('Rex Renamed');
    expect($pet->fresh()->status)->toBe('adopted');
    expect($pet->fresh()->shelter_id)->toBe($shelter->id);
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
