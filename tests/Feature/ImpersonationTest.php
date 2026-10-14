<?php

use App\Livewire\ShelterSwitcher;
use App\Models\Pet;
use App\Models\Shelter;
use App\Models\User;
use Livewire\Livewire;
use Symfony\Component\HttpKernel\Exception\HttpException;

beforeEach(function () {
    $this->admin = User::factory()->admin()->create(['last_login' => null]);
    $this->shelter = Shelter::factory()->create();
    $this->manager = User::factory()->forShelter($this->shelter, 'manager')->create(['last_login' => null]);
});

test('an admin starts viewing as a manager without touching their last login', function () {
    $this->actingAs($this->admin)
        ->post(route('impersonation.start', $this->manager))
        ->assertRedirect(route('dashboard'));

    $this->assertAuthenticatedAs($this->manager);
    expect(session('impersonator_id'))->toBe($this->admin->id)
        ->and($this->manager->fresh()->last_login)->toBeNull();

    $this->get(route('dashboard'))->assertOk()->assertSee('impersonation-banner', false);
});

test('stopping works once the read-only guard of a viewing request is active', function () {
    $this->actingAs($this->admin)->post(route('impersonation.start', $this->manager));
    $this->get(route('dashboard'))->assertOk();

    $this->post(route('impersonation.stop'))->assertRedirect(route('admin.users.index'));

    expect($this->admin->fresh()->last_login)->not->toBeNull();
});

test('only admins can start, and never as another admin or themselves', function () {
    $this->actingAs($this->manager)->post(route('impersonation.start', $this->manager))->assertForbidden();

    $this->actingAs($this->admin);
    $this->post(route('impersonation.start', User::factory()->admin()->create()))->assertForbidden();
    $this->post(route('impersonation.start', $this->admin))->assertForbidden();
});

test('stopping returns to the admin', function () {
    $this->actingAs($this->admin)->post(route('impersonation.start', $this->manager));

    $this->post(route('impersonation.stop'))->assertRedirect(route('admin.users.index'));

    $this->assertAuthenticatedAs($this->admin);
    expect(session()->has('impersonator_id'))->toBeFalse();
});

test('a normal user cannot call stop', function () {
    $this->actingAs($this->manager)->post(route('impersonation.stop'))->assertForbidden();
});

test('form posts, credential pages and database writes are refused while viewing', function () {
    $this->actingAs($this->admin)->post(route('impersonation.start', $this->manager));

    $this->get(route('profile.edit'))->assertForbidden();
    $this->post(route('locale.update'), ['locale' => 'en'])->assertForbidden();
    $this->get(route('dashboard'))->assertOk();

    expect(fn () => Pet::factory()->for($this->shelter)->create())->toThrow(HttpException::class);
    expect(Pet::query()->withoutGlobalScopes()->count())->toBe(0);
});

test('the shelter switcher still works while viewing', function () {
    $second = Shelter::factory()->create();
    $this->manager->shelters()->attach($second, ['role' => 'manager']);
    $this->actingAs($this->admin)->post(route('impersonation.start', $this->manager));
    $this->get(route('dashboard'));

    Livewire::test(ShelterSwitcher::class)->call('switchTo', $second->id);

    expect($this->manager->fresh()->current_shelter_id)->toBe($second->id);
});

test('the users list offers the view-as button to admins only, for non-admin users', function () {
    $this->actingAs($this->admin)->get(route('admin.users.index'))
        ->assertSee('data-test="impersonate-'.$this->manager->id.'"', false)
        ->assertDontSee('data-test="impersonate-'.$this->admin->id.'"', false);

    $this->actingAs($this->manager)->get(route('admin.users.index'))
        ->assertDontSee('data-test="impersonate-', false);
});
