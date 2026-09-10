<?php

use App\Models\Facility;
use App\Models\Shelter;
use App\Models\Wing;

test('shelter relation returns the shelter the facility belongs to', function () {
    $shelter = Shelter::factory()->create();
    $facility = Facility::factory()->for($shelter)->create();

    expect($facility->shelter->is($shelter))->toBeTrue();
});

test('wings relation only returns wings belonging to the facility', function () {
    $facility = Facility::factory()->create();
    $otherFacility = Facility::factory()->create();

    $ownWing = Wing::factory()->for($facility)->create();
    Wing::factory()->for($otherFacility)->create();

    expect($facility->wings)->toHaveCount(1)
        ->and($facility->wings->first()->is($ownWing))->toBeTrue();
});
