<?php

use App\Livewire\Admin\UserForm;
use App\Models\Shelter;
use App\Models\User;
use App\Notifications\ShelterMembershipAdded;
use App\Notifications\UserInvitation;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

test('guests are redirected to the login page', function () {
    $this->get(route('admin.users.create'))->assertRedirect(route('login'));
});

test('staff are forbidden from viewing the form', function () {
    $shelter = Shelter::factory()->create();
    $staff = User::factory()->forShelter($shelter, 'staff')->create();
    $this->actingAs($staff);

    $this->get(route('admin.users.create'))->assertForbidden();
});

test('admins and managers can view the invite and edit pages', function () {
    $shelter = Shelter::factory()->create();
    $staff = User::factory()->forShelter($shelter, 'staff')->create();

    $this->actingAs(User::factory()->admin()->create());
    $this->get(route('admin.users.create'))->assertOk();
    $this->get(route('admin.users.edit', $staff))->assertOk();

    $this->actingAs(User::factory()->forShelter($shelter, 'manager')->create());
    $this->get(route('admin.users.create'))->assertOk();
    $this->get(route('admin.users.edit', $staff))->assertOk();
});

test('admin can invite a new staff member to a shelter', function () {
    Notification::fake();

    $admin = User::factory()->admin()->create();
    $shelter = Shelter::factory()->create();
    $this->actingAs($admin);

    Livewire::test(UserForm::class)
        ->set('userName', 'Nova Funcionária')
        ->set('userEmail', 'nova@example.com')
        ->set('userMemberships.0.shelter_id', $shelter->id)
        ->set('userMemberships.0.role', 'staff')
        ->call('saveUser')
        ->assertHasNoErrors()
        ->assertRedirect(route('admin.users.index'));

    $invited = User::query()->where('email', 'nova@example.com')->first();

    expect($invited)->not->toBeNull()
        ->and($invited->is_admin)->toBeFalse()
        ->and($invited->current_shelter_id)->toBe($shelter->id)
        ->and($invited->roleForShelter($shelter->id))->toBe('staff');

    Notification::assertSentTo($invited, UserInvitation::class);
});

test('admin can invite a staff member with vaccination notifications enabled', function () {
    Notification::fake();

    $admin = User::factory()->admin()->create();
    $shelter = Shelter::factory()->create();
    $this->actingAs($admin);

    Livewire::test(UserForm::class)
        ->set('userName', 'Nova Funcionária')
        ->set('userEmail', 'nova-notif@example.com')
        ->set('userMemberships.0.shelter_id', $shelter->id)
        ->set('userMemberships.0.vaccination_notifications', true)
        ->call('saveUser')
        ->assertHasNoErrors();

    $invited = User::query()->where('email', 'nova-notif@example.com')->first();

    expect($invited)->not->toBeNull();
    expect($invited->shelters()->first()->pivot->vaccination_notifications)->toBeTrue();
});

test('admin can toggle vaccination notifications when editing a user', function () {
    $admin = User::factory()->admin()->create();
    $shelter = Shelter::factory()->create();
    $staff = User::factory()->forShelter($shelter, 'staff', false)->create();
    $this->actingAs($admin);

    Livewire::test(UserForm::class, ['user' => $staff])
        ->assertSet('userMemberships.0.vaccination_notifications', false)
        ->set('userMemberships.0.vaccination_notifications', true)
        ->call('saveUser')
        ->assertHasNoErrors();

    expect($staff->fresh()->shelters()->first()->pivot->vaccination_notifications)->toBeTrue();
});

test('admin can invite another admin without assigning any shelter', function () {
    Notification::fake();

    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    Livewire::test(UserForm::class)
        ->set('userName', 'Novo Admin')
        ->set('userEmail', 'novoadmin@example.com')
        ->set('userIsAdmin', true)
        ->call('saveUser')
        ->assertHasNoErrors();

    $invited = User::query()->where('email', 'novoadmin@example.com')->first();

    expect($invited)->not->toBeNull()
        ->and($invited->is_admin)->toBeTrue()
        ->and($invited->current_shelter_id)->toBeNull()
        ->and($invited->shelters)->toHaveCount(0);
});

test('shelter is required when inviting a non-admin user', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    Livewire::test(UserForm::class)
        ->set('userName', 'Sem Abrigo')
        ->set('userEmail', 'semabrigo@example.com')
        ->call('saveUser')
        ->assertHasErrors(['userMemberships.0.shelter_id']);
});

