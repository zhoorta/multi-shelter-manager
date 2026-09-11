<?php

use App\Livewire\Pets\SponsorshipForm;
use App\Models\Pet;
use App\Models\Shelter;
use App\Models\Sponsorship;
use App\Models\User;
use Livewire\Livewire;

test('guests are redirected to the login page', function () {
    $pet = Pet::factory()->create(['is_sponsorable' => true]);

    $this->get(route('pets.sponsor', $pet))->assertRedirect(route('login'));
});

test('admins are forbidden from viewing the form', function () {
    $admin = User::factory()->create(['role' => 'admin', 'shelter_id' => null]);
    $this->actingAs($admin);

    $pet = Pet::factory()->create(['is_sponsorable' => true]);

    $this->get(route('pets.sponsor', $pet))->assertForbidden();
});

test('managers and staff can view the sponsorship form for a sponsorable pet in their shelter', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create(['is_sponsorable' => true]);

    $manager = User::factory()->create(['role' => 'manager', 'shelter_id' => $shelter->id]);
    $this->actingAs($manager);
    $this->get(route('pets.sponsor', $pet))->assertOk();

    $staff = User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]);
    $this->actingAs($staff);
    $this->get(route('pets.sponsor', $pet))->assertOk();
});

test('returns 404 when the pet belongs to another shelter', function () {
    $otherShelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($otherShelter)->create(['is_sponsorable' => true]);

    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    $this->get(route('pets.sponsor', $pet))->assertNotFound();
});

test('is forbidden when the pet is not sponsorable', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create(['is_sponsorable' => false]);

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    $this->get(route('pets.sponsor', $pet))->assertForbidden();
});

test('creates a sponsorship record for the pet', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create(['is_sponsorable' => true]);

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    $component = Livewire::test(SponsorshipForm::class, ['pet' => $pet])
        ->set('sponsorName', 'Maria Silva')
        ->set('sponsorEmail', 'maria@example.com')
        ->set('sponsorPhone', '912345678')
        ->set('sendFeedback', true)
        ->set('sendNewsletter', true)
        ->call('saveSponsorship')
        ->assertHasNoErrors();

    $sponsorship = $pet->sponsorships()->first();
    expect($sponsorship)->not->toBeNull();
    expect($sponsorship->name)->toBe('Maria Silva');
    expect($sponsorship->email)->toBe('maria@example.com');
    expect($sponsorship->send_feedback)->toBeTrue();
    expect($sponsorship->send_newsletter)->toBeTrue();

    $component->assertRedirect(route('pets.show', $pet));
});

test('requires a sponsor name', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create(['is_sponsorable' => true]);

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    Livewire::test(SponsorshipForm::class, ['pet' => $pet])
        ->set('sponsorName', '')
        ->call('saveSponsorship')
        ->assertHasErrors(['sponsorName' => 'required']);
});

test('loads the existing sponsorship data when editing', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create(['is_sponsorable' => true]);
    $sponsorship = Sponsorship::factory()->for($pet)->create(['name' => 'Ana Costa']);

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    Livewire::test(SponsorshipForm::class, ['pet' => $pet, 'sponsorship' => $sponsorship])
        ->assertSet('sponsorName', 'Ana Costa')
        ->assertSet('sponsorEmail', $sponsorship->email);
});

test('updates an existing sponsorship record', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create(['is_sponsorable' => true]);
    $sponsorship = Sponsorship::factory()->for($pet)->create(['name' => 'Ana Costa']);

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    $component = Livewire::test(SponsorshipForm::class, ['pet' => $pet, 'sponsorship' => $sponsorship])
        ->set('sponsorName', 'Ana Costa Silva')
        ->call('saveSponsorship')
        ->assertHasNoErrors();

    expect($pet->sponsorships()->count())->toBe(1);
    expect($sponsorship->refresh()->name)->toBe('Ana Costa Silva');

    $component->assertRedirect(route('pets.show', $pet));
});

test('returns 404 when the sponsorship does not belong to the given pet', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create(['is_sponsorable' => true]);
    $otherPet = Pet::factory()->for($shelter)->create(['is_sponsorable' => true]);
    $sponsorship = Sponsorship::factory()->for($otherPet)->create();

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    $this->get(route('pets.sponsor.edit', [$pet, $sponsorship]))->assertNotFound();
});
