<?php

use App\Livewire\Reports\ShelterReports;
use App\Models\Adoption;
use App\Models\Cage;
use App\Models\Facility;
use App\Models\Member;
use App\Models\MemberPayment;
use App\Models\Pet;
use App\Models\Shelter;
use App\Models\Sickness;
use App\Models\Sponsorship;
use App\Models\SponsorshipPayment;
use App\Models\User;
use App\Models\Vaccine;
use App\Models\Wing;
use Livewire\Livewire;

beforeEach(function () {
    $this->travelTo('2026-09-25 10:00:00');
    $this->shelter = Shelter::factory()->create();
    $this->manager = User::factory()->forShelter($this->shelter, 'manager')->create();
});

function sponsorshipPayment(Pet $pet, string $start, string $end, string $paidOn, float $value, ?Sponsorship $sponsorship = null): SponsorshipPayment
{
    return SponsorshipPayment::factory()
        ->for($sponsorship ?? Sponsorship::factory()->for($pet)->create())
        ->create(['start_date' => $start, 'end_date' => $end, 'payment_date' => $paidOn, 'payment_value' => $value]);
}

test('income is counted by payment date for each source, only for the shelter', function () {
    $pet = Pet::factory()->for($this->shelter)->create();
    sponsorshipPayment($pet, '2025-12-01', '2026-11-30', '2025-12-20', 120);
    sponsorshipPayment($pet, '2026-03-01', '2026-03-31', '2026-03-02', 10);

    $member = Member::factory()->for($this->shelter)->create(['join_date' => '2020-01-01']);
    MemberPayment::factory()->for($member)->create(['payment_date' => '2026-02-10', 'payment_value' => 12, 'payment_method' => 'cash']);
    MemberPayment::factory()->for($member)->create(['type' => 'joining_fee', 'start_date' => null, 'end_date' => null, 'payment_date' => '2026-02-10', 'payment_value' => 5, 'payment_method' => 'bank_transfer']);

    $adopted = Pet::factory()->for($this->shelter)->create();
    Adoption::factory()->for($adopted)->create(['adoption_date' => '2026-04-15', 'adoption_fee' => 50]);

    $otherShelterPet = Pet::factory()->create();
    sponsorshipPayment($otherShelterPet, '2026-03-01', '2026-03-31', '2026-03-02', 999);
    MemberPayment::factory()->create(['payment_date' => '2026-02-10', 'payment_value' => 999]);

    $finance = Livewire::actingAs($this->manager)
        ->test(ShelterReports::class, ['period' => '2026', 'tab' => 'finances'])
        ->assertSee('Sponsorships ending soon')
        ->get('financeReport');

    expect($finance['totals'])->toBe([
        'income' => 77.0,
        'sponsorships' => 10.0,
        'membershipFees' => 12.0,
        'joiningFees' => 5.0,
        'adoptionFees' => 50.0,
    ])
        ->and($finance['incomeBuckets'][1])->toBe(['sponsorships' => 0.0, 'membershipFees' => 12.0, 'joiningFees' => 5.0, 'adoptionFees' => 0.0])
        ->and($finance['incomeBuckets'][2]['sponsorships'])->toBe(10.0)
        ->and($finance['paymentsByMethod'])->toBe([
            ['label' => 'Cash', 'value' => 12.0],
            ['label' => 'Bank transfer', 'value' => 5.0],
        ]);
});

test('active sponsorships follow the paid periods and the monthly value spreads each payment', function () {
    $pet = Pet::factory()->for($this->shelter)->create();
    sponsorshipPayment($pet, '2026-01-01', '2026-12-31', '2026-01-05', 120);
    sponsorshipPayment(Pet::factory()->for($this->shelter)->create(), '2026-03-01', '2026-04-30', '2026-03-01', 20);

    $finance = Livewire::actingAs($this->manager)
        ->test(ShelterReports::class, ['period' => '2026'])
        ->get('financeReport');

    expect($finance['activeSponsorshipsBuckets'])->toBe([1, 1, 2, 2, 1, 1, 1, 1, 1, null, null, null])
        ->and($finance['sponsorships'])->toBe(['active' => 1, 'sponsoredPets' => 1, 'monthlyValue' => 10.0]);
});

test('sponsorships ending soon leave out renewed ones and those ending later', function () {
    $ending = Pet::factory()->for($this->shelter)->create(['name' => 'Ending']);
    sponsorshipPayment($ending, '2026-09-01', '2026-10-10', '2026-09-01', 10);

    $renewed = Pet::factory()->for($this->shelter)->create(['name' => 'Renewed']);
    $renewedSponsorship = Sponsorship::factory()->for($renewed)->create();
    sponsorshipPayment($renewed, '2026-09-01', '2026-10-01', '2026-09-01', 10, $renewedSponsorship);
    sponsorshipPayment($renewed, '2026-10-02', '2027-10-01', '2026-09-20', 100, $renewedSponsorship);

    $later = Pet::factory()->for($this->shelter)->create(['name' => 'Later']);
    sponsorshipPayment($later, '2026-09-01', '2026-12-31', '2026-09-01', 10);

    $ending = Livewire::actingAs($this->manager)
        ->test(ShelterReports::class)
        ->get('financeReport')['sponsorshipsEnding'];

    expect($ending->map(fn (SponsorshipPayment $payment): string => $payment->sponsorship->pet->name)->all())->toBe(['Ending']);
});