test('an invalid membership role cannot be assigned', function () {
    $admin = User::factory()->admin()->create();
    $shelter = Shelter::factory()->create();
    $this->actingAs($admin);

    Livewire::test(UserForm::class)
        ->set('userName', 'Papel Inválido')
        ->set('userEmail', 'papel@example.com')
        ->set('userMemberships.0.shelter_id', $shelter->id)
        ->set('userMemberships.0.role', 'owner')
        ->call('saveUser')
        ->assertHasErrors(['userMemberships.0.role']);
});

test('admin can edit an existing user\'s membership role and shelter', function () {
    $admin = User::factory()->admin()->create();
    $shelterA = Shelter::factory()->create();
    $shelterB = Shelter::factory()->create();
    $staff = User::factory()->forShelter($shelterA, 'staff')->create();
    $this->actingAs($admin);

    Livewire::test(UserForm::class, ['user' => $staff])
        ->set('userMemberships.0.shelter_id', $shelterB->id)
        ->set('userMemberships.0.role', 'manager')
        ->call('saveUser')
        ->assertHasNoErrors();

    expect($staff->fresh()->roleForShelter($shelterB->id))->toBe('manager')
        ->and($staff->fresh()->belongsToShelter($shelterA->id))->toBeFalse();
});

test('manager can invite a staff member to their own shelter', function () {
    Notification::fake();

    $shelter = Shelter::factory()->create();
    $manager = User::factory()->forShelter($shelter, 'manager')->create();
    $this->actingAs($manager);

    Livewire::test(UserForm::class)
        ->set('userName', 'Nova Funcionária')
        ->set('userEmail', 'nova-manager@example.com')
        ->call('saveUser')
        ->assertHasNoErrors();

    $invited = User::query()->where('email', 'nova-manager@example.com')->first();

    expect($invited)->not->toBeNull()
        ->and($invited->roleForShelter($shelter->id))->toBe('staff')
        ->and($invited->current_shelter_id)->toBe($shelter->id);

    Notification::assertSentTo($invited, UserInvitation::class);
});

test('manager can invite another manager to their own shelter', function () {
    Notification::fake();

    $shelter = Shelter::factory()->create();
    $manager = User::factory()->forShelter($shelter, 'manager')->create();
    $this->actingAs($manager);

    Livewire::test(UserForm::class)
        ->set('userName', 'Novo Gestor')
        ->set('userEmail', 'novo-gestor@example.com')
        ->set('userMemberships.0.role', 'manager')
        ->call('saveUser')
        ->assertHasNoErrors();

    $invited = User::query()->where('email', 'novo-gestor@example.com')->first();

    expect($invited)->not->toBeNull()
        ->and($invited->roleForShelter($shelter->id))->toBe('manager');
});

test('manager cannot invite an admin even when tampering with the admin flag', function () {
    Notification::fake();

    $shelter = Shelter::factory()->create();
    $manager = User::factory()->forShelter($shelter, 'manager')->create();
    $this->actingAs($manager);

    Livewire::test(UserForm::class)
        ->set('userName', 'Tentativa Admin')
        ->set('userEmail', 'tentativa-admin@example.com')
        ->set('userIsAdmin', true)
        ->call('saveUser')
        ->assertHasNoErrors();

    $invited = User::query()->where('email', 'tentativa-admin@example.com')->first();

    expect($invited->is_admin)->toBeFalse();
});

test('manager cannot assign an invited user to another shelter', function () {
    $shelter = Shelter::factory()->create();
    $otherShelter = Shelter::factory()->create();
    $manager = User::factory()->forShelter($shelter, 'manager')->create();
    $this->actingAs($manager);

    Livewire::test(UserForm::class)
        ->set('userName', 'Tentativa Outro Abrigo')
        ->set('userEmail', 'tentativa-outro@example.com')
        ->set('userMemberships.0.shelter_id', $otherShelter->id)
        ->call('saveUser')
        ->assertHasErrors(['userMemberships.0.shelter_id']);

    expect(User::query()->where('email', 'tentativa-outro@example.com')->exists())->toBeFalse();
});

test('manager can edit a user from their own shelter', function () {
    $shelter = Shelter::factory()->create();
    $manager = User::factory()->forShelter($shelter, 'manager')->create();
    $staff = User::factory()->forShelter($shelter, 'staff')->create();
    $this->actingAs($manager);

    Livewire::test(UserForm::class, ['user' => $staff])
        ->set('userName', 'Nome Atualizado')
        ->set('userMemberships.0.role', 'manager')
        ->call('saveUser')
        ->assertHasNoErrors();

    expect($staff->fresh()->name)->toBe('Nome Atualizado')
        ->and($staff->fresh()->roleForShelter($shelter->id))->toBe('manager');
});

