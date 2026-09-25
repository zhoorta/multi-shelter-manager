<?php

use App\Livewire\Reports\ShelterReportPrint;
use App\Livewire\Reports\ShelterReports;
use App\Models\Adoption;
use App\Models\Pet;
use App\Models\Shelter;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->travelTo('2026-09-25 10:00:00');
    $this->shelter = Shelter::factory()->create();
    $this->manager = User::factory()->forShelter($this->shelter, 'manager')->create();
});

test('managers can open the reports page', function () {
    $this->actingAs($this->manager)
        ->get(route('reports.index'))
        ->assertOk()
        ->assertSee('Intakes and exits');
});

test('staff, viewers and admins cannot open the reports page', function (string $role) {
    $user = $role === 'admin'
        ? User::factory()->admin()->create()
        : User::factory()->forShelter($this->shelter, $role)->create();

    $this->actingAs($user)->get(route('reports.index'))->assertForbidden();
})->with(['staff', 'viewer', 'admin']);

test('only managers see the reports navigation link', function () {
    $staff = User::factory()->forShelter($this->shelter, 'staff')->create();

    $this->actingAs($this->manager)->get(route('dashboard'))->assertSee(route('reports.index'));
    $this->actingAs($staff)->get(route('dashboard'))->assertDontSee(route('reports.index'));
});

test('a year period counts the intakes, adoptions, returns and deaths of that year only', function () {
    $adopted = Pet::factory()->for($this->shelter)->create(['checkin_date' => '2025-03-10']);
    Adoption::factory()->for($adopted)->create(['adoption_date' => '2025-06-10']);

    $returned = Pet::factory()->for($this->shelter)->create(['checkin_date' => '2024-12-01']);
    Adoption::factory()->for($returned)->create(['adoption_date' => '2024-12-20', 'return_date' => '2025-02-01']);

    Pet::factory()->for($this->shelter)->create(['checkin_date' => '2025-03-15', 'date_of_death' => '2025-08-05']);

    $rejected = Pet::factory()->for($this->shelter)->create(['checkin_date' => '2026-01-10']);
    Adoption::factory()->for($rejected)->create(['adoption_date' => '2025-07-01', 'application_status' => 'Rejected']);

    $otherShelterPet = Pet::factory()->create(['checkin_date' => '2025-03-10', 'date_of_death' => '2025-04-01']);
    Adoption::factory()->for($otherShelterPet)->create(['adoption_date' => '2025-03-20']);

    $report = Livewire::actingAs($this->manager)
        ->test(ShelterReports::class, ['period' => '2025'])
        ->get('report');

    expect($report['totals'])->toMatchArray([
        'intakes' => 2,
        'adoptions' => 1,
        'returns' => 1,
        'deaths' => 1,
        'medianDaysToAdoption' => 92,
    ])
        ->and($report['groupedByYear'])->toBeFalse()
        ->and($report['buckets'])->toHaveCount(12)
        ->and(array_column($report['buckets'], 'intakes'))->toBe([0, 0, 2, 0, 0, 0, 0, 0, 0, 0, 0, 0])
        ->and($report['buckets'][5]['adoptions'])->toBe(1)
        ->and($report['buckets'][7]['deaths'])->toBe(1);
});

test('the population at the end of each month leaves out adopted and dead pets and brings back returned ones', function () {
    $adopted = Pet::factory()->for($this->shelter)->create(['checkin_date' => '2026-01-05']);
    Adoption::factory()->for($adopted)->create(['adoption_date' => '2026-02-10', 'return_date' => '2026-04-02']);
    Pet::factory()->for($this->shelter)->create(['checkin_date' => '2026-02-01', 'date_of_death' => '2026-03-15']);

    $buckets = Livewire::actingAs($this->manager)
        ->test(ShelterReports::class, ['period' => '2026'])
        ->get('report')['buckets'];

    expect(array_column($buckets, 'population'))
        ->toBe([1, 1, 0, 1, 1, 1, 1, 1, 1, null, null, null]);
});

test('the default period is the last 12 months and all time is grouped by year', function () {
    Pet::factory()->for($this->shelter)->create(['checkin_date' => '2019-05-01']);

    $component = Livewire::actingAs($this->manager)->test(ShelterReports::class);

    expect($component->get('report')['start']->toDateString())->toBe('2025-10-01')
        ->and($component->get('report')['buckets'])->toHaveCount(12);

    $allTime = $component->set('period', 'all')->get('report');

    expect($allTime['groupedByYear'])->toBeTrue()
        ->and(array_column($allTime['buckets'], 'label'))->toBe(['2019', '2020', '2021', '2022', '2023', '2024', '2025', '2026'])
        ->and($allTime['buckets'][0]['intakes'])->toBe(1);
});

