<?php

use App\Models\Breed;
use App\Models\Cage;
use App\Models\Color;
use App\Models\FurType;
use App\Models\Pet;
use App\Models\PetImage;
use App\Models\PetSickness;
use App\Models\PetVaccine;
use App\Models\Shelter;
use App\Models\Sickness;
use App\Models\Species;
use App\Models\User;
use App\Models\Vaccine;

test('shelter relation returns the shelter the pet belongs to', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();

    expect($pet->shelter->is($shelter))->toBeTrue();
});

test('cage relation returns the cage the pet is housed in', function () {
    $cage = Cage::factory()->create();
    $pet = Pet::factory()->for($cage)->create();

    expect($pet->cage->is($cage))->toBeTrue();
});

test('species relation returns the pet species', function () {
    $species = Species::factory()->create();
    $pet = Pet::factory()->for($species)->create();

    expect($pet->species->is($species))->toBeTrue();
});

test('breed relation returns the pet breed', function () {
    $breed = Breed::factory()->create();
    $pet = Pet::factory()->for($breed)->create();

    expect($pet->breed->is($breed))->toBeTrue();
});

test('primary color relation returns the pet primary color', function () {
    $color = Color::factory()->create();
    $pet = Pet::factory()->for($color, 'primaryColor')->create();

    expect($pet->primaryColor->is($color))->toBeTrue();
});

test('secondary color relation returns the pet secondary color', function () {
    $color = Color::factory()->create();
    $pet = Pet::factory()->for($color, 'secondaryColor')->create();

    expect($pet->secondaryColor->is($color))->toBeTrue();
});

test('fur type relation returns the pet fur type', function () {
    $furType = FurType::factory()->create();
    $pet = Pet::factory()->for($furType)->create();

    expect($pet->furType->is($furType))->toBeTrue();
});

test('images relation only returns images belonging to the pet', function () {
    $pet = Pet::factory()->create();

    $ownImage = PetImage::factory()->for($pet)->create();
    PetImage::factory()->create();

    expect($pet->images)->toHaveCount(1)
        ->and($pet->images->first()->is($ownImage))->toBeTrue();
});

test('sicknesses relation exposes the diagnosis pivot data', function () {
    $pet = Pet::factory()->create();
    $sickness = Sickness::factory()->create();

    $pet->sicknesses()->attach($sickness, [
        'diagnosed_at' => '2026-01-10',
        'status' => 'active',
        'treatment_notes' => 'Started antibiotics.',
    ]);

    $attached = $pet->sicknesses()->first();

    expect($attached->is($sickness))->toBeTrue()
        ->and($attached->pivot)->toBeInstanceOf(PetSickness::class)
        ->and($attached->pivot->status)->toBe('active')
        ->and($attached->pivot->treatment_notes)->toBe('Started antibiotics.');
});

test('vaccines relation exposes the administration pivot data', function () {
    $pet = Pet::factory()->create();
    $vaccine = Vaccine::factory()->create();

    $pet->vaccines()->attach($vaccine, [
        'administered_date' => '2026-01-05',
        'due_date' => '2027-01-05',
        'status' => 'administered',
    ]);

    $attached = $pet->vaccines()->first();

    expect($attached->is($vaccine))->toBeTrue()
        ->and($attached->pivot)->toBeInstanceOf(PetVaccine::class)
        ->and($attached->pivot->due_date->toDateString())->toBe('2027-01-05')
        ->and($attached->pivot->status)->toBe('administered');
});

test('age in words is null when birth date is unknown', function () {
    $pet = Pet::factory()->create(['birth_date' => null]);

    expect($pet->age_in_words)->toBeNull();
});

test('age in words combines years and months', function () {
    $pet = Pet::factory()->create(['birth_date' => now()->subYears(1)->subMonths(3)]);

    expect($pet->age_in_words)->toBe('1 year and 3 months');
});

test('age in words omits years when the pet is under a year old', function () {
    $pet = Pet::factory()->create(['birth_date' => now()->subMonths(5)]);

    expect($pet->age_in_words)->toBe('5 months');
});

test('age in words uses singular forms for one year and one month', function () {
    $pet = Pet::factory()->create(['birth_date' => now()->subYears(1)->subMonths(1)]);

    expect($pet->age_in_words)->toBe('1 year and 1 month');
});

test('stamps created_by on the sickness pivot record when authenticated', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $pet = Pet::factory()->create();
    $sickness = Sickness::factory()->create();

    $pet->sicknesses()->attach($sickness, [
        'diagnosed_at' => '2026-01-10',
        'status' => 'active',
    ]);

    expect($pet->sicknesses()->first()->pivot->created_by)->toBe($user->id);
});
