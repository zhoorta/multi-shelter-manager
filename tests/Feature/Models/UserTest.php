<?php

use App\Models\Shelter;
use App\Models\User;

test('managers and staff can edit their current shelter but viewers cannot', function (string $role, bool $canEdit, bool $isViewer) {
    $shelter = Shelter::factory()->create();
    $user = User::factory()->forShelter($shelter, $role)->create();

    expect($user->canEditCurrentShelter())->toBe($canEdit)
        ->and($user->isViewerOfCurrentShelter())->toBe($isViewer);
})->with([
    'manager' => ['manager', true, false],
    'staff' => ['staff', true, false],
    'viewer' => ['viewer', false, true],
]);

test('uses the role of the current shelter when the user belongs to several', function () {
    $viewedShelter = Shelter::factory()->create();
    $staffedShelter = Shelter::factory()->create();
    $user = User::factory()->forShelter($viewedShelter, 'viewer')->create();
    $user->shelters()->attach($staffedShelter, ['role' => 'staff']);

    expect($user->canEditCurrentShelter())->toBeFalse();

    $user->update(['current_shelter_id' => $staffedShelter->id]);

    expect($user->canEditCurrentShelter())->toBeTrue()
        ->and($user->isViewerOfCurrentShelter())->toBeFalse();
});

test('users without a current shelter cannot edit', function () {
    $admin = User::factory()->admin()->create();

    expect($admin->canEditCurrentShelter())->toBeFalse()
        ->and($admin->isViewerOfCurrentShelter())->toBeFalse();
});
