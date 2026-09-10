<?php

use App\Livewire\Admin\ManageUsers;
use App\Models\Shelter;
use App\Models\User;
use App\Notifications\UserInvitation;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('admin.users.index'));

    $response->assertRedirect(route('login'));
});

test('staff and managers are forbidden from viewing the page', function () {
    $staff = User::factory()->create(['role' => 'staff']);
    $this->actingAs($staff);
    $this->get(route('admin.users.index'))->assertForbidden();

    $manager = User::factory()->create(['role' => 'manager']);
    $this->actingAs($manager);
    $this->get(route('admin.users.index'))->assertForbidden();
});

test('admins can view the page', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    $this->get(route('admin.users.index'))->assertOk();
});

test('lists users across every shelter', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    $shelterA = Shelter::factory()->create();
    $shelterB = Shelter::factory()->create();
    $staffA = User::factory()->create(['role' => 'staff', 'shelter_id' => $shelterA->id]);
    $staffB = User::factory()->create(['role' => 'staff', 'shelter_id' => $shelterB->id]);

    $response = $this->get(route('admin.users.index'));

    $response->assertSee($staffA->name)->assertSee($staffB->name);
});

test('admin can invite a staff member to a shelter', function () {
    Notification::fake();

    $admin = User::factory()->create(['role' => 'admin']);
    $shelter = Shelter::factory()->create();
    $this->actingAs($admin);

    Livewire::test(ManageUsers::class)
        ->set('userName', 'Nova Funcionária')
        ->set('userEmail', 'nova@example.com')
        ->set('userRole', 'staff')
        ->set('userShelterId', $shelter->id)
        ->call('saveUser')
        ->assertHasNoErrors();

    $invited = User::query()->where('email', 'nova@example.com')->first();

    expect($invited)->not->toBeNull()
        ->and($invited->role)->toBe('staff')
        ->and($invited->shelter_id)->toBe($shelter->id);

    Notification::assertSentTo($invited, UserInvitation::class);
});

test('admin can invite another admin without assigning a shelter', function () {
    Notification::fake();

    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    Livewire::test(ManageUsers::class)
        ->set('userName', 'Novo Admin')
        ->set('userEmail', 'novoadmin@example.com')
        ->set('userRole', 'admin')
        ->call('saveUser')
        ->assertHasNoErrors();

    $invited = User::query()->where('email', 'novoadmin@example.com')->first();

    expect($invited)->not->toBeNull()
        ->and($invited->role)->toBe('admin')
        ->and($invited->shelter_id)->toBeNull();
});

test('shelter is required when inviting a non-admin user', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    Livewire::test(ManageUsers::class)
        ->set('userName', 'Sem Abrigo')
        ->set('userEmail', 'semabrigo@example.com')
        ->set('userRole', 'manager')
        ->call('saveUser')
        ->assertHasErrors(['userShelterId']);
});

test('the user role cannot be assigned', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $shelter = Shelter::factory()->create();
    $this->actingAs($admin);

    Livewire::test(ManageUsers::class)
        ->set('userName', 'Papel Inválido')
        ->set('userEmail', 'papel@example.com')
        ->set('userRole', 'user')
        ->set('userShelterId', $shelter->id)
        ->call('saveUser')
        ->assertHasErrors(['userRole']);
});

test('admin can edit an existing user role and shelter', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $shelterA = Shelter::factory()->create();
    $shelterB = Shelter::factory()->create();
    $staff = User::factory()->create(['role' => 'staff', 'shelter_id' => $shelterA->id]);
    $this->actingAs($admin);

    Livewire::test(ManageUsers::class)
        ->call('editUser', $staff->id)
        ->set('userRole', 'manager')
        ->set('userShelterId', $shelterB->id)
        ->call('saveUser')
        ->assertHasNoErrors();

    expect($staff->fresh()->role)->toBe('manager')
        ->and($staff->fresh()->shelter_id)->toBe($shelterB->id);
});

test('admin can delete another user', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $staff = User::factory()->create(['role' => 'staff']);
    $this->actingAs($admin);

    Livewire::test(ManageUsers::class)
        ->call('deleteUser', $staff->id);

    expect(User::query()->find($staff->id))->toBeNull();
});

test('admin cannot delete their own account', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    Livewire::test(ManageUsers::class)
        ->call('deleteUser', $admin->id);

    expect(User::query()->find($admin->id))->not->toBeNull();
});