test('manager cannot edit a user from another shelter', function () {
    $shelter = Shelter::factory()->create();
    $otherShelter = Shelter::factory()->create();
    $manager = User::factory()->forShelter($shelter, 'manager')->create();
    $otherStaff = User::factory()->forShelter($otherShelter, 'staff')->create();
    $this->actingAs($manager);

    $this->get(route('admin.users.edit', $otherStaff))->assertNotFound();
});

test('admin can add an existing user to a second shelter without re-inviting them', function () {
    Notification::fake();

    $shelterA = Shelter::factory()->create();
    $shelterB = Shelter::factory()->create();
    $existing = User::factory()->forShelter($shelterA, 'staff')->create();
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    Livewire::test(UserForm::class)
        ->set('userEmail', $existing->email)
        ->assertSet('existingUserId', $existing->id)
        ->set('userMemberships.0.shelter_id', $shelterB->id)
        ->set('userMemberships.0.role', 'staff')
        ->call('saveUser')
        ->assertHasNoErrors();

    expect($existing->fresh()->belongsToShelter($shelterA->id))->toBeTrue()
        ->and($existing->fresh()->belongsToShelter($shelterB->id))->toBeTrue();

    Notification::assertSentTo($existing, ShelterMembershipAdded::class);
    Notification::assertNotSentTo($existing, UserInvitation::class);
});

test('adding an existing user to a shelter they already belong to fails validation', function () {
    $shelter = Shelter::factory()->create();
    $existing = User::factory()->forShelter($shelter, 'staff')->create();
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    Livewire::test(UserForm::class)
        ->set('userEmail', $existing->email)
        ->set('userMemberships.0.shelter_id', $shelter->id)
        ->call('saveUser')
        ->assertHasErrors(['userMemberships']);

    expect($existing->fresh()->shelters)->toHaveCount(1);
});

test('editing a user never exposes a membership outside the manager\'s authority', function () {
    $ownShelter = Shelter::factory()->create();
    $otherShelter = Shelter::factory()->create();
    $manager = User::factory()->forShelter($ownShelter, 'manager')->create();
    $multiShelterStaff = User::factory()->forShelter($ownShelter, 'staff')->create();
    $multiShelterStaff->shelters()->attach($otherShelter, ['role' => 'staff', 'vaccination_notifications' => false]);
    $this->actingAs($manager);

    $component = Livewire::test(UserForm::class, ['user' => $multiShelterStaff]);

    expect($component->get('userMemberships'))->toHaveCount(1)
        ->and($component->get('userMemberships')[0]['shelter_id'])->toBe($ownShelter->id);

    $component->set('userMemberships.0.role', 'manager')->call('saveUser')->assertHasNoErrors();

    expect($multiShelterStaff->fresh()->belongsToShelter($otherShelter->id))->toBeTrue()
        ->and($multiShelterStaff->fresh()->roleForShelter($otherShelter->id))->toBe('staff');
});

test('the same shelter cannot be assigned twice to a user', function () {
    $admin = User::factory()->admin()->create();
    $shelter = Shelter::factory()->create();
    $staff = User::factory()->forShelter($shelter, 'staff')->create();
    $this->actingAs($admin);

    Livewire::test(UserForm::class, ['user' => $staff])
        ->call('addMembership')
        ->set('userMemberships.1.shelter_id', $shelter->id)
        ->call('saveUser')
        ->assertHasErrors(['userMemberships.1.shelter_id' => 'distinct']);

    expect($staff->fresh()->shelters)->toHaveCount(1);
});

test('manager of several shelters can add a user to every shelter they manage', function () {
    Notification::fake();

    $currentShelter = Shelter::factory()->create();
    $otherManagedShelter = Shelter::factory()->create();
    $manager = User::factory()->forShelter($currentShelter, 'manager')->create();
    $manager->shelters()->attach($otherManagedShelter, ['role' => 'manager', 'vaccination_notifications' => false]);
    $this->actingAs($manager);

    Livewire::test(UserForm::class)
        ->assertSet('userMemberships.0.shelter_id', $currentShelter->id)
        ->set('userName', 'Duas Filiações')
        ->set('userEmail', 'duas-filiacoes@example.com')
        ->call('addMembership')
        ->set('userMemberships.1.shelter_id', $otherManagedShelter->id)
        ->set('userMemberships.1.role', 'manager')
        ->call('saveUser')
        ->assertHasNoErrors();

    $invited = User::query()->where('email', 'duas-filiacoes@example.com')->first();

    expect($invited->roleForShelter($currentShelter->id))->toBe('staff')
        ->and($invited->roleForShelter($otherManagedShelter->id))->toBe('manager');
});

