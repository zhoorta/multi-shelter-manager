<?php

use App\Livewire\Pets\DiagnosisForm;
use App\Models\Pet;
use App\Models\Shelter;
use App\Models\Sickness;
use App\Models\Species;
use App\Models\User;
use Livewire\Livewire;

/**
 * @return array{0: Shelter, 1: Pet, 2: Sickness}
 */
function petWithSpeciesSickness(): array
{
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();
    $sickness = Sickness::factory()->create();
    $sickness->species()->attach($pet->species_id);

    return [$shelter, $pet, $sickness];
}

test('guests are redirected to the login page', function () {
    $pet = Pet::factory()->create();

    $this->get(route('pets.diagnose', $pet))->assertRedirect(route('login'));
});

test('admins are forbidden from viewing the form', function () {
    $this->actingAs(User::factory()->admin()->create());

    $this->get(route('pets.diagnose', Pet::factory()->create()))->assertForbidden();
});

test('viewers are forbidden from viewing the form', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();
    $this->actingAs(User::factory()->forShelter($shelter, 'viewer')->create());

    $this->get(route('pets.diagnose', $pet))->assertForbidden();
});

test('staff can view the diagnosis form for a pet in their shelter', function () {
    [$shelter, $pet] = petWithSpeciesSickness();
    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    $this->get(route('pets.diagnose', $pet))->assertOk();
});

test('returns 404 when the pet belongs to another shelter', function () {
    $pet = Pet::factory()->for(Shelter::factory())->create();
    $this->actingAs(User::factory()->forShelter(Shelter::factory()->create(), 'staff')->create());

    $this->get(route('pets.diagnose', $pet))->assertNotFound();
});

test('only offers sicknesses linked to the pet species', function () {
    [$shelter, $pet, $sickness] = petWithSpeciesSickness();
    $sickness->update(['name' => 'Parvovirus']);
    Sickness::factory()->create(['name' => 'Feline Leukemia'])->species()->attach(Species::factory()->create());
    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    $this->get(route('pets.diagnose', $pet))
        ->assertSee('Parvovirus')
        ->assertDontSee('Feline Leukemia');
});

test('creates an active diagnosis dated today by default', function () {
    [$shelter, $pet, $sickness] = petWithSpeciesSickness();
    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    Livewire::test(DiagnosisForm::class, ['pet' => $pet])
        ->set('sicknessId', (string) $sickness->id)
        ->set('treatmentNotes', 'Antibiotics for 10 days')
        ->call('saveDiagnosis')
        ->assertHasNoErrors()
        ->assertRedirect(route('pets.show', $pet));

    $diagnosis = $pet->sicknesses()->first()->pivot;
    expect($diagnosis->sickness_id)->toBe($sickness->id)
        ->and($diagnosis->diagnosed_at->toDateString())->toBe(now()->toDateString())
        ->and($diagnosis->status)->toBe('active')
        ->and($diagnosis->resolved_at)->toBeNull()
        ->and($diagnosis->treatment_notes)->toBe('Antibiotics for 10 days');
});

test('allows the same sickness to be diagnosed more than once', function () {
    [$shelter, $pet, $sickness] = petWithSpeciesSickness();
    $pet->sicknesses()->attach($sickness, ['diagnosed_at' => '2026-01-10', 'status' => 'treated']);
    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    Livewire::test(DiagnosisForm::class, ['pet' => $pet])
        ->set('sicknessId', (string) $sickness->id)
        ->call('saveDiagnosis')
        ->assertHasNoErrors();

    expect($pet->sicknesses()->count())->toBe(2);
});

test('marking a diagnosis as treated suggests today as the resolution date', function () {
    [$shelter, $pet] = petWithSpeciesSickness();
    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    Livewire::test(DiagnosisForm::class, ['pet' => $pet])
        ->set('status', 'treated')
        ->assertSet('resolvedAt', now()->toDateString())
        ->set('status', 'chronic')
        ->assertSet('resolvedAt', '');
});

test('only stores a resolution date for treated diagnoses', function (string $status, ?string $expectedResolvedAt) {
    [$shelter, $pet, $sickness] = petWithSpeciesSickness();
    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    Livewire::test(DiagnosisForm::class, ['pet' => $pet])
        ->set('sicknessId', (string) $sickness->id)
        ->set('diagnosedAt', '2026-03-01')
        ->set('status', $status)
        ->set('resolvedAt', '2026-03-20')
        ->call('saveDiagnosis')
        ->assertHasNoErrors();

    expect($pet->sicknesses()->first()->pivot->resolved_at?->toDateString())->toBe($expectedResolvedAt);
})->with([
    'treated' => ['treated', '2026-03-20'],
    'chronic' => ['chronic', null],
]);

