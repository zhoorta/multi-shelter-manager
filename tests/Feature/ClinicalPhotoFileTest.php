<?php

use App\Models\Pet;
use App\Models\PetClinicalPhoto;
use App\Models\Shelter;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

beforeEach(function () {
    Storage::fake('local');
    $this->shelter = Shelter::factory()->create();
    $this->pet = Pet::factory()->for($this->shelter)->create();

    Storage::disk('local')->put('clinical-photos/1/original.jpg', UploadedFile::fake()->image('o.jpg', 1200, 900)->getContent());
    Storage::disk('local')->put('clinical-photos/1/thumbs/original.jpg', UploadedFile::fake()->image('t.jpg', 100, 75)->getContent());

    $this->photo = PetClinicalPhoto::factory()->for($this->pet)->create([
        'label' => 'Lump A',
        'taken_at' => '2026-10-12',
        'image_path' => 'clinical-photos/1/original.jpg',
        'thumb_path' => 'clinical-photos/1/thumbs/original.jpg',
    ]);

    $this->url = fn (array $query = []) => route('pets.clinical-photos.file', ['pet' => $this->pet, 'clinicalPhoto' => $this->photo, ...$query]);
});

test('guests are redirected to the login page', function () {
    $this->get(($this->url)())->assertRedirect(route('login'));
});

test('members of the shelter, viewers included, get the thumbnail by default and the original on request', function () {
    foreach (['staff', 'viewer'] as $role) {
        $this->actingAs(User::factory()->forShelter($this->shelter, $role)->create());

        $thumbnail = $this->get(($this->url)())->assertOk();
        expect($thumbnail->headers->get('Cache-Control'))->toContain('private');
        expect(getimagesizefromstring($thumbnail->streamedContent())[0])->toBe(100);

        $original = $this->get(($this->url)(['size' => 'full']))->assertOk();
        expect(getimagesizefromstring($original->streamedContent())[0])->toBe(1200);
    }
});

test('the download is the original, named after the pet, label and date', function () {
    $this->actingAs(User::factory()->forShelter($this->shelter, 'staff')->create());

    $response = $this->get(($this->url)(['download' => 1]))->assertOk();

    expect($response->headers->get('Content-Disposition'))->toContain(Str::slug($this->pet->ref).'-lump-a-2026-10-12.jpg')
        ->and(getimagesizefromstring($response->streamedContent())[0])->toBe(1200);
});

test('falls back to the original when the photo has no thumbnail', function () {
    $this->photo->update(['thumb_path' => null]);
    $this->actingAs(User::factory()->forShelter($this->shelter, 'staff')->create());

    $response = $this->get(($this->url)())->assertOk();

    expect(getimagesizefromstring($response->streamedContent())[0])->toBe(1200);
});

test('a user of another shelter and an admin cannot get the photo', function () {
    $this->actingAs(User::factory()->forShelter(Shelter::factory()->create(), 'manager')->create());
    $this->get(($this->url)())->assertNotFound();

    $this->actingAs(User::factory()->admin()->create());
    $this->get(($this->url)())->assertForbidden();
});

test('the photo must belong to the pet in the URL', function () {
    $this->actingAs(User::factory()->forShelter($this->shelter, 'staff')->create());
    $other = Pet::factory()->for($this->shelter)->create();

    $this->get(route('pets.clinical-photos.file', ['pet' => $other, 'clinicalPhoto' => $this->photo]))->assertNotFound();
});

test('a deleted photo and a missing file answer 404', function () {
    $this->actingAs(User::factory()->forShelter($this->shelter, 'staff')->create());

    Storage::disk('local')->delete(['clinical-photos/1/original.jpg', 'clinical-photos/1/thumbs/original.jpg']);
    $this->get(($this->url)())->assertNotFound();

    $this->photo->delete();
    $this->get(($this->url)())->assertNotFound();
});

test('the health records switch hides the photos', function () {
    $this->shelter->update(['modules' => ['health_records' => false]]);
    $this->actingAs(User::factory()->forShelter($this->shelter, 'manager')->create());

    $this->get(($this->url)())->assertNotFound();
});
