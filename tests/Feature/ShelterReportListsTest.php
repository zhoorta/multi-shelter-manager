<?php

use App\Livewire\Reports\ShelterReports;
use App\Models\Adoption;
use App\Models\Pet;
use App\Models\Shelter;
use App\Models\Species;
use App\Models\User;
use App\Models\Vaccine;
use Livewire\Livewire;

beforeEach(function () {
    $this->travelTo('2026-09-25 10:00:00');
    $this->shelter = Shelter::factory()->create();
    $this->dog = Species::factory()->create(['name' => 'Dog']);
    $this->actingAs(User::factory()->forShelter($this->shelter, 'manager')->create());
});

/**
 * Call an export of the reports page for a year and return the CSV text.
 */
function exportedList(string $method, string $year = '2025'): string
{
    $response = Livewire::withQueryParams(['period' => $year])
        ->test(ShelterReports::class)
        ->call($method)
        ->assertFileDownloaded();

    return base64_decode((string) ($response->effects['download']['content'] ?? ''));
}

test('exports the animals that entered in the year with chip, birth date and box', function () {
    Pet::factory()->for($this->shelter)->create(['species_id' => $this->dog->id, 'name' => 'Abby', 'chip' => '941000023525681', 'birth_date' => '2020-03-04', 'checkin_date' => '2025-03-10']);
    Pet::factory()->for($this->shelter)->create(['species_id' => $this->dog->id, 'name' => 'Earlier', 'checkin_date' => '2024-12-31']);
    Pet::factory()->for(Shelter::factory()->create())->create(['species_id' => $this->dog->id, 'name' => 'Outsider', 'checkin_date' => '2025-03-10']);

    $csv = exportedList('exportIntakes');

    expect($csv)
        ->toContain('Reference;Name;Species;Gender;Microchip;"Birth Date";"Checkin Date";Facility;Wing;Cage')
        ->toContain(';Abby;Dog;')
        ->toContain('941000023525681;04/03/2020;10/03/2025')
        ->not->toContain('Earlier')
        ->not->toContain('Outsider');
});

test('exports the approved adoptions of the year with the owner and the registry transfer', function () {
    $pet = Pet::factory()->for($this->shelter)->create(['species_id' => $this->dog->id, 'name' => 'Abby']);
    Adoption::factory()->for($pet)->create(['name' => 'Maria Silva', 'adoption_date' => '2025-06-01', 'siac_transferred_at' => '2025-06-20']);
    Adoption::factory()->for($pet)->create(['name' => 'Rejected Owner', 'adoption_date' => '2025-06-02', 'application_status' => 'Rejected']);
    Adoption::factory()->for($pet)->create(['name' => 'Other Year', 'adoption_date' => '2024-06-01']);
    Adoption::factory()->for(Pet::factory()->for(Shelter::factory()->create()))->create(['name' => 'Outsider', 'adoption_date' => '2025-06-01']);

    $csv = exportedList('exportAdoptions');

    expect($csv)
        ->toContain('Maria Silva')
        ->toContain('01/06/2025;')
        ->toContain('20/06/2025')
        ->not->toContain('Rejected Owner')
        ->not->toContain('Other Year')
        ->not->toContain('Outsider');
});

test('exports the deaths of the year', function () {
    Pet::factory()->for($this->shelter)->create(['species_id' => $this->dog->id, 'name' => 'Abby', 'checkin_date' => '2024-01-10', 'date_of_death' => '2025-02-03']);
    Pet::factory()->for($this->shelter)->create(['species_id' => $this->dog->id, 'name' => 'Alive']);

    expect(exportedList('exportDeaths'))
        ->toContain('Abby')
        ->toContain('03/02/2025')
        ->not->toContain('Alive');
});

test('exports the vaccinations given in the year, one row per dose', function () {
    $rabies = Vaccine::factory()->create(['name' => 'Rabies']);
    $pet = Pet::factory()->for($this->shelter)->create(['species_id' => $this->dog->id, 'name' => 'Abby']);
    $pet->vaccines()->attach($rabies->id, ['status' => 'administered', 'administered_date' => '2025-05-01', 'lot_number' => 'L123', 'veterinarian_name' => 'Dr Costa']);
    $pet->vaccines()->attach($rabies->id, ['status' => 'administered', 'administered_date' => '2024-05-01']);
    $pet->vaccines()->attach($rabies->id, ['status' => 'scheduled', 'due_date' => '2025-07-01']);

    $csv = exportedList('exportVaccinations');

    expect($csv)
        ->toContain('01/05/2025;Rabies;L123;"Dr Costa"')
        ->not->toContain('01/05/2024')
        ->and(substr_count($csv, 'Abby'))->toBe(1);
});

test('only managers can use the list exports', function () {
    $this->actingAs(User::factory()->forShelter($this->shelter, 'staff')->create());

    Livewire::test(ShelterReports::class)->assertForbidden();
});
