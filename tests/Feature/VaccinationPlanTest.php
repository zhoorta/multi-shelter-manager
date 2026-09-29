<?php

use App\Livewire\Pets\VaccinationPlan;
use App\Models\Pet;
use App\Models\Shelter;
use App\Models\Species;
use App\Models\User;
use App\Models\Vaccine;
use Livewire\Livewire;

beforeEach(function () {
    $this->travelTo('2026-09-29');

    $this->shelter = Shelter::factory()->create();
    $this->dogs = Species::factory()->create(['name' => 'Dog']);
    $this->rabies = Vaccine::factory()->create(['name' => 'Rabies']);
    $this->polyvalent = Vaccine::factory()->create(['name' => 'Polyvalent']);
});

/**
 * A dog of the shelter whose last dose of each vaccine has its next one due on the given date.
 *
 * @param  array<int, array{0: Vaccine, 1: string}>  $dueVaccines
 * @param  array<string, mixed>  $attributes
 */
function planDog(Shelter $shelter, Species $dogs, array $dueVaccines, array $attributes = []): Pet
{
    $pet = Pet::factory()->for($shelter)->create(['species_id' => $dogs->id, ...$attributes]);

    foreach ($dueVaccines as [$vaccine, $dueDate]) {
        $pet->vaccines()->attach($vaccine->id, ['status' => 'administered', 'administered_date' => '2024-05-01', 'due_date' => $dueDate]);
    }

    return $pet;
}

test('admins are forbidden from the vaccination plan', function () {
    $this->actingAs(User::factory()->create(['is_admin' => true]));

    $this->get(route('pets.vaccinations.plan'))->assertForbidden();
    $this->get(route('pets.vaccinations.plan.print'))->assertForbidden();
});

test('counts the residents\' open vaccinations per vaccine and month, with earlier ones before the year', function () {
    $this->actingAs(User::factory()->forShelter($this->shelter, 'viewer')->create());

    planDog($this->shelter, $this->dogs, [[$this->rabies, '2027-05-01'], [$this->polyvalent, '2027-05-31']]);
    planDog($this->shelter, $this->dogs, [[$this->rabies, '2027-05-20']]);
    planDog($this->shelter, $this->dogs, [[$this->rabies, '2026-11-01']]);
    planDog($this->shelter, $this->dogs, [[$this->rabies, '2028-01-01']]);
    planDog($this->shelter, $this->dogs, [[$this->rabies, '2027-05-01']], ['status' => 'adopted']);
    planDog(Shelter::factory()->create(), $this->dogs, [[$this->rabies, '2027-05-01']]);
    $revaccinated = planDog($this->shelter, $this->dogs, [[$this->rabies, '2027-06-01']]);
    $revaccinated->vaccines()->attach($this->rabies->id, ['status' => 'administered', 'administered_date' => '2026-09-01', 'due_date' => '2029-09-01']);

    Livewire::withQueryParams(['year' => 2027])
        ->test(VaccinationPlan::class)
        ->assertSet('counts', [
            $this->rabies->id => [5 => 2, 0 => 1],
            $this->polyvalent->id => [5 => 1],
        ]);
});

test('choosing a month lists each due animal once with the vaccines it needs', function () {
    $this->actingAs(User::factory()->forShelter($this->shelter, 'viewer')->create());

    planDog($this->shelter, $this->dogs, [[$this->rabies, '2027-05-01'], [$this->polyvalent, '2027-05-31']], ['name' => 'Abby', 'chip' => '941000023525681']);
    planDog($this->shelter, $this->dogs, [[$this->polyvalent, '2027-05-10']], ['name' => 'Bingo']);
    planDog($this->shelter, $this->dogs, [[$this->rabies, '2027-06-01']], ['name' => 'Charlie']);

    $plan = Livewire::withQueryParams(['year' => 2027])
        ->test(VaccinationPlan::class)
        ->call('selectMonth', 5)
        ->assertDontSee('Charlie');

    expect($plan->html())->toMatch('~Abby.*941000023525681.*Rabies.*01/05/2027.*Polyvalent.*31/05/2027.*Bingo~s');

    $plan->call('selectMonth', 5, $this->rabies->id)
        ->assertSee('Abby')
        ->assertDontSee('Bingo');
});

test('the group vaccination for the months before the current year covers the overdue animals', function () {
    $this->actingAs(User::factory()->forShelter($this->shelter, 'staff')->create());

    planDog($this->shelter, $this->dogs, [[$this->rabies, '2025-05-01']]);

    Livewire::test(VaccinationPlan::class)
        ->call('selectMonth', 0, $this->rabies->id)
        ->assertSee(e(route('pets.vaccinations.group', ['vaccine' => $this->rabies->id, 'due' => 'overdue'])), false);
});

test('only staff get the group vaccination button for the chosen month and vaccine', function (string $role, bool $seesButton) {
    $this->actingAs(User::factory()->forShelter($this->shelter, $role)->create());

    planDog($this->shelter, $this->dogs, [[$this->rabies, '2027-05-01']]);
    $groupUrl = e(route('pets.vaccinations.group', ['vaccine' => $this->rabies->id, 'due' => '2027-05']));

    $plan = Livewire::withQueryParams(['year' => 2027])
        ->test(VaccinationPlan::class)
        ->call('selectMonth', 5, $this->rabies->id);

    $seesButton ? $plan->assertSee($groupUrl, false) : $plan->assertDontSee($groupUrl, false);
})->with([
    'staff' => ['staff', true],
    'viewer' => ['viewer', false],
]);

test('prints the vet list of the month\'s due residents of the shelter only', function () {
    $this->actingAs(User::factory()->forShelter($this->shelter, 'viewer')->create());

    planDog($this->shelter, $this->dogs, [[$this->rabies, '2027-05-01']], ['name' => 'Abby']);
    planDog($this->shelter, $this->dogs, [[$this->polyvalent, '2027-05-01']], ['name' => 'Bingo']);
    planDog(Shelter::factory()->create(), $this->dogs, [[$this->rabies, '2027-05-01']], ['name' => 'Outsider']);

    $this->get(route('pets.vaccinations.plan.print', ['year' => 2027, 'month' => 5, 'vaccine' => $this->rabies->id]))
        ->assertOk()
        ->assertSee('Rabies')
        ->assertSee('Abby')
        ->assertDontSee('Bingo')
        ->assertDontSee('Outsider');
});
