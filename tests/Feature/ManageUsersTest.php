<?php

use App\Livewire\Admin\ManageUsers;
use App\Models\Shelter;
use App\Models\User;
use App\Notifications\UserInvitation;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('admin.users.index'));

    $response->assertRedirect(route('login'));
});

test('staff are forbidden from viewing the page', function () {
    $staff = User::factory()->create(['role' => 'staff']);
    $this->actingAs($staff);
    $this->get(route('admin.users.index'))->assertForbidden();
});

test('admins can view the page', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    $this->get(route('admin.users.index'))->assertOk();
});

test('managers can view the page', function () {
    $manager = User::factory()->create(['role' => 'manager', 'shelter_id' => Shelter::factory()]);
    $this->actingAs($manager);

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

test('filters users by shelter', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    $shelterA = Shelter::factory()->create();
    $shelterB = Shelter::factory()->create();
    $staffA = User::factory()->create(['role' => 'staff', 'shelter_id' => $shelterA->id]);
    $staffB = User::factory()->create(['role' => 'staff', 'shelter_id' => $shelterB->id]);

    Livewire::test(ManageUsers::class)
        ->set('filterShelterId', (string) $shelterA->id)
        ->assertSee($staffA->name)
        ->assertDontSee($staffB->name);
});

test('displays each user\'s last login', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    User::factory()->create(['last_login' => now()->setDate(2026, 1, 15)->setTime(10, 30)]);

    $response = $this->get(route('admin.users.index'));

    $response->assertSee('15/01/2026 10:30');
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

test('manager only sees users from their own shelter', function () {
    $shelter = Shelter::factory()->create();
    $manager = User::factory()->create(['role' => 'manager', 'shelter_id' => $shelter->id]);
    $this->actingAs($manager);

    $ownStaff = User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]);
    $otherShelter = Shelter::factory()->create();
    $otherStaff = User::factory()->create(['role' => 'staff', 'shelter_id' => $otherShelter->id]);

    $response = $this->get(route('admin.users.index'));

    $response->assertSee($ownStaff->name)->assertDontSee($otherStaff->name);
});

test('manager can invite a staff member to their own shelter', function () {
    Notification::fake();

    $shelter = Shelter::factory()->create();
    $manager = User::factory()->create(['role' => 'manager', 'shelter_id' => $shelter->id]);
    $this->actingAs($manager);

    Livewire::test(ManageUsers::class)
        ->set('userName', 'Nova Funcionária')
        ->set('userEmail', 'nova-manager@example.com')
        ->set('userRole', 'staff')
        ->call('saveUser')
        ->assertHasNoErrors();

    $invited = User::query()->where('email', 'nova-manager@example.com')->first();

    expect($invited)->not->toBeNull()
        ->and($invited->role)->toBe('staff')
        ->and($invited->shelter_id)->toBe($shelter->id);

    Notification::assertSentTo($invited, UserInvitation::class);
});

test('manager can invite another manager to their own shelter', function () {
    Notification::fake();

    $shelter = Shelter::factory()->create();
    $manager = User::factory()->create(['role' => 'manager', 'shelter_id' => $shelter->id]);
    $this->actingAs($manager);

    Livewire::test(ManageUsers::class)
        ->set('userName', 'Novo Gestor')
        ->set('userEmail', 'novo-gestor@example.com')
        ->set('userRole', 'manager')
        ->call('saveUser')
        ->assertHasNoErrors();

    $invited = User::query()->where('email', 'novo-gestor@example.com')->first();

    expect($invited)->not->toBeNull()
        ->and($invited->role)->toBe('manager')
        ->and($invited->shelter_id)->toBe($shelter->id);
});

test('manager cannot invite an admin', function () {
    $shelter = Shelter::factory()->create();
    $manager = User::factory()->create(['role' => 'manager', 'shelter_id' => $shelter->id]);
    $this->actingAs($manager);

    Livewire::test(ManageUsers::class)
        ->set('userName', 'Tentativa Admin')
        ->set('userEmail', 'tentativa-admin@example.com')
        ->set('userRole', 'admin')
        ->call('saveUser')
        ->assertHasErrors(['userRole']);
});

test('manager cannot assign an invited user to another shelter', function () {
    Notification::fake();

    $shelter = Shelter::factory()->create();
    $otherShelter = Shelter::factory()->create();
    $manager = User::factory()->create(['role' => 'manager', 'shelter_id' => $shelter->id]);
    $this->actingAs($manager);

    Livewire::test(ManageUsers::class)
        ->set('userName', 'Tentativa Outro Abrigo')
        ->set('userEmail', 'tentativa-outro@example.com')
        ->set('userRole', 'staff')
        ->set('userShelterId', $otherShelter->id)
        ->call('saveUser')
        ->assertHasNoErrors();

    $invited = User::query()->where('email', 'tentativa-outro@example.com')->first();

    expect($invited->shelter_id)->toBe($shelter->id);
});

test('manager can edit a user from their own shelter', function () {
    $shelter = Shelter::factory()->create();
    $manager = User::factory()->create(['role' => 'manager', 'shelter_id' => $shelter->id]);
    $staff = User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]);
    $this->actingAs($manager);

    Livewire::test(ManageUsers::class)
        ->call('editUser', $staff->id)
        ->set('userName', 'Nome Atualizado')
        ->set('userRole', 'manager')
        ->call('saveUser')
        ->assertHasNoErrors();

    expect($staff->fresh()->name)->toBe('Nome Atualizado')
        ->and($staff->fresh()->role)->toBe('manager')
        ->and($staff->fresh()->shelter_id)->toBe($shelter->id);
});

test('manager cannot edit a user from another shelter', function () {
    $shelter = Shelter::factory()->create();
    $otherShelter = Shelter::factory()->create();
    $manager = User::factory()->create(['role' => 'manager', 'shelter_id' => $shelter->id]);
    $otherStaff = User::factory()->create(['role' => 'staff', 'shelter_id' => $otherShelter->id]);
    $this->actingAs($manager);

    expect(fn () => Livewire::test(ManageUsers::class)->call('editUser', $otherStaff->id))
        ->toThrow(ModelNotFoundException::class);
});

test('manager cannot delete a user from another shelter', function () {
    $shelter = Shelter::factory()->create();
    $otherShelter = Shelter::factory()->create();
    $manager = User::factory()->create(['role' => 'manager', 'shelter_id' => $shelter->id]);
    $otherStaff = User::factory()->create(['role' => 'staff', 'shelter_id' => $otherShelter->id]);
    $this->actingAs($manager);

    expect(fn () => Livewire::test(ManageUsers::class)->call('deleteUser', $otherStaff->id))
        ->toThrow(ModelNotFoundException::class);

    expect($otherStaff->fresh()->trashed())->toBeFalse();
});

test('manager can delete a user from their own shelter', function () {
    $shelter = Shelter::factory()->create();
    $manager = User::factory()->create(['role' => 'manager', 'shelter_id' => $shelter->id]);
    $staff = User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]);
    $this->actingAs($manager);

    Livewire::test(ManageUsers::class)
        ->call('deleteUser', $staff->id);

    expect(User::query()->find($staff->id))->toBeNull();
});
