<?php

use App\Livewire\Pets\ClinicalPhotoForm;
use App\Models\Pet;
use App\Models\PetClinicalPhoto;
use App\Models\Shelter;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

beforeEach(function () {
    Storage::fake('local');
    $this->shelter = Shelter::factory()->create();
    $this->pet = Pet::factory()->for($this->shelter)->create(['name' => 'Uva']);
});

test('guests are redirected to the login page', function () {
    $this->get(route('pets.clinical-photos.create', $this->pet))->assertRedirect(route('login'));
});

test('viewers, staff limited to other areas and admins cannot open the form', function () {
    $this->actingAs(User::factory()->forShelter($this->shelter, 'viewer')->create());
    $this->get(route('pets.clinical-photos.create', $this->pet))->assertForbidden();

    $this->actingAs(User::factory()->forShelter($this->shelter, 'staff', editAreas: ['pets'])->create());
    $this->get(route('pets.clinical-photos.create', $this->pet))->assertForbidden();

    $this->actingAs(User::factory()->admin()->create());
    $this->get(route('pets.clinical-photos.create', $this->pet))->assertForbidden();
});

test('a pet of another shelter answers 404', function () {
    $stranger = Pet::factory()->for(Shelter::factory()->create())->create();
    $this->actingAs(User::factory()->forShelter($this->shelter, 'staff')->create());

    $this->get(route('pets.clinical-photos.create', $stranger))->assertNotFound();
});

test('the health records switch hides the form', function () {
    $this->shelter->update(['modules' => ['health_records' => false]]);
    $this->actingAs(User::factory()->forShelter($this->shelter, 'manager')->create());

    $this->get(route('pets.clinical-photos.create', $this->pet))->assertNotFound();
});

test('saves each photo with the label, date and note, keeps the original and makes a thumbnail', function () {
    $user = User::factory()->forShelter($this->shelter, 'staff', editAreas: ['health'])->create();
    $this->actingAs($user);

    Livewire::test(ClinicalPhotoForm::class, ['pet' => $this->pet])
        ->set('label', '  Lump, left breast ')
        ->set('takenAt', '2026-10-12')
        ->set('photoNotes', 'Firm, 1 cm')
        ->set('photos', [UploadedFile::fake()->image('a.jpg', 3000, 2000), UploadedFile::fake()->image('b.png', 800, 600)])
        ->call('savePhoto')
        ->assertHasNoErrors()
        ->assertRedirect(route('pets.show', ['pet' => $this->pet, 'tab' => 'health']));

    $photos = $this->pet->clinicalPhotos()->get();

    expect($photos)->toHaveCount(2)
        ->and($photos->first()->label)->toBe('Lump, left breast')
        ->and($photos->first()->taken_at->toDateString())->toBe('2026-10-12')
        ->and($photos->first()->notes)->toBe('Firm, 1 cm')
        ->and($photos->first()->created_by)->toBe($user->id);

    foreach ($photos as $photo) {
        Storage::disk('local')->assertExists($photo->image_path);
        Storage::disk('local')->assertExists($photo->thumb_path);
        expect($photo->file_size)->toBeGreaterThan(0);
    }

    [$width, $height] = getimagesizefromstring(Storage::disk('local')->get($photos->first()->thumb_path));
    expect(max($width, $height))->toBeLessThanOrEqual(480);
    [$originalWidth] = getimagesizefromstring(Storage::disk('local')->get($photos->first()->image_path));
    expect($originalWidth)->toBe(3000);
});

test('requires a label, a photo of at most 10 MB, and a date that is not in the future', function () {
    $this->actingAs(User::factory()->forShelter($this->shelter, 'staff')->create());

    Livewire::test(ClinicalPhotoForm::class, ['pet' => $this->pet])
        ->call('savePhoto')
        ->assertHasErrors(['label' => 'required', 'photos' => 'required']);

    Livewire::test(ClinicalPhotoForm::class, ['pet' => $this->pet])
        ->set('label', 'Wound')
        ->set('takenAt', now()->addDay()->toDateString())
        ->set('photos', [UploadedFile::fake()->image('a.jpg')])
        ->call('savePhoto')
        ->assertHasErrors(['takenAt' => 'before_or_equal']);

    Livewire::test(ClinicalPhotoForm::class, ['pet' => $this->pet])
        ->set('label', 'Wound')
        ->set('photos', [UploadedFile::fake()->image('big.jpg')->size(10241)])
        ->call('savePhoto')
        ->assertHasErrors(['photos.0' => 'max']);

    expect(PetClinicalPhoto::query()->count())->toBe(0);
});

test('suggests the labels already used for the pet', function () {
    $this->actingAs(User::factory()->forShelter($this->shelter, 'staff')->create());
    PetClinicalPhoto::factory()->for($this->pet)->create(['label' => 'Lump A']);
    PetClinicalPhoto::factory()->for($this->pet)->create(['label' => 'Lump A']);
    PetClinicalPhoto::factory()->for(Pet::factory()->for($this->shelter))->create(['label' => 'Other pet']);

    expect(Livewire::test(ClinicalPhotoForm::class, ['pet' => $this->pet])->get('knownLabels'))->toBe(['Lump A']);
});

test('editing changes the label, date and note without a new photo', function () {
    $this->actingAs(User::factory()->forShelter($this->shelter, 'staff')->create());
    $photo = PetClinicalPhoto::factory()->for($this->pet)->create(['label' => 'Lump', 'taken_at' => '2026-10-01']);

    Livewire::test(ClinicalPhotoForm::class, ['pet' => $this->pet, 'clinicalPhoto' => $photo])
        ->assertSet('label', 'Lump')
        ->set('label', 'Lump, right side')
        ->set('takenAt', '2026-10-02')
        ->set('photoNotes', 'Smaller')
        ->call('savePhoto')
        ->assertHasNoErrors();

    $photo->refresh();
    expect($photo->label)->toBe('Lump, right side')
        ->and($photo->taken_at->toDateString())->toBe('2026-10-02')
        ->and($photo->notes)->toBe('Smaller');
});

test('editing a photo of another pet answers 404', function () {
    $this->actingAs(User::factory()->forShelter($this->shelter, 'staff')->create());
    $otherPetsPhoto = PetClinicalPhoto::factory()->for(Pet::factory()->for($this->shelter))->create();

    $this->get(route('pets.clinical-photos.edit', ['pet' => $this->pet, 'clinicalPhoto' => $otherPetsPhoto]))->assertNotFound();
});
