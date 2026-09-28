<?php

use App\Models\Pet;
use App\Models\PetTreatment;
use App\Models\PetVaccine;
use App\Models\Shelter;
use App\Models\Species;
use App\Models\Treatment;
use App\Models\Vaccine;

beforeEach(function () {
    $this->travelTo('2026-09-29');

    $this->dog = Species::factory()->create(['name' => 'Cão']);
    $this->cat = Species::factory()->create(['name' => 'Gato']);
    $this->shelter = Shelter::factory()->create();

    $this->polyvalent = Vaccine::factory()->hasAttached($this->dog)->create(['name' => 'Polivalente Canina (DHPPi)', 'frequency_months' => 36]);
    $this->rabies = Vaccine::factory()->hasAttached($this->dog)->create(['name' => 'Antirrábica (Raiva)', 'frequency_months' => 36]);
    Vaccine::factory()->hasAttached($this->cat)->create(['name' => 'Antirrábica (Raiva)']);
    $this->triple = Vaccine::factory()->hasAttached($this->cat)->create(['name' => 'Tríplice Felina (FVRCP)']);
    Vaccine::factory()->hasAttached($this->cat)->create(['name' => 'Leucemia Felina (FeLV)']);
    $this->deworming = Treatment::factory()->create(['name' => 'Desparasitação interna + externa', 'frequency_months' => 3]);
});

/**
 * Create a pet with a Portugal Zoófilo ref (the factory always sets a PET ref).
 *
 * @param  array<string, mixed>  $attributes
 */
function afamaPet(Shelter $shelter, Species $species, string $ref, array $attributes = []): Pet
{
    $pet = Pet::factory()->for($shelter)->create(['species_id' => $species->id, ...$attributes]);
    $pet->update(['ref' => $ref]);

    return $pet;
}

/**
 * @return array<string, mixed>
 */
function afamaOptions(Shelter $shelter, array $options = []): array
{
    $fixtures = base_path('tests/Fixtures/Afama');

    return [
        '--shelter' => $shelter->id,
        '--dogs' => "{$fixtures}/dogs.csv",
        '--cats' => "{$fixtures}/cats.csv",
        '--clinical' => "{$fixtures}/clinical.csv",
        ...$options,
    ];
}

test('imports a dog\'s last doses with their next dates, its Nexgard round and its clinical notes', function () {
    $abby = afamaPet($this->shelter, $this->dog, 'PZ101', ['clinical_notes' => 'Castrada no canil.']);

    $this->artisan('app:import-afama-health-sheet', afamaOptions($this->shelter))->assertSuccessful();

    $polyvalent = PetVaccine::query()->where('pet_id', $abby->id)->where('vaccine_id', $this->polyvalent->id)->sole();
    $rabies = PetVaccine::query()->where('pet_id', $abby->id)->where('vaccine_id', $this->rabies->id)->sole();
    $deworming = PetTreatment::query()->where('pet_id', $abby->id)->sole();

    expect($polyvalent->administered_date->toDateString())->toBe('2025-05-20')
        ->and($polyvalent->due_date->toDateString())->toBe('2028-05-01')
        ->and($polyvalent->status)->toBe('administered')
        ->and($rabies->due_date->toDateString())->toBe('2028-11-01')
        ->and($deworming->treatment_id)->toBe($this->deworming->id)
        ->and($deworming->administered_date->toDateString())->toBe('2026-07-23')
        ->and($deworming->due_date->toDateString())->toBe('2026-10-23')
        ->and($deworming->product)->toBe('Nexgard')
        ->and($abby->fresh()->clinical_notes)->toBe("Castrada no canil.\n\nPeso: 36 kg - Abril2026\nDiabetes - 2024\nVigiar");
});

test('ignores a dose dated in the future and schedules its next date instead', function () {
    $lana = afamaPet($this->shelter, $this->dog, 'PZ102');

    $this->artisan('app:import-afama-health-sheet', afamaOptions($this->shelter))
        ->expectsOutputToContain('2027-06-02 is in the future; ignored.')
        ->assertSuccessful();

    $rabies = PetVaccine::query()->where('pet_id', $lana->id)->sole();

    expect($rabies->status)->toBe('scheduled')
        ->and($rabies->administered_date)->toBeNull()
        ->and($rabies->due_date->toDateString())->toBe('2028-05-20');
});

test('matches by chip when the number is unknown, reports a differing chip, and never matches another shelter\'s pet', function () {
    afamaPet($this->shelter, $this->dog, 'PZ103', ['name' => 'Zoe', 'chip' => '960035000530227']);
    $caramelo = afamaPet($this->shelter, $this->dog, 'PZ555', ['chip' => '900026000553483']);
    $outsider = afamaPet(Shelter::factory()->create(), $this->dog, 'PZ888');

    $this->artisan('app:import-afama-health-sheet', afamaOptions($this->shelter))
        ->expectsOutputToContain('Chip 900085000530227 differs from the pet\'s chip 960035000530227')
        ->expectsOutputToContain('No pet found for "Intruso"')
        ->assertSuccessful();

    expect(PetVaccine::query()->where('pet_id', $outsider->id)->exists())->toBeFalse()
        ->and(PetTreatment::query()->where('pet_id', $caramelo->id)->exists())->toBeFalse()
        ->and($caramelo->fresh()->clinical_notes)->toBeNull();
});

