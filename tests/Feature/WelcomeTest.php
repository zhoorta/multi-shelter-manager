<?php

use App\Livewire\Welcome;
use App\Models\Breed;
use App\Models\Pet;
use App\Models\PetImage;
use App\Models\Region;
use App\Models\Shelter;
use App\Models\Size;
use App\Models\Species;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Livewire;

/**
 * @param  array<string, mixed>  $attributes
 */
function publishedPet(array $attributes = []): Pet
{
    return Pet::factory()->create([
        'publish_to_portal' => true,
        'is_adoptable' => true,
        'status' => 'available',
        ...$attributes,
    ]);
}

test('guests see published pets from every shelter', function () {
    publishedPet(['name' => 'Bolinha']);
    publishedPet(['name' => 'Pantufa']);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Bolinha')
        ->assertSee('Pantufa');
});

test('hides pets that are unpublished, not adoptable, adopted or deceased', function () {
    publishedPet(['name' => 'Visible']);
    publishedPet(['name' => 'Unpublished', 'publish_to_portal' => false]);
    publishedPet(['name' => 'NotAdoptable', 'is_adoptable' => false]);
    publishedPet(['name' => 'Adopted', 'status' => 'adopted']);
    publishedPet(['name' => 'Deceased', 'date_of_death' => now()->subDay()]);

    Livewire::test(Welcome::class)
        ->assertSee('Visible')
        ->assertDontSee('Unpublished')
        ->assertDontSee('NotAdoptable')
        ->assertDontSee('Adopted')
        ->assertDontSee('Deceased');
});

test('logged-in staff see published pets from other shelters too', function () {
    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    publishedPet(['name' => 'OtherShelterPet', 'shelter_id' => Shelter::factory()->create()->id]);

    Livewire::test(Welcome::class)->assertSee('OtherShelterPet');
});

test('filters pets by species, gender, size and breed', function () {
    $species = Species::factory()->create();
    $breed = Breed::factory()->for($species)->create();
    $otherBreed = Breed::factory()->for($species)->create();
    $size = Size::factory()->for($species)->create();
    $otherSize = Size::factory()->for($species)->create();

    $base = ['species_id' => $species->id, 'breed_id' => $breed->id, 'size_id' => $size->id, 'gender' => 'female'];
    publishedPet(['name' => 'Match', ...$base]);
    publishedPet(['name' => 'OtherSpecies']);
    publishedPet(['name' => 'OtherGender', ...$base, 'gender' => 'male']);
    publishedPet(['name' => 'OtherSize', ...$base, 'size_id' => $otherSize->id]);
    publishedPet(['name' => 'OtherBreed', ...$base, 'breed_id' => $otherBreed->id]);

    Livewire::test(Welcome::class)
        ->set('speciesFilter', (string) $species->id)
        ->set('genderFilter', 'female')
        ->set('sizeFilter', (string) $size->id)
        ->set('breedFilter', (string) $breed->id)
        ->assertSee('Match')
        ->assertDontSee('OtherSpecies')
        ->assertDontSee('OtherGender')
        ->assertDontSee('OtherSize')
        ->assertDontSee('OtherBreed');
});

test('filters pets by the region of their shelter', function () {
    $region = Region::factory()->create();
    $otherRegion = Region::factory()->create();

    publishedPet(['name' => 'InRegion', 'shelter_id' => Shelter::factory()->create(['region_id' => $region->id])->id]);
    publishedPet(['name' => 'OutOfRegion', 'shelter_id' => Shelter::factory()->create(['region_id' => $otherRegion->id])->id]);

    Livewire::test(Welcome::class)
        ->set('regionFilter', (string) $region->id)
        ->assertSee('InRegion')
        ->assertDontSee('OutOfRegion');
});

test('changing species clears the breed and size filters', function () {
    Livewire::test(Welcome::class)
        ->set('breedFilter', '5')
        ->set('sizeFilter', '3')
        ->set('speciesFilter', (string) Species::factory()->create()->id)
        ->assertSet('breedFilter', '')
        ->assertSet('sizeFilter', '');
});

test('shows the shelter contact in the pet details', function () {
    $shelter = Shelter::factory()->create(['name' => 'Abrigo Feliz', 'email' => 'ola@abrigo.test']);
    $pet = publishedPet(['shelter_id' => $shelter->id]);

    Livewire::test(Welcome::class)
        ->call('showPet', $pet->id)
        ->assertSet('selectedPetId', $pet->id)
        ->assertSee('Abrigo Feliz')
        ->assertSee('mailto:ola@abrigo.test', false);
});

test('shows every photo of the pet in the details slider', function () {
    $pet = publishedPet();
    PetImage::factory()->for($pet)->create(['image_path' => 'pets/second.jpg']);
    PetImage::factory()->for($pet)->create(['image_path' => 'pets/main.jpg', 'is_main' => true]);

    Livewire::test(Welcome::class)
        ->call('showPet', $pet->id)
        ->assertSeeInOrder(['main.jpg', 'second.jpg'])
        ->assertSee(__('Next photo'));
});

test('cannot open the details of an unpublished pet', function () {
    $pet = publishedPet(['publish_to_portal' => false]);

    Livewire::test(Welcome::class)->call('showPet', $pet->id);
})->throws(ModelNotFoundException::class);
