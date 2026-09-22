<?php

use App\Livewire\Admin\ManageUsers;
use App\Models\Shelter;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Livewire;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('admin.users.index'));

    $response->assertRedirect(route('login'));
});

test('staff are forbidden from viewing the page', function () {
    $shelter = Shelter::factory()->create();
    $staff = User::factory()->forShelter($shelter, 'staff')->create();
    $this->actingAs($staff);
    $this->get(route('admin.users.index'))->assertForbidden();
});

test('admins can view the page', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $this->get(route('admin.users.index'))->assertOk();
});

test('managers can view the page', function () {
    $shelter = Shelter::factory()->create();
    $manager = User::factory()->forShelter($shelter, 'manager')->create();
    $this->actingAs($manager);

    $this->get(route('admin.users.index'))->assertOk();
});

test('lists users across every shelter', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $shelterA = Shelter::factory()->create();
    $shelterB = Shelter::factory()->create();
    $staffA = User::factory()->forShelter($shelterA, 'staff')->create();
    $staffB = User::factory()->forShelter($shelterB, 'staff')->create();

    $response = $this->get(route('admin.users.index'));

    $response->assertSee($staffA->name)->assertSee($staffB->name);
});

test('filters users by shelter', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $shelterA = Shelter::factory()->create();
    $shelterB = Shelter::factory()->create();
    $staffA = User::factory()->forShelter($shelterA, 'staff')->create();
    $staffB = User::factory()->forShelter($shelterB, 'staff')->create();

    Livewire::test(ManageUsers::class)
        ->set('filterShelterId', (string) $shelterA->id)
        ->assertSee($staffA->name)
        ->assertDontSee($staffB->name);
});

test('paginates users 20 per page', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    User::factory()->count(24)->sequence(fn ($sequence) => ['name' => 'User '.$sequence->index])->create();

    $component = Livewire::test(ManageUsers::class);

    expect($component->get('users')->count())->toBe(20);
    expect($component->get('users')->total())->toBe(25);

    $component->call('nextPage');

    expect($component->get('users')->count())->toBe(5);
});

test('displays each user\'s last login', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    User::factory()->create(['last_login' => now()->setDate(2026, 1, 15)->setTime(10, 30)]);

    $response = $this->get(route('admin.users.index'));

    $response->assertSee('15/01/2026 10:30');
});

test('lists a shelter name in the notifications column for a user with vaccination notifications enabled there', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $shelter = Shelter::factory()->create();
    $staff = User::factory()->forShelter($shelter, 'staff', true)->create();

    Livewire::test(ManageUsers::class)
        ->assertSeeInOrder([$staff->name, $shelter->name]);
});

test('leaves the notifications column blank for a user without vaccination notifications enabled', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $shelter = Shelter::factory()->create();
    $staff = User::factory()->forShelter($shelter, 'staff', false)->create();

    Livewire::test(ManageUsers::class)
        ->assertSeeInOrder([$staff->name, '—']);
});

test('admin can delete another user', function () {
    $admin = User::factory()->admin()->create();
    $staff = User::factory()->create();
    $this->actingAs($admin);

    Livewire::test(ManageUsers::class)
        ->call('deleteUser', $staff->id);

    expect(User::query()->find($staff->id))->toBeNull();
});

test('admin cannot delete their own account', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    Livewire::test(ManageUsers::class)
        ->call('deleteUser', $admin->id);

    expect(User::query()->find($admin->id))->not->toBeNull();
});

test('manager only sees users from their own shelter', function () {
    $shelter = Shelter::factory()->create();
    $manager = User::factory()->forShelter($shelter, 'manager')->create();
    $this->actingAs($manager);

    $ownStaff = User::factory()->forShelter($shelter, 'staff')->create();
    $otherShelter = Shelter::factory()->create();
    $otherStaff = User::factory()->forShelter($otherShelter, 'staff')->create();

    $response = $this->get(route('admin.users.index'));

    $response->assertSee($ownStaff->name)->assertDontSee($otherStaff->name);
});

test('manager cannot delete a user from another shelter', function () {
    $shelter = Shelter::factory()->create();
    $otherShelter = Shelter::factory()->create();
    $manager = User::factory()->forShelter($shelter, 'manager')->create();
    $otherStaff = User::factory()->forShelter($otherShelter, 'staff')->create();
    $this->actingAs($manager);

    expect(fn () => Livewire::test(ManageUsers::class)->call('deleteUser', $otherStaff->id))
        ->toThrow(ModelNotFoundException::class);

    expect($otherStaff->fresh()->trashed())->toBeFalse();
});

