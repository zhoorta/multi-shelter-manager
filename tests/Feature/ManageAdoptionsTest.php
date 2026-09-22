<?php

use App\Livewire\Pets\ManageAdoptions;
use App\Models\Adoption;
use App\Models\Pet;
use App\Models\Shelter;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Livewire;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('pets.adoptions.index'));

    $response->assertRedirect(route('login'));
});

test('admins are forbidden from viewing the page', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $this->get(route('pets.adoptions.index'))->assertForbidden();
});

test('managers and staff can view the page', function () {
    $shelter = Shelter::factory()->create();

    $manager = User::factory()->forShelter($shelter, 'manager')->create();
    $this->actingAs($manager);
    $this->get(route('pets.adoptions.index'))->assertOk();

    $staff = User::factory()->forShelter($shelter, 'staff')->create();
    $this->actingAs($staff);
    $this->get(route('pets.adoptions.index'))->assertOk();
});

test('shows a placeholder message when there are no adoptions', function () {
    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    $this->get(route('pets.adoptions.index'))->assertSee(__('No adoptions registered'));
});

test('lists the owner contacts and the adopted pet', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create(['name' => 'Rex', 'ref' => 'PET00001']);
    Adoption::factory()->for($pet)->create([
        'name' => 'Maria Silva',
        'email' => 'maria@example.com',
        'phone' => '912345678',
    ]);

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    Livewire::test(ManageAdoptions::class)
        ->assertSee('Maria Silva')
        ->assertSee('maria@example.com')
        ->assertSee('912345678')
        ->assertSee($pet->species->name)
        ->assertSee('PET00001')
        ->assertSee('Rex');
});

test('lists only adoptions whose pet belongs to the acting user\'s shelter', function () {
    $shelter = Shelter::factory()->create();
    $otherShelter = Shelter::factory()->create();

    $pet = Pet::factory()->for($shelter)->create(['name' => 'Rex']);
    Adoption::factory()->for($pet)->create(['name' => 'Maria Silva']);

    $otherPet = Pet::factory()->for($otherShelter)->create(['name' => 'Other Shelter Dog']);
    Adoption::factory()->for($otherPet)->create(['name' => 'Other Shelter Adopter']);

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    Livewire::test(ManageAdoptions::class)
        ->assertSee('Maria Silva')
        ->assertDontSee('Other Shelter Adopter');
});

test('shows the adoption date in bold and hides the return date when the pet was not returned', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();
    Adoption::factory()->for($pet)->create(['name' => 'Maria Silva', 'adoption_date' => '2026-01-10']);

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    Livewire::test(ManageAdoptions::class)
        ->assertSeeHtml('<strong>10/01/2026</strong>')
        ->assertSee(str_replace(':date', '', __('Adopted at :date')))
        ->assertDontSee(str_replace(':date', '', __('Returned at :date')));
});

test('shows the return date in bold on its own line when the pet was returned', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();
    Adoption::factory()->for($pet)->create([
        'name' => 'Maria Silva',
        'adoption_date' => '2026-01-10',
        'return_date' => '2026-02-15',
    ]);

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    Livewire::test(ManageAdoptions::class)
        ->assertSeeHtml('<strong>10/01/2026</strong>')
        ->assertSeeHtml('<strong>15/02/2026</strong>')
        ->assertSee(str_replace(':date', '', __('Returned at :date')));
});

test('shows the adoption notes on a new line', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();
    Adoption::factory()->for($pet)->create(['name' => 'Maria Silva', 'notes' => 'Prefers weekend visits']);

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    Livewire::test(ManageAdoptions::class)
        ->assertSee('Prefers weekend visits');
});

test('links the view action to the adoption show route', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();
    $adoption = Adoption::factory()->for($pet)->create();

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    Livewire::test(ManageAdoptions::class)
        ->assertSeeHtml(route('pets.adopt.show', [$pet, $adoption]));
});

test('filters the adoptions by owner name', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();
    Adoption::factory()->for($pet)->create(['name' => 'Maria Silva']);
    Adoption::factory()->for($pet)->create(['name' => 'Joao Costa']);

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    Livewire::test(ManageAdoptions::class)
        ->set('search', 'Maria')
        ->assertSee('Maria Silva')
        ->assertDontSee('Joao Costa');
});

test('filters the adoptions by pet ref', function () {
    $shelter = Shelter::factory()->create();
    $rex = Pet::factory()->for($shelter)->create(['name' => 'Rex', 'ref' => 'PET00001']);
    $bella = Pet::factory()->for($shelter)->create(['name' => 'Bella', 'ref' => 'PET00002']);
    Adoption::factory()->for($rex)->create(['name' => 'Maria Silva']);
    Adoption::factory()->for($bella)->create(['name' => 'Joao Costa']);

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    Livewire::test(ManageAdoptions::class)
        ->set('search', 'PET00001')
        ->assertSee('Maria Silva')
        ->assertDontSee('Joao Costa');
});

test('filters the adoptions by owner email', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();
    Adoption::factory()->for($pet)->create(['name' => 'Maria Silva', 'email' => 'maria@example.com']);
    Adoption::factory()->for($pet)->create(['name' => 'Joao Costa', 'email' => 'joao@example.com']);

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    Livewire::test(ManageAdoptions::class)
        ->set('search', 'maria@example.com')
        ->assertSee('Maria Silva')
        ->assertDontSee('Joao Costa');
});

