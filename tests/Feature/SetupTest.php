<?php

use App\Livewire\Setup;
use App\Models\User;
use Livewire\Livewire;

test('login redirects to the setup wizard when no users exist', function () {
    $this->get(route('login'))->assertRedirect(route('setup'));
});

test('setup wizard can be rendered when no users exist', function () {
    $this->get(route('setup'))->assertOk()->assertSeeLivewire(Setup::class);
});

test('setup wizard redirects to login once a user exists', function () {
    User::factory()->create();

    $this->get(route('setup'))->assertRedirect(route('login'));
});

test('setup wizard creates a verified admin and logs them in', function () {
    Livewire::test(Setup::class)
        ->set('name', 'Admin')
        ->set('email', 'admin@example.com')
        ->set('password', 'password')
        ->set('password_confirmation', 'password')
        ->call('createAdmin')
        ->assertHasNoErrors()
        ->assertRedirect(route('dashboard'));

    $admin = User::query()->sole();

    expect($admin->name)->toBe('Admin')
        ->and($admin->email)->toBe('admin@example.com')
        ->and($admin->is_admin)->toBeTrue()
        ->and($admin->email_verified_at)->not->toBeNull();

    $this->assertAuthenticatedAs($admin);
});

test('setup wizard validates the admin details', function () {
    Livewire::test(Setup::class)
        ->set('name', '')
        ->set('email', 'not-an-email')
        ->set('password', 'password')
        ->set('password_confirmation', 'different')
        ->call('createAdmin')
        ->assertHasErrors(['name' => 'required', 'email' => 'email', 'password' => 'confirmed']);

    expect(User::query()->exists())->toBeFalse();
});

test('setup wizard refuses to create an admin once a user exists', function () {
    $component = Livewire::test(Setup::class);

    User::factory()->create();

    $component
        ->set('name', 'Intruder')
        ->set('email', 'intruder@example.com')
        ->set('password', 'password')
        ->set('password_confirmation', 'password')
        ->call('createAdmin')
        ->assertForbidden();

    expect(User::query()->where('email', 'intruder@example.com')->exists())->toBeFalse();
});
