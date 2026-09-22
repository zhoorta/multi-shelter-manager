<?php

use App\Livewire\Pets\SponsorshipShow;
use App\Models\Pet;
use App\Models\Shelter;
use App\Models\Sponsorship;
use App\Models\SponsorshipPayment;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Livewire;

test('guests are redirected to the login page', function () {
    $pet = Pet::factory()->create(['is_sponsorable' => true]);
    $sponsorship = Sponsorship::factory()->for($pet)->create();

    $this->get(route('pets.sponsor.show', [$pet, $sponsorship]))->assertRedirect(route('login'));
});

test('admins are forbidden from viewing the page', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $pet = Pet::factory()->create(['is_sponsorable' => true]);
    $sponsorship = Sponsorship::factory()->for($pet)->create();

    $this->get(route('pets.sponsor.show', [$pet, $sponsorship]))->assertForbidden();
});

test('managers and staff can view a sponsorship for a pet in their shelter', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create(['is_sponsorable' => true]);
    $sponsorship = Sponsorship::factory()->for($pet)->create(['name' => 'Maria Silva']);

    $manager = User::factory()->forShelter($shelter, 'manager')->create();
    $this->actingAs($manager);
    $this->get(route('pets.sponsor.show', [$pet, $sponsorship]))->assertOk()->assertSee('Maria Silva');

    $staff = User::factory()->forShelter($shelter, 'staff')->create();
    $this->actingAs($staff);
    $this->get(route('pets.sponsor.show', [$pet, $sponsorship]))->assertOk()->assertSee('Maria Silva');
});

test('returns 404 when the sponsorship does not belong to the given pet', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create(['is_sponsorable' => true]);
    $otherPet = Pet::factory()->for($shelter)->create(['is_sponsorable' => true]);
    $sponsorship = Sponsorship::factory()->for($otherPet)->create();

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    $this->get(route('pets.sponsor.show', [$pet, $sponsorship]))->assertNotFound();
});

test('returns 404 when the pet belongs to another shelter', function () {
    $otherShelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($otherShelter)->create(['is_sponsorable' => true]);
    $sponsorship = Sponsorship::factory()->for($pet)->create();

    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    $this->get(route('pets.sponsor.show', [$pet, $sponsorship]))->assertNotFound();
});

test('shows the sponsor contacts and details', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create(['is_sponsorable' => true]);
    $sponsorship = Sponsorship::factory()->for($pet)->create([
        'name' => 'Maria Silva',
        'email' => 'maria@example.com',
        'phone' => '912345678',
    ]);

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    $this->get(route('pets.sponsor.show', [$pet, $sponsorship]))
        ->assertOk()
        ->assertSeeInOrder(['Maria Silva', 'maria@example.com', '912345678']);
});

test('creates a sponsorship payment', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create(['is_sponsorable' => true]);
    $sponsorship = Sponsorship::factory()->for($pet)->create();

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    Livewire::test(SponsorshipShow::class, ['pet' => $pet, 'sponsorship' => $sponsorship])
        ->call('createPayment', $sponsorship->id)
        ->set('paymentStartDate', '2026-02-01')
        ->set('paymentEndDate', '2026-02-28')
        ->set('paymentDate', '2026-02-01')
        ->set('paymentValue', '30.00')
        ->set('paymentNotes', 'February contribution')
        ->call('savePayment')
        ->assertHasNoErrors();

    $payment = $sponsorship->payments()->first();
    expect($payment)->not->toBeNull();
    expect($payment->payment_value)->toBe('30.00');
});

test('cannot edit a payment belonging to a different sponsorship', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create(['is_sponsorable' => true]);
    $sponsorship = Sponsorship::factory()->for($pet)->create();

    $otherSponsorship = Sponsorship::factory()->for($pet)->create();
    $otherPayment = SponsorshipPayment::factory()->for($otherSponsorship)->create();

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    expect(fn () => Livewire::test(SponsorshipShow::class, ['pet' => $pet, 'sponsorship' => $sponsorship])
        ->call('editPayment', $otherPayment->id))
        ->toThrow(ModelNotFoundException::class);
});
