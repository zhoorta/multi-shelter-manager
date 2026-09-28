<?php

use App\Models\Pet;
use App\Models\PetVaccine;
use App\Models\Vaccine;

test('pending keeps a dose next date open until a later dose of the same vaccine is logged', function () {
    $pet = Pet::factory()->create();
    $vaccine = Vaccine::factory()->create();
    $otherVaccine = Vaccine::factory()->create();
    $pet->vaccines()->attach($vaccine, ['administered_date' => '2025-09-01', 'due_date' => '2026-09-01', 'status' => 'administered']);
    $pet->vaccines()->attach($otherVaccine, ['administered_date' => '2026-09-20', 'status' => 'administered']);

    expect(PetVaccine::query()->pending()->count())->toBe(1);

    $pet->vaccines()->attach($vaccine, ['administered_date' => '2026-09-20', 'status' => 'administered']);

    expect(PetVaccine::query()->pending()->count())->toBe(0);
});

test('pending ignores soft-deleted later doses', function () {
    $pet = Pet::factory()->create();
    $vaccine = Vaccine::factory()->create();
    $pet->vaccines()->attach($vaccine, ['administered_date' => '2025-09-01', 'due_date' => '2026-09-01', 'status' => 'administered']);
    $pet->vaccines()->attach($vaccine, ['administered_date' => '2026-09-20', 'status' => 'administered']);

    PetVaccine::query()->whereDate('administered_date', '2026-09-20')->sole()->delete();

    expect(PetVaccine::query()->pending()->count())->toBe(1);
});

test('pending counts an open scheduled vaccination instead of the dose next date it replans', function () {
    $pet = Pet::factory()->create();
    $vaccine = Vaccine::factory()->create();
    $pet->vaccines()->attach($vaccine, ['administered_date' => '2025-09-01', 'due_date' => '2026-09-01', 'status' => 'administered']);
    $pet->vaccines()->attach($vaccine, ['due_date' => '2026-10-15', 'status' => 'scheduled']);

    expect(PetVaccine::query()->pending()->pluck('status')->all())->toBe(['scheduled']);
});
