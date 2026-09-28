<?php

use App\Livewire\Admin\ManageTreatments;
use App\Models\Species;
use App\Models\Treatment;
use App\Models\User;
use Livewire\Livewire;

test('staff are forbidden from the treatments catalogue', function () {
    $this->actingAs(User::factory()->create());

    $this->get(route('admin.treatments.index'))->assertForbidden();
});

test('admins can create a treatment with a frequency and species', function () {
    $this->actingAs(User::factory()->admin()->create());
    $dogs = Species::factory()->create();

    Livewire::test(ManageTreatments::class)
        ->set('treatmentName', 'Desparasitação interna')
        ->set('treatmentFrequencyMonths', '3')
        ->set('treatmentSpeciesIds', [$dogs->id])
        ->call('saveTreatment')
        ->assertHasNoErrors()
        ->assertDispatched('modal-close', name: 'treatment-form');

    $treatment = Treatment::query()->sole();
    expect($treatment->name)->toBe('Desparasitação interna')
        ->and($treatment->frequency_months)->toBe(3)
        ->and($treatment->species->pluck('id')->all())->toBe([$dogs->id]);
});

test('requires a name and a frequency between 1 and 120 months', function () {
    $this->actingAs(User::factory()->admin()->create());

    Livewire::test(ManageTreatments::class)
        ->set('treatmentFrequencyMonths', '0')
        ->call('saveTreatment')
        ->assertHasErrors(['treatmentName' => 'required', 'treatmentFrequencyMonths' => 'min']);
});

test('admins can soft delete a treatment', function () {
    $this->actingAs(User::factory()->admin()->create());
    $treatment = Treatment::factory()->create();

    Livewire::test(ManageTreatments::class)->call('deleteTreatment', $treatment->id);

    expect($treatment->fresh()->trashed())->toBeTrue();
});
