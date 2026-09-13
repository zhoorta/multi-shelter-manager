<?php

use App\Livewire\Admin\ManageActivities;
use App\Models\Activity;
use App\Models\User;
use Livewire\Livewire;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('admin.activities.index'));

    $response->assertRedirect(route('login'));
});

test('staff and managers are forbidden from viewing the page', function () {
    $staff = User::factory()->create(['role' => 'staff']);
    $this->actingAs($staff);
    $this->get(route('admin.activities.index'))->assertForbidden();

    $manager = User::factory()->create(['role' => 'manager']);
    $this->actingAs($manager);
    $this->get(route('admin.activities.index'))->assertForbidden();
});

test('admins can view the page', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    $this->get(route('admin.activities.index'))->assertOk();
});

test('shows a placeholder message when there are no activities', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    $this->get(route('admin.activities.index'))->assertSee(__('No activities registered'));
});

test('creates a new activity and closes the modal', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    Livewire::test(ManageActivities::class)
        ->set('activityName', 'Dog Walking')
        ->call('saveActivity')
        ->assertHasNoErrors()
        ->assertDispatched('modal-close', name: 'activity-form');

    expect(Activity::query()->where('name', 'Dog Walking')->exists())->toBeTrue();
});

test('opening the create modal resets a stale edit state', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    $activity = Activity::factory()->create(['name' => 'Feeding']);

    Livewire::test(ManageActivities::class)
        ->call('editActivity', $activity->id)
        ->assertSet('activityName', 'Feeding')
        ->call('createActivity')
        ->assertSet('editingActivityId', null)
        ->assertSet('activityName', '');
});

test('requires a name to create an activity', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    Livewire::test(ManageActivities::class)
        ->set('activityName', '')
        ->call('saveActivity')
        ->assertHasErrors(['activityName' => 'required']);
});

test('rejects a duplicate activity name', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    Activity::factory()->create(['name' => 'Feeding']);

    Livewire::test(ManageActivities::class)
        ->set('activityName', 'Feeding')
        ->call('saveActivity')
        ->assertHasErrors(['activityName' => 'unique']);
});

test('updates an existing activity', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    $activity = Activity::factory()->create(['name' => 'Feeding']);

    Livewire::test(ManageActivities::class)
        ->call('editActivity', $activity->id)
        ->assertSet('activityName', 'Feeding')
        ->set('activityName', 'Feeding & Grooming')
        ->call('saveActivity')
        ->assertHasNoErrors();

    expect($activity->fresh()->name)->toBe('Feeding & Grooming');
});

test('soft-deletes an activity instead of removing it permanently', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    $activity = Activity::factory()->create(['name' => 'Feeding']);

    Livewire::test(ManageActivities::class)
        ->call('deleteActivity', $activity->id)
        ->assertDontSee('Feeding');

    expect($activity->fresh()->trashed())->toBeTrue();
    expect(Activity::query()->find($activity->id))->toBeNull();
});