test('requires a sickness and a diagnosis date', function () {
    [$shelter, $pet] = petWithSpeciesSickness();
    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    Livewire::test(DiagnosisForm::class, ['pet' => $pet])
        ->set('diagnosedAt', '')
        ->call('saveDiagnosis')
        ->assertHasErrors(['sicknessId' => 'required', 'diagnosedAt' => 'required']);
});

test('rejects a sickness that does not belong to the pet species', function () {
    [$shelter, $pet] = petWithSpeciesSickness();
    $otherSickness = Sickness::factory()->create();
    $otherSickness->species()->attach(Species::factory()->create());
    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    Livewire::test(DiagnosisForm::class, ['pet' => $pet])
        ->set('sicknessId', (string) $otherSickness->id)
        ->call('saveDiagnosis')
        ->assertHasErrors(['sicknessId' => 'exists']);
});

test('rejects an unknown status', function () {
    [$shelter, $pet, $sickness] = petWithSpeciesSickness();
    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    Livewire::test(DiagnosisForm::class, ['pet' => $pet])
        ->set('sicknessId', (string) $sickness->id)
        ->set('status', 'cured')
        ->call('saveDiagnosis')
        ->assertHasErrors(['status' => 'in']);
});

test('rejects a resolution date before the diagnosis date', function () {
    [$shelter, $pet, $sickness] = petWithSpeciesSickness();
    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    Livewire::test(DiagnosisForm::class, ['pet' => $pet])
        ->set('sicknessId', (string) $sickness->id)
        ->set('diagnosedAt', '2026-03-01')
        ->set('status', 'treated')
        ->set('resolvedAt', '2026-02-01')
        ->call('saveDiagnosis')
        ->assertHasErrors(['resolvedAt' => 'after_or_equal']);
});

test('loads the existing diagnosis when editing', function () {
    [$shelter, $pet, $sickness] = petWithSpeciesSickness();
    $pet->sicknesses()->attach($sickness, [
        'diagnosed_at' => '2026-02-01',
        'status' => 'treated',
        'resolved_at' => '2026-02-20',
        'treatment_notes' => 'Ear drops',
    ]);
    $diagnosis = $pet->sicknesses()->first()->pivot;
    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    Livewire::test(DiagnosisForm::class, ['pet' => $pet, 'petSickness' => $diagnosis])
        ->assertSet('sicknessId', (string) $sickness->id)
        ->assertSet('diagnosedAt', '2026-02-01')
        ->assertSet('status', 'treated')
        ->assertSet('resolvedAt', '2026-02-20')
        ->assertSet('treatmentNotes', 'Ear drops');
});

test('updates an existing diagnosis instead of creating a new one', function () {
    [$shelter, $pet, $sickness] = petWithSpeciesSickness();
    $pet->sicknesses()->attach($sickness, ['diagnosed_at' => '2026-02-01', 'status' => 'active']);
    $diagnosis = $pet->sicknesses()->first()->pivot;
    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    Livewire::test(DiagnosisForm::class, ['pet' => $pet, 'petSickness' => $diagnosis])
        ->set('status', 'treated')
        ->set('resolvedAt', '2026-02-15')
        ->call('saveDiagnosis')
        ->assertHasNoErrors();

    $updated = $diagnosis->fresh();
    expect($pet->sicknesses()->count())->toBe(1)
        ->and($updated->status)->toBe('treated')
        ->and($updated->resolved_at->toDateString())->toBe('2026-02-15')
        ->and($updated->diagnosed_at->toDateString())->toBe('2026-02-01');
});

test('returns 404 when editing a diagnosis that does not belong to the given pet', function () {
    [$shelter, $pet, $sickness] = petWithSpeciesSickness();
    $otherPet = Pet::factory()->for($shelter)->create();
    $otherPet->sicknesses()->attach($sickness, ['diagnosed_at' => now()]);
    $otherDiagnosis = $otherPet->sicknesses()->first()->pivot;
    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    $this->get(route('pets.diagnose.edit', [$pet, $otherDiagnosis]))->assertNotFound();
});