test('manager can delete a user from their own shelter', function () {
    $shelter = Shelter::factory()->create();
    $manager = User::factory()->forShelter($shelter, 'manager')->create();
    $staff = User::factory()->forShelter($shelter, 'staff')->create();
    $this->actingAs($manager);

    Livewire::test(ManageUsers::class)
        ->call('deleteUser', $staff->id);

    expect(User::query()->find($staff->id))->toBeNull();
});

test('manager deleting a user who belongs to other shelters only removes them from the current shelter', function () {
    $shelter = Shelter::factory()->create();
    $otherShelter = Shelter::factory()->create();
    $manager = User::factory()->forShelter($shelter, 'manager')->create();
    $staff = User::factory()->forShelter($shelter, 'staff')->create();
    $staff->shelters()->attach($otherShelter, ['role' => 'staff', 'vaccination_notifications' => false]);
    $this->actingAs($manager);

    Livewire::test(ManageUsers::class)
        ->call('deleteUser', $staff->id)
        ->assertDontSee($staff->email);

    $staff = $staff->fresh();

    expect($staff->trashed())->toBeFalse()
        ->and($staff->belongsToShelter($shelter->id))->toBeFalse()
        ->and($staff->belongsToShelter($otherShelter->id))->toBeTrue()
        ->and($staff->current_shelter_id)->toBe($otherShelter->id);
});

test('admin deleting a user with several shelters deletes the whole account', function () {
    $shelter = Shelter::factory()->create();
    $otherShelter = Shelter::factory()->create();
    $staff = User::factory()->forShelter($shelter, 'staff')->create();
    $staff->shelters()->attach($otherShelter, ['role' => 'staff', 'vaccination_notifications' => false]);
    $this->actingAs(User::factory()->admin()->create());

    Livewire::test(ManageUsers::class)->call('deleteUser', $staff->id);

    expect($staff->fresh()->trashed())->toBeTrue();
});

test('manager sees every shelter a listed user belongs to among the shelters they manage', function () {
    $currentShelter = Shelter::factory()->create(['name' => 'Abrigo Atual']);
    $otherManagedShelter = Shelter::factory()->create(['name' => 'Abrigo Gerido']);
    $unmanagedShelter = Shelter::factory()->create(['name' => 'Abrigo Alheio']);
    $manager = User::factory()->forShelter($currentShelter, 'manager')->create();
    $manager->shelters()->attach($otherManagedShelter, ['role' => 'manager', 'vaccination_notifications' => false]);
    $staff = User::factory()->forShelter($currentShelter, 'staff')->create();
    $staff->shelters()->attach($otherManagedShelter, ['role' => 'staff', 'vaccination_notifications' => false]);
    $staff->shelters()->attach($unmanagedShelter, ['role' => 'staff', 'vaccination_notifications' => false]);
    $this->actingAs($manager);

    Livewire::test(ManageUsers::class)
        ->assertSee('Abrigo Atual')
        ->assertSee('Abrigo Gerido')
        ->assertDontSee('Abrigo Alheio');
});

test('manager who manages two shelters only sees the roster of their current shelter', function () {
    $shelterA = Shelter::factory()->create();
    $shelterB = Shelter::factory()->create();
    $manager = User::factory()->forShelter($shelterA, 'manager')->create();
    $manager->shelters()->attach($shelterB, ['role' => 'manager', 'vaccination_notifications' => false]);
    $this->actingAs($manager);

    $staffA = User::factory()->forShelter($shelterA, 'staff')->create();
    $staffB = User::factory()->forShelter($shelterB, 'staff')->create();

    Livewire::test(ManageUsers::class)
        ->assertSee($staffA->name)
        ->assertDontSee($staffB->name);

    expect(fn () => Livewire::test(ManageUsers::class)->call('deleteUser', $staffB->id))
        ->toThrow(ModelNotFoundException::class);
});

test('a manager of another shelter is forbidden while acting in a shelter where they are staff', function () {
    $managedShelter = Shelter::factory()->create();
    $currentShelter = Shelter::factory()->create();
    $user = User::factory()->forShelter($currentShelter, 'staff')->create();
    $user->shelters()->attach($managedShelter, ['role' => 'manager', 'vaccination_notifications' => false]);
    $this->actingAs($user);

    $this->get(route('admin.users.index'))->assertForbidden();
});
