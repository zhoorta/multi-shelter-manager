<?php

use App\Livewire\Pets\ManageSponsorships;
use App\Models\Pet;
use App\Models\Shelter;
use App\Models\Sponsorship;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Livewire;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('pets.sponsorships.index'));

    $response->assertRedirect(route('login'));
});

test('admins are forbidden from viewing the page', function () {
    $admin = User::factory()->create(['role' => 'admin', 'shelter_id' => null]);
    $this->actingAs($admin);

    $this->get(route('pets.sponsorships.index'))->assertForbidden();
});

test('managers and staff can view the page', function () {
    $shelter = Shelter::factory()->create();

    $manager = User::factory()->create(['role' => 'manager', 'shelter_id' => $shelter->id]);
    $this->actingAs($manager);
    $this->get(route('pets.sponsorships.index'))->assertOk();

    $staff = User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]);
    $this->actingAs($staff);
    $this->get(route('pets.sponsorships.index'))->assertOk();
});

test('shows a placeholder message when there are no sponsorships', function () {
    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    $this->get(route('pets.sponsorships.index'))->assertSee(__('No sponsorships registered'));
});

test('lists the sponsor contacts and the sponsored pet', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create(['name' => 'Rex', 'ref' => 'PET00001']);
    Sponsorship::factory()->for($pet)->create([
        'name' => 'Maria Silva',
        'email' => 'maria@example.com',
        'phone' => '912345678',
    ]);

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    Livewire::test(ManageSponsorships::class)
        ->assertSee('Maria Silva')
        ->assertSee('maria@example.com')
        ->assertSee('912345678')
        ->assertSee($pet->species->name)
        ->assertSee('PET00001')
        ->assertSee('Rex');
});

test('lists only sponsorships whose pet belongs to the acting user\'s shelter', function () {
    $shelter = Shelter::factory()->create();
    $otherShelter = Shelter::factory()->create();

    $pet = Pet::factory()->for($shelter)->create(['name' => 'Rex']);
    Sponsorship::factory()->for($pet)->create(['name' => 'Maria Silva']);

    $otherPet = Pet::factory()->for($otherShelter)->create(['name' => 'Other Shelter Dog']);
    Sponsorship::factory()->for($otherPet)->create(['name' => 'Other Shelter Sponsor']);

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    Livewire::test(ManageSponsorships::class)
        ->assertSee('Maria Silva')
        ->assertDontSee('Other Shelter Sponsor');
});

test('links the view action to the sponsorship show route', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();
    $sponsorship = Sponsorship::factory()->for($pet)->create();

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    Livewire::test(ManageSponsorships::class)
        ->assertSeeHtml(route('pets.sponsor.show', [$pet, $sponsorship]));
});

test('soft-deletes a sponsorship instead of removing it permanently', function () {
    $shelter = Shelter::factory()->create();
    $user = User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]);
    $this->actingAs($user);

    $pet = Pet::factory()->for($shelter)->create();
    $sponsorship = Sponsorship::factory()->for($pet)->create(['name' => 'Maria Silva']);

    Livewire::test(ManageSponsorships::class)
        ->call('deleteSponsorship', $sponsorship->id)
        ->assertDontSee('Maria Silva');

    expect($sponsorship->fresh()->trashed())->toBeTrue();
    expect($sponsorship->fresh()->deleted_by)->toBe($user->id);
});

test('cannot delete a sponsorship belonging to another shelter\'s pet', function () {
    $otherShelter = Shelter::factory()->create();
    $otherPet = Pet::factory()->for($otherShelter)->create();
    $sponsorship = Sponsorship::factory()->for($otherPet)->create();

    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    expect(fn () => Livewire::test(ManageSponsorships::class)->call('deleteSponsorship', $sponsorship->id))
        ->toThrow(ModelNotFoundException::class);

    expect($sponsorship->fresh()->trashed())->toBeFalse();
});
