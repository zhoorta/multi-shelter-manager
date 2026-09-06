<?php

use App\Models\Cage;
use App\Models\Shelter;
use App\Models\Wing;

test('shelter relation returns the shelter the wing belongs to', function () {
    $shelter = Shelter::factory()->create();
    $wing = Wing::factory()->for($shelter)->create();

    expect($wing->shelter->is($shelter))->toBeTrue();
});

test('cages relation only returns cages belonging to the wing', function () {
    $wing = Wing::factory()->create();
    $otherWing = Wing::factory()->create();

    $ownCage = Cage::factory()->for($wing)->create();
    Cage::factory()->for($otherWing)->create();

    expect($wing->cages)->toHaveCount(1)
        ->and($wing->cages->first()->is($ownCage))->toBeTrue();
});
