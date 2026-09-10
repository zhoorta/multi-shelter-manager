<?php

use App\Models\Cage;
use App\Models\Facility;
use App\Models\Wing;

test('facility relation returns the facility the wing belongs to', function () {
    $facility = Facility::factory()->create();
    $wing = Wing::factory()->for($facility)->create();

    expect($wing->facility->is($facility))->toBeTrue();
});

test('cages relation only returns cages belonging to the wing', function () {
    $wing = Wing::factory()->create();
    $otherWing = Wing::factory()->create();

    $ownCage = Cage::factory()->for($wing)->create();
    Cage::factory()->for($otherWing)->create();

    expect($wing->cages)->toHaveCount(1)
        ->and($wing->cages->first()->is($ownCage))->toBeTrue();
});