test('manager editing a user sees and changes memberships in every shelter they manage', function () {
    $currentShelter = Shelter::factory()->create();
    $otherManagedShelter = Shelter::factory()->create();
    $unmanagedShelter = Shelter::factory()->create();
    $manager = User::factory()->forShelter($currentShelter, 'manager')->create();
    $manager->shelters()->attach($otherManagedShelter, ['role' => 'manager', 'vaccination_notifications' => false]);
    $staff = User::factory()->forShelter($currentShelter, 'staff')->create();
    $staff->shelters()->attach($otherManagedShelter, ['role' => 'staff', 'vaccination_notifications' => false]);
    $staff->shelters()->attach($unmanagedShelter, ['role' => 'staff', 'vaccination_notifications' => false]);
    $this->actingAs($manager);

    $component = Livewire::test(UserForm::class, ['user' => $staff]);

    expect(collect($component->get('userMemberships'))->pluck('shelter_id')->sort()->values()->all())
        ->toBe(collect([$currentShelter->id, $otherManagedShelter->id])->sort()->values()->all());

    $index = collect($component->get('userMemberships'))->search(fn ($membership) => $membership['shelter_id'] === $otherManagedShelter->id);

    $component->set("userMemberships.{$index}.role", 'manager')->call('saveUser')->assertHasNoErrors();

    expect($staff->fresh()->roleForShelter($otherManagedShelter->id))->toBe('manager')
        ->and($staff->fresh()->roleForShelter($currentShelter->id))->toBe('staff')
        ->and($staff->fresh()->roleForShelter($unmanagedShelter->id))->toBe('staff');
});

test('manager can edit a user who only belongs to another shelter they manage', function () {
    $currentShelter = Shelter::factory()->create();
    $otherManagedShelter = Shelter::factory()->create();
    $manager = User::factory()->forShelter($currentShelter, 'manager')->create();
    $manager->shelters()->attach($otherManagedShelter, ['role' => 'manager', 'vaccination_notifications' => false]);
    $otherStaff = User::factory()->forShelter($otherManagedShelter, 'staff')->create();
    $this->actingAs($manager);

    $this->get(route('admin.users.edit', $otherStaff))->assertOk();
});

test('manager cannot change their own name, shelters or roles', function () {
    $shelter = Shelter::factory()->create();
    $otherManagedShelter = Shelter::factory()->create();
    $manager = User::factory()->forShelter($shelter, 'manager')->create(['name' => 'Nome Original']);
    $manager->shelters()->attach($otherManagedShelter, ['role' => 'manager', 'vaccination_notifications' => false]);
    $this->actingAs($manager);

    $component = Livewire::test(UserForm::class, ['user' => $manager])
        ->assertSet('isManagerEditingOwnAccount', true)
        ->call('addMembership')
        ->call('removeMembership', 0);

    expect($component->get('userMemberships'))->toHaveCount(2);

    $component
        ->set('userName', 'Nome Alterado')
        ->set('userMemberships.0.role', 'staff')
        ->set('userMemberships.1.role', 'staff')
        ->call('saveUser')
        ->assertHasNoErrors();

    $manager = $manager->fresh();

    expect($manager->name)->toBe('Nome Original')
        ->and($manager->roleForShelter($shelter->id))->toBe('manager')
        ->and($manager->roleForShelter($otherManagedShelter->id))->toBe('manager');
});

test('manager can still toggle their own vaccination notifications', function () {
    $shelter = Shelter::factory()->create();
    $manager = User::factory()->forShelter($shelter, 'manager')->create();
    $this->actingAs($manager);

    Livewire::test(UserForm::class, ['user' => $manager])
        ->set('userMemberships.0.vaccination_notifications', true)
        ->call('saveUser')
        ->assertHasNoErrors();

    expect((bool) $manager->shelters()->first()->pivot->vaccination_notifications)->toBeTrue();
});

test('admin editing a manager can still change their name and shelters', function () {
    $shelter = Shelter::factory()->create();
    $manager = User::factory()->forShelter($shelter, 'manager')->create();
    $this->actingAs(User::factory()->admin()->create());

    Livewire::test(UserForm::class, ['user' => $manager])
        ->assertSet('isManagerEditingOwnAccount', false)
        ->set('userName', 'Nome Pelo Admin')
        ->set('userMemberships.0.role', 'staff')
        ->call('saveUser')
        ->assertHasNoErrors();

    expect($manager->fresh()->name)->toBe('Nome Pelo Admin')
        ->and($manager->fresh()->roleForShelter($shelter->id))->toBe('staff');
});
