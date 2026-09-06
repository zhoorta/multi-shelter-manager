<?php

use App\Models\PetImage;
use App\Models\Shelter;
use App\Models\User;
use App\Models\Wing;

test('stamps created_by and updated_by when creating a record while authenticated', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $wing = Wing::factory()->for(Shelter::factory())->create();

    expect($wing->created_by)->toBe($user->id)
        ->and($wing->updated_by)->toBe($user->id);
});

test('does not stamp created_by or updated_by when no user is authenticated', function () {
    $wing = Wing::factory()->for(Shelter::factory())->create();

    expect($wing->created_by)->toBeNull()
        ->and($wing->updated_by)->toBeNull();
});

test('stamps updated_by with the current user on update without changing created_by', function () {
    $creator = User::factory()->create();
    $this->actingAs($creator);
    $wing = Wing::factory()->for(Shelter::factory())->create();

    $editor = User::factory()->create();
    $this->actingAs($editor);
    $wing->update(['name' => 'Updated wing name']);

    expect($wing->fresh()->updated_by)->toBe($editor->id)
        ->and($wing->fresh()->created_by)->toBe($creator->id);
});

test('stamps deleted_by when soft deleting a record', function () {
    $user = User::factory()->create();
    $this->actingAs($user);
    $wing = Wing::factory()->for(Shelter::factory())->create();

    $wing->delete();

    expect($wing->fresh()->deleted_by)->toBe($user->id);
});

test('does not error when deleting a model that has no deleted_by column', function () {
    $user = User::factory()->create();
    $this->actingAs($user);
    $petImage = PetImage::factory()->create();

    $petImage->delete();

    expect(PetImage::find($petImage->id))->toBeNull();
});