test('choosing a custom period starts from the range on screen and swaps reversed dates', function () {
    $component = Livewire::actingAs($this->manager)
        ->test(ShelterReports::class, ['period' => '2025'])
        ->set('period', 'custom')
        ->assertSet('from', '2025-01-01')
        ->assertSet('to', '2025-12-31');

    $report = $component->set('from', '2026-03-31')->set('to', '2026-02-01')->get('report');

    expect($report['start']->toDateString())->toBe('2026-02-01')
        ->and($report['end']->toDateString())->toBe('2026-03-31')
        ->and($report['buckets'])->toHaveCount(2);
});

test('an invalid custom date falls back to the last 12 months and fills the date inputs', function () {
    Livewire::withQueryParams(['period' => 'custom', 'from' => '2026-02-30', 'to' => '2026-03-31'])
        ->actingAs($this->manager)
        ->test(ShelterReports::class)
        ->assertSet('from', '2025-10-01')
        ->assertSet('to', '2026-09-25');
});

test('adoptions are grouped by age at adoption with the median wait of each group', function () {
    foreach ([['2026-05-01', 10], ['2026-05-01', 30], ['2026-05-01', 40]] as [$birthDate, $daysWaiting]) {
        $pet = Pet::factory()->for($this->shelter)->create(['birth_date' => $birthDate, 'checkin_date' => '2026-06-01']);
        Adoption::factory()->for($pet)->create(['adoption_date' => now()->setDate(2026, 6, 1)->addDays($daysWaiting)]);
    }

    $pet = Pet::factory()->for($this->shelter)->create(['birth_date' => '2018-01-01', 'checkin_date' => '2026-01-01']);
    Adoption::factory()->for($pet)->create(['adoption_date' => '2026-03-01']);

    $report = Livewire::actingAs($this->manager)
        ->test(ShelterReports::class, ['period' => '2026'])
        ->get('report');

    expect($report['adoptionsByAge'])->toBe([
        ['label' => 'Under 1 year', 'value' => 3],
        ['label' => 'Over 7 years', 'value' => 1],
    ])
        ->and($report['medianDaysByAge'])->toBe([
            ['label' => 'Under 1 year', 'value' => 30],
            ['label' => 'Over 7 years', 'value' => 59],
        ]);
});

test('the longest waiting list only shows available pets of the shelter, oldest intake first', function () {
    Pet::factory()->for($this->shelter)->create(['name' => 'Newcomer', 'checkin_date' => '2026-09-01']);
    Pet::factory()->for($this->shelter)->create(['name' => 'Veteran', 'checkin_date' => '2020-01-01']);
    Pet::factory()->for($this->shelter)->create(['name' => 'Resident', 'checkin_date' => '2019-01-01', 'status' => 'not_available']);
    Pet::factory()->create(['name' => 'Stranger', 'checkin_date' => '2018-01-01']);

    $longestWaiting = Livewire::actingAs($this->manager)
        ->test(ShelterReports::class)
        ->get('report')['longestWaiting'];

    expect($longestWaiting->pluck('name')->all())->toBe(['Veteran', 'Newcomer']);
});

test('intakes and adoptions are counted per species', function () {
    $cat = Pet::factory()->for($this->shelter)->create(['checkin_date' => '2026-02-01']);
    Adoption::factory()->for($cat)->create(['adoption_date' => '2026-03-01']);
    Pet::factory()->for($this->shelter)->create(['species_id' => $cat->species_id, 'breed_id' => $cat->breed_id, 'checkin_date' => '2026-04-01']);

    $report = Livewire::actingAs($this->manager)
        ->test(ShelterReports::class, ['period' => '2026'])
        ->get('report');

    expect($report['intakesBySpecies'])->toBe([['label' => $cat->species->name_plural, 'value' => 2]])
        ->and($report['adoptionsBySpecies'])->toBe([['label' => $cat->species->name_plural, 'value' => 1]]);
});

test('the print button carries the period on screen', function () {
    Livewire::actingAs($this->manager)
        ->test(ShelterReports::class, ['period' => '2025'])
        ->assertSee(route('reports.print', ['period' => '2025']))
        ->set('period', 'custom')
        ->set('from', '2025-02-01')
        ->assertSee(route('reports.print', ['period' => 'custom', 'from' => '2025-02-01', 'to' => '2025-12-31']));
});

test('managers can print the activity report of the chosen year', function () {
    $pet = Pet::factory()->for($this->shelter)->create(['checkin_date' => '2025-03-10']);
    Adoption::factory()->for($pet)->create(['adoption_date' => '2025-06-10']);

    $this->actingAs($this->manager)
        ->get(route('reports.print', ['period' => '2025']))
        ->assertOk()
        ->assertSee('Activity report 2025')
        ->assertSee('01/01/2025 – 31/12/2025')
        ->assertSee($this->shelter->name);

    $totals = Livewire::withQueryParams(['period' => '2025'])
        ->actingAs($this->manager)
        ->test(ShelterReportPrint::class)
        ->get('report')['totals'];

    expect($totals)->toMatchArray(['intakes' => 1, 'adoptions' => 1]);
});

test('staff cannot print the activity report', function () {
    $staff = User::factory()->forShelter($this->shelter, 'staff')->create();

    $this->actingAs($staff)->get(route('reports.print'))->assertForbidden();
});