test('member figures count active and new members and the fees expected in the period', function () {
    Member::factory()->for($this->shelter)->create(['join_date' => '2020-01-01', 'membership_fee' => 12, 'membership_fee_frequency' => 'yearly']);
    Member::factory()->for($this->shelter)->create(['join_date' => '2025-07-01', 'membership_fee' => 3, 'membership_fee_frequency' => 'quarterly']);
    Member::factory()->for($this->shelter)->create(['join_date' => '2020-01-01', 'status' => 'left']);

    $members = Livewire::actingAs($this->manager)
        ->test(ShelterReports::class, ['period' => '2025'])
        ->get('financeReport')['members'];

    expect($members)->toMatchArray([
        'active' => 2,
        'joined' => 1,
        'inArrears' => 2,
        'feesExpected' => 18.0,
        'feesCollected' => 0.0,
    ]);
});

test('occupancy compares the pets in the shelter with the capacity of its cages', function () {
    $wing = Wing::factory()->for(Facility::factory()->for($this->shelter)->create(['name' => 'Main']))->create(['name' => 'Dogs']);
    $cage = Cage::factory()->for($wing)->create(['capacity' => 4]);
    Pet::factory()->for($this->shelter)->count(2)->create(['cage_id' => $cage->id, 'checkin_date' => '2026-01-10']);
    Pet::factory()->for($this->shelter)->create(['checkin_date' => '2026-01-10']);
    Pet::factory()->for($this->shelter)->create(['cage_id' => $cage->id, 'checkin_date' => '2026-01-10', 'date_of_death' => '2026-02-01']);
    Cage::factory()->create(['capacity' => 50]);

    $occupancy = Livewire::actingAs($this->manager)
        ->test(ShelterReports::class, ['period' => '2026', 'tab' => 'occupancy'])
        ->assertSee('Occupancy by wing')
        ->get('occupancyReport');

    expect($occupancy['totals'])->toBe(['capacity' => 4, 'housed' => 2, 'withoutCage' => 1, 'inFosterFamilies' => 0, 'rate' => 75])
        ->and($occupancy['wings'])->toBe([['label' => 'Main · Dogs', 'value' => 50, 'display' => '2 / 4 (50%)']])
        ->and(array_slice($occupancy['rateBuckets'], 0, 2))->toBe([100, 75]);
});

test('health counts vaccinations and diagnoses in the period and the sterilised share of pets in the shelter', function () {
    $vaccine = Vaccine::factory()->create(['name' => 'Rabies']);
    $sickness = Sickness::factory()->create(['name' => 'Otitis']);
    $neutered = Pet::factory()->for($this->shelter)->create(['is_neutered' => true]);
    $pet = Pet::factory()->for($this->shelter)->create();

    $neutered->vaccines()->attach($vaccine, ['status' => 'administered', 'administered_date' => '2026-03-05']);
    $neutered->vaccines()->attach($vaccine, ['status' => 'administered', 'administered_date' => '2025-03-05']);
    $pet->vaccines()->attach($vaccine, ['status' => 'scheduled', 'due_date' => '2026-09-01']);
    $pet->sicknesses()->attach($sickness, ['diagnosed_at' => '2026-05-01', 'status' => 'chronic']);
    $pet->sicknesses()->attach($sickness, ['diagnosed_at' => '2024-05-01', 'status' => 'treated']);
    Pet::factory()->create()->vaccines()->attach($vaccine, ['status' => 'administered', 'administered_date' => '2026-03-05']);

    $health = Livewire::actingAs($this->manager)
        ->test(ShelterReports::class, ['period' => '2026', 'tab' => 'health'])
        ->assertSee('Diagnoses by sickness')
        ->get('healthReport');

    expect($health['totals'])->toBe([
        'vaccinations' => 1,
        'overdueVaccinations' => 1,
        'diagnoses' => 1,
        'openCases' => 1,
        'neuteredRate' => 50,
    ])
        ->and($health['vaccinationBuckets'][2])->toBe(1)
        ->and($health['vaccinationsByVaccine'])->toBe([['label' => 'Rabies', 'value' => 1]])
        ->and($health['diagnosesBySickness'])->toBe([['label' => 'Otitis', 'value' => 1]]);
});

test('an unknown tab shows the animals section', function () {
    Livewire::actingAs($this->manager)
        ->test(ShelterReports::class, ['tab' => 'secrets'])
        ->assertSee('Intakes and exits');
});

test('the print button prints the open tab with its own title', function () {
    Livewire::actingAs($this->manager)
        ->test(ShelterReports::class, ['period' => '2025', 'tab' => 'finances'])
        ->assertSee(route('reports.print', ['tab' => 'finances', 'period' => '2025']));

    $this->actingAs($this->manager)
        ->get(route('reports.print', ['tab' => 'finances', 'period' => '2025']))
        ->assertOk()
        ->assertSee('Financial report 2025')
        ->assertSee('Sponsorships ending soon')
        ->assertDontSee('Intakes and exits');
});
