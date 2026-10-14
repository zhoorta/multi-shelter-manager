<?php

use App\Models\Pet;
use App\Models\PetImage;
use App\Models\Shelter;
use App\Models\Volunteer;
use Illuminate\Support\Facades\Storage;

test('fills in the size of files that have none and reports the missing ones', function () {
    Storage::fake();

    Storage::put('pets/a.jpg', str_repeat('a', 100));
    Storage::put('volunteers/b.jpg', str_repeat('b', 200));
    Storage::put('shelters/c.png', str_repeat('c', 300));

    $pet = Pet::factory()->create();
    $image = PetImage::factory()->create(['pet_id' => $pet->id, 'image_path' => 'pets/a.jpg', 'file_size' => null]);
    $gone = PetImage::factory()->create(['pet_id' => $pet->id, 'image_path' => 'pets/gone.jpg', 'file_size' => null]);
    $kept = PetImage::factory()->create(['pet_id' => $pet->id, 'image_path' => 'pets/a.jpg', 'file_size' => 5]);
    $volunteer = Volunteer::factory()->create(['image_path' => 'volunteers/b.jpg', 'image_size' => null]);
    $shelter = Shelter::factory()->create(['logo_path' => 'shelters/c.png', 'logo_size' => null]);

    $this->artisan('app:backfill-file-sizes')
        ->expectsOutputToContain('pet photos: 1 updated, 1 missing on disk.')
        ->assertSuccessful();

    expect($image->fresh()->file_size)->toBe(100)
        ->and($gone->fresh()->file_size)->toBeNull()
        ->and($kept->fresh()->file_size)->toBe(5)
        ->and($volunteer->fresh()->image_size)->toBe(200)
        ->and($shelter->fresh()->logo_size)->toBe(300);
});
