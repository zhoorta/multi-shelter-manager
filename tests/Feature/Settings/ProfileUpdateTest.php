<?php

use App\Livewire\Settings\Profile;
use App\Models\User;
use Livewire\Livewire;

test('profile page is displayed', function () {
    $this->actingAs($user = User::factory()->create());

    $this->get('/settings/profile')->assertOk();
});

test('profile page displays the user name and email as read-only', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    Livewire::test(Profile::class)
        ->assertSee($user->name)
        ->assertSee($user->email);

    expect(method_exists(Profile::class, 'updateProfileInformation'))->toBeFalse();
});

test('profile page does not offer account deletion', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $this->get('/settings/profile')->assertOk()->assertDontSee(__('Delete account'));
});