test('filters the adoptions by owner phone', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();
    Adoption::factory()->for($pet)->create(['name' => 'Maria Silva', 'phone' => '911111111']);
    Adoption::factory()->for($pet)->create(['name' => 'Joao Costa', 'phone' => '922222222']);

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    Livewire::test(ManageAdoptions::class)
        ->set('search', '911111111')
        ->assertSee('Maria Silva')
        ->assertDontSee('Joao Costa');
});

test('filters the adoptions by notes', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();
    Adoption::factory()->for($pet)->create(['name' => 'Maria Silva', 'notes' => 'Prefers weekend visits']);
    Adoption::factory()->for($pet)->create(['name' => 'Joao Costa', 'notes' => 'Has a big backyard']);

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    Livewire::test(ManageAdoptions::class)
        ->set('search', 'weekend visits')
        ->assertSee('Maria Silva')
        ->assertDontSee('Joao Costa');
});

test('filters the adoptions by pet name', function () {
    $shelter = Shelter::factory()->create();
    $rex = Pet::factory()->for($shelter)->create(['name' => 'Rex']);
    $bella = Pet::factory()->for($shelter)->create(['name' => 'Bella']);
    Adoption::factory()->for($rex)->create(['name' => 'Maria Silva']);
    Adoption::factory()->for($bella)->create(['name' => 'Joao Costa']);

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    Livewire::test(ManageAdoptions::class)
        ->set('search', 'Rex')
        ->assertSee('Maria Silva')
        ->assertDontSee('Joao Costa');
});

test('resets the page when the search term changes', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();
    Adoption::factory()->for($pet)->count(25)->create();
    Adoption::factory()->for($pet)->create(['name' => 'Maria Silva']);

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    // Maria Silva is the only match and, being the latest record, would only
    // be visible on page 1 — so this only passes if updatingSearch() resets
    // the page back from 2.
    Livewire::test(ManageAdoptions::class)
        ->call('gotoPage', 2)
        ->set('search', 'Maria')
        ->assertSee('Maria Silva');
});

test('soft-deletes an adoption instead of removing it permanently', function () {
    $shelter = Shelter::factory()->create();
    $user = User::factory()->forShelter($shelter, 'staff')->create();
    $this->actingAs($user);

    $pet = Pet::factory()->for($shelter)->create();
    $adoption = Adoption::factory()->for($pet)->create(['name' => 'Maria Silva']);

    Livewire::test(ManageAdoptions::class)
        ->call('deleteAdoption', $adoption->id)
        ->assertDontSee('Maria Silva');

    expect($adoption->fresh()->trashed())->toBeTrue();
    expect($adoption->fresh()->deleted_by)->toBe($user->id);
});

test('deleting an open adoption clears the pet checkout date and marks it available', function () {
    $shelter = Shelter::factory()->create();
    $user = User::factory()->forShelter($shelter, 'staff')->create();
    $this->actingAs($user);

    $pet = Pet::factory()->for($shelter)->create([
        'status' => 'adopted',
        'checkout_date' => '2026-01-10',
    ]);
    $adoption = Adoption::factory()->for($pet)->create([
        'adoption_date' => '2026-01-10',
        'return_date' => null,
    ]);

    Livewire::test(ManageAdoptions::class)->call('deleteAdoption', $adoption->id);

    expect($adoption->fresh()->trashed())->toBeTrue();
    $pet->refresh();
    expect($pet->status)->toBe('available');
    expect($pet->checkout_date)->toBeNull();
});

test('deleting an open adoption marks a non-adoptable pet not_available instead of available', function () {
    $shelter = Shelter::factory()->create();
    $user = User::factory()->forShelter($shelter, 'staff')->create();
    $this->actingAs($user);

    $pet = Pet::factory()->for($shelter)->create([
        'status' => 'adopted',
        'checkout_date' => '2026-01-10',
        'is_adoptable' => false,
    ]);
    $adoption = Adoption::factory()->for($pet)->create([
        'adoption_date' => '2026-01-10',
        'return_date' => null,
    ]);

    Livewire::test(ManageAdoptions::class)->call('deleteAdoption', $adoption->id);

    $pet->refresh();
    expect($pet->status)->toBe('not_available');
    expect($pet->checkout_date)->toBeNull();
});

test('deleting a closed adoption does not change the pet status or checkout date', function () {
    $shelter = Shelter::factory()->create();
    $user = User::factory()->forShelter($shelter, 'staff')->create();
    $this->actingAs($user);

    // The pet's current state reflects a separate, newer open adoption —
    // deleting the closed one below must leave it untouched.
    $pet = Pet::factory()->for($shelter)->create([
        'status' => 'adopted',
        'checkout_date' => '2026-03-01',
    ]);
    $adoption = Adoption::factory()->for($pet)->create([
        'adoption_date' => '2026-01-10',
        'return_date' => '2026-02-15',
    ]);

    Livewire::test(ManageAdoptions::class)->call('deleteAdoption', $adoption->id);

    expect($adoption->fresh()->trashed())->toBeTrue();
    $pet->refresh();
    expect($pet->status)->toBe('adopted');
    expect($pet->checkout_date->toDateString())->toBe('2026-03-01');
});

test('cannot delete an adoption belonging to another shelter\'s pet', function () {
    $otherShelter = Shelter::factory()->create();
    $otherPet = Pet::factory()->for($otherShelter)->create();
    $adoption = Adoption::factory()->for($otherPet)->create();

    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    expect(fn () => Livewire::test(ManageAdoptions::class)->call('deleteAdoption', $adoption->id))
        ->toThrow(ModelNotFoundException::class);

    expect($adoption->fresh()->trashed())->toBeFalse();
});
