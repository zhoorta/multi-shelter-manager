<?php

use App\Livewire\Admin\ManageShelters;
use App\Models\Pet;
use App\Models\Shelter;
use App\Models\User;
use Livewire\Livewire;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('admin.shelters.index'));

    $response->assertRedirect(route('login'));
});

test('staff and managers are forbidden from viewing the page', function () {
    $staff = User::factory()->create(['role' => 'staff']);
    $this->actingAs($staff);
    $this->get(route('admin.shelters.index'))->assertForbidden();

    $manager = User::factory()->create(['role' => 'manager']);
    $this->actingAs($manager);
    $this->get(route('admin.shelters.index'))->assertForbidden();
});

test('admins can view the page', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    $this->get(route('admin.shelters.index'))->assertOk();
});

test('shows a placeholder message when there are no shelters', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    $this->get(route('admin.shelters.index'))->assertSee(__('No shelters registered'));
});

test('lists shelters with their user and pet counts', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    $shelter = Shelter::factory()->create(['name' => 'Happy Paws', 'short_name' => 'HP', 'city' => 'Lisbon']);
    User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]);
    Pet::factory()->create(['shelter_id' => $shelter->id]);

    Livewire::test(ManageShelters::class)
        ->assertSee('Happy Paws')
        ->assertSee('HP')
        ->assertSee('Lisbon');
});

test('does not show a short name when the shelter has none', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    Shelter::factory()->create(['name' => 'Happy Paws', 'short_name' => null, 'city' => 'Lisbon']);

    Livewire::test(ManageShelters::class)
        ->assertDontSeeHtml('text-xs font-normal text-neutral-500 dark:text-neutral-400');
});

test('links to the separate create and edit pages instead of a modal', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    $shelter = Shelter::factory()->create(['name' => 'Happy Paws']);

    $this->get(route('admin.shelters.index'))
        ->assertOk()
        ->assertSee(route('admin.shelters.create'), false)
        ->assertSee(route('admin.shelters.edit', $shelter), false);
});

test('soft-deletes a shelter instead of removing it permanently', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    $shelter = Shelter::factory()->create(['name' => 'Happy Paws']);

    Livewire::test(ManageShelters::class)
        ->call('deleteShelter', $shelter->id)
        ->assertDontSee('Happy Paws');

    expect($shelter->fresh()->trashed())->toBeTrue();
    expect(Shelter::query()->find($shelter->id))->toBeNull();
});