test('skips a row whose number belongs to another animal with a different chip and name', function () {
    $ema = afamaPet($this->shelter, $this->dog, 'PZ104', ['name' => 'Ema', 'chip' => '991001002024964']);

    $this->artisan('app:import-afama-health-sheet', afamaOptions($this->shelter))
        ->expectsOutputToContain('"Caramelo" (chip 900026000553480) doesn\'t match PZ104 Ema')
        ->assertSuccessful();

    expect(PetVaccine::query()->where('pet_id', $ema->id)->exists())->toBeFalse();
});

test('imports cats with the cat vaccines and reports the vaccine columns with no catalogue vaccine', function () {
    $cookie = afamaPet($this->shelter, $this->cat, 'PZ201');

    $this->artisan('app:import-afama-health-sheet', afamaOptions($this->shelter))
        ->expectsOutputToContain('"Último RCPCh (Vacina)" has no catalogue vaccine')
        ->assertSuccessful();

    expect(PetVaccine::query()->where('pet_id', $cookie->id)->sole()->vaccine_id)->toBe($this->triple->id)
        ->and(PetTreatment::query()->where('pet_id', $cookie->id)->sole()->due_date->toDateString())->toBe('2026-09-04')
        ->and($cookie->fresh()->clinical_notes)->toBe('Notas: relembrar Ana Carvalho');
});

test('matches a clinical row with no number by a name only one resident pet has', function () {
    $julie = afamaPet($this->shelter, $this->dog, 'PZ12194', ['name' => 'Julie']);
    afamaPet($this->shelter, $this->dog, 'PZ15000', ['name' => 'Julie', 'status' => 'adopted']);

    $this->artisan('app:import-afama-health-sheet', afamaOptions($this->shelter))->assertSuccessful();

    expect($julie->fresh()->clinical_notes)->toBe('Massa 25-06-2026 Vigiar');
});

test('a re-run updates the imported records instead of duplicating them', function () {
    $abby = afamaPet($this->shelter, $this->dog, 'PZ101');

    $this->artisan('app:import-afama-health-sheet', afamaOptions($this->shelter))->assertSuccessful();
    $this->artisan('app:import-afama-health-sheet', afamaOptions($this->shelter))->assertSuccessful();

    expect(PetVaccine::query()->where('pet_id', $abby->id)->count())->toBe(2)
        ->and(PetTreatment::query()->where('pet_id', $abby->id)->count())->toBe(1)
        ->and($abby->fresh()->clinical_notes)->toBe("Peso: 36 kg - Abril2026\nDiabetes - 2024\nVigiar");
});

test('a dose fulfils the open scheduled vaccination', function () {
    $abby = afamaPet($this->shelter, $this->dog, 'PZ101');
    $abby->vaccines()->attach($this->polyvalent->id, ['status' => 'scheduled', 'due_date' => '2025-06-01']);

    $this->artisan('app:import-afama-health-sheet', afamaOptions($this->shelter))->assertSuccessful();

    $polyvalent = PetVaccine::query()->where('pet_id', $abby->id)->where('vaccine_id', $this->polyvalent->id)->sole();

    expect($polyvalent->status)->toBe('administered')
        ->and($polyvalent->administered_date->toDateString())->toBe('2025-05-20');
});

test('fails without importing when a catalogue vaccine or the deworming treatment is missing', function () {
    $abby = afamaPet($this->shelter, $this->dog, 'PZ101');
    $this->rabies->delete();
    $this->deworming->delete();

    $this->artisan('app:import-afama-health-sheet', afamaOptions($this->shelter))
        ->expectsOutputToContain('Missing from the catalogue: Antirrábica (Raiva) (Cão), treatment "Desparasitação interna + externa"')
        ->assertFailed();

    expect(PetVaccine::query()->where('pet_id', $abby->id)->exists())->toBeFalse();
});

test('saves nothing on a dry run', function () {
    $abby = afamaPet($this->shelter, $this->dog, 'PZ101');

    $this->artisan('app:import-afama-health-sheet', afamaOptions($this->shelter, ['--dry-run' => true]))
        ->expectsOutputToContain('[Dry run, nothing saved] 2 vaccinations, 1 deworming rounds')
        ->assertSuccessful();

    expect(PetVaccine::query()->count())->toBe(0)
        ->and(PetTreatment::query()->count())->toBe(0)
        ->and($abby->fresh()->clinical_notes)->toBeNull();
});
