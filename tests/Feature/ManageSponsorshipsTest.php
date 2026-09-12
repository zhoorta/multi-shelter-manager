<?php

use App\Livewire\Pets\ManageSponsorships;
use App\Models\Pet;
use App\Models\Shelter;
use App\Models\Sponsorship;
use App\Models\SponsorshipPayment;
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

test('shows the no payments message when a sponsorship has no payments', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();
    Sponsorship::factory()->for($pet)->create(['name' => 'Maria Silva']);

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    Livewire::test(ManageSponsorships::class)
        ->assertSee(__('(No payments done)'));
});

test('shows the valid until date in bold when the latest payment end date is in the future', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();
    $sponsorship = Sponsorship::factory()->for($pet)->create(['name' => 'Maria Silva']);
    SponsorshipPayment::factory()->for($sponsorship)->create(['end_date' => today()->addMonth()]);

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    Livewire::test(ManageSponsorships::class)
        ->assertSeeHtml('<strong>'.today()->addMonth()->format('d/m/Y').'</strong>')
        ->assertSee(str_replace(':date', '', __('Valid until :date')));
});

test('shows the expired at date when the latest payment end date is in the past', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();
    $sponsorship = Sponsorship::factory()->for($pet)->create(['name' => 'Maria Silva']);
    SponsorshipPayment::factory()->for($sponsorship)->create(['end_date' => today()->subMonth()]);

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    Livewire::test(ManageSponsorships::class)
        ->assertSeeHtml('<strong>'.today()->subMonth()->format('d/m/Y').'</strong>')
        ->assertSee(str_replace(':date', '', __('Expired at :date')));
});

test('uses the largest payment end date to determine validity', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();
    $sponsorship = Sponsorship::factory()->for($pet)->create(['name' => 'Maria Silva']);
    SponsorshipPayment::factory()->for($sponsorship)->create(['end_date' => today()->addMonth()]);
    SponsorshipPayment::factory()->for($sponsorship)->create(['end_date' => today()->addYear()]);

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    Livewire::test(ManageSponsorships::class)
        ->assertSeeHtml('<strong>'.today()->addYear()->format('d/m/Y').'</strong>');
});

test('links the view action to the sponsorship show route', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();
    $sponsorship = Sponsorship::factory()->for($pet)->create();

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    Livewire::test(ManageSponsorships::class)
        ->assertSeeHtml(route('pets.sponsor.show', [$pet, $sponsorship]));
});

test('links the sponsor name to the sponsorship show route', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();
    $sponsorship = Sponsorship::factory()->for($pet)->create(['name' => 'Maria Silva']);

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    Livewire::test(ManageSponsorships::class)
        ->assertSeeHtml('href="'.route('pets.sponsor.show', [$pet, $sponsorship]).'"')
        ->assertSeeHtml('Maria Silva');
});

test('shows the sponsorship notes on a new line', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();
    Sponsorship::factory()->for($pet)->create(['name' => 'Maria Silva', 'notes' => 'Prefers email contact']);

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    Livewire::test(ManageSponsorships::class)
        ->assertSee('Prefers email contact');
});

test('filters the sponsorships by sponsor name', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();
    Sponsorship::factory()->for($pet)->create(['name' => 'Maria Silva']);
    Sponsorship::factory()->for($pet)->create(['name' => 'Joao Costa']);

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    Livewire::test(ManageSponsorships::class)
        ->set('search', 'Maria')
        ->assertSee('Maria Silva')
        ->assertDontSee('Joao Costa');
});

test('filters the sponsorships by phone', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();
    Sponsorship::factory()->for($pet)->create(['name' => 'Maria Silva', 'phone' => '912345678']);
    Sponsorship::factory()->for($pet)->create(['name' => 'Joao Costa', 'phone' => '911111111']);

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    Livewire::test(ManageSponsorships::class)
        ->set('search', '912345')
        ->assertSee('Maria Silva')
        ->assertDontSee('Joao Costa');
});

test('filters the sponsorships by email', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();
    Sponsorship::factory()->for($pet)->create(['name' => 'Maria Silva', 'email' => 'maria@example.com']);
    Sponsorship::factory()->for($pet)->create(['name' => 'Joao Costa', 'email' => 'joao@example.com']);

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    Livewire::test(ManageSponsorships::class)
        ->set('search', 'maria@example.com')
        ->assertSee('Maria Silva')
        ->assertDontSee('Joao Costa');
});

test('filters the sponsorships by pet name', function () {
    $shelter = Shelter::factory()->create();
    $rex = Pet::factory()->for($shelter)->create(['name' => 'Rex']);
    $bella = Pet::factory()->for($shelter)->create(['name' => 'Bella']);
    Sponsorship::factory()->for($rex)->create(['name' => 'Maria Silva']);
    Sponsorship::factory()->for($bella)->create(['name' => 'Joao Costa']);

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    Livewire::test(ManageSponsorships::class)
        ->set('search', 'Rex')
        ->assertSee('Maria Silva')
        ->assertDontSee('Joao Costa');
});

test('filters the sponsorships by pet ref', function () {
    $shelter = Shelter::factory()->create();
    $rex = Pet::factory()->for($shelter)->create(['name' => 'Rex', 'ref' => 'PET00001']);
    $bella = Pet::factory()->for($shelter)->create(['name' => 'Bella', 'ref' => 'PET00002']);
    Sponsorship::factory()->for($rex)->create(['name' => 'Maria Silva']);
    Sponsorship::factory()->for($bella)->create(['name' => 'Joao Costa']);

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    Livewire::test(ManageSponsorships::class)
        ->set('search', 'PET00001')
        ->assertSee('Maria Silva')
        ->assertDontSee('Joao Costa');
});

test('filters the sponsorships by notes', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();
    Sponsorship::factory()->for($pet)->create(['name' => 'Maria Silva', 'notes' => 'Prefers email contact']);
    Sponsorship::factory()->for($pet)->create(['name' => 'Joao Costa', 'notes' => 'Calls every month']);

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    Livewire::test(ManageSponsorships::class)
        ->set('search', 'email contact')
        ->assertSee('Maria Silva')
        ->assertDontSee('Joao Costa');
});

test('resets the page when the search term changes', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();
    Sponsorship::factory()->for($pet)->count(25)->create();
    Sponsorship::factory()->for($pet)->create(['name' => 'Maria Silva']);

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    // Maria Silva is the only match and, being the latest record, would only
    // be visible on page 1 — so this only passes if updatingSearch() resets
    // the page back from 2.
    Livewire::test(ManageSponsorships::class)
        ->call('gotoPage', 2)
        ->set('search', 'Maria')
        ->assertSee('Maria Silva');
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
