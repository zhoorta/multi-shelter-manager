<?php

use App\Livewire\Pets\PetShow;
use App\Models\Adoption;
use App\Models\Breed;
use App\Models\Cage;
use App\Models\Facility;
use App\Models\Pet;
use App\Models\Shelter;
use App\Models\Sickness;
use App\Models\Size;
use App\Models\Species;
use App\Models\Sponsorship;
use App\Models\SponsorshipPayment;
use App\Models\User;
use App\Models\Wing;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Livewire;

test('guests are redirected to the login page', function () {
    $pet = Pet::factory()->create();

    $this->get(route('pets.show', $pet))->assertRedirect(route('login'));
});

test('admins are forbidden from viewing the page', function () {
    $admin = User::factory()->create(['role' => 'admin', 'shelter_id' => null]);
    $this->actingAs($admin);

    $pet = Pet::factory()->create();

    $this->get(route('pets.show', $pet))->assertForbidden();
});

test('managers and staff can view a pet belonging to their shelter', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create(['name' => 'Rex']);

    $manager = User::factory()->create(['role' => 'manager', 'shelter_id' => $shelter->id]);
    $this->actingAs($manager);
    $this->get(route('pets.show', $pet))->assertOk()->assertSee('Rex');

    $staff = User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]);
    $this->actingAs($staff);
    $this->get(route('pets.show', $pet))->assertOk()->assertSee('Rex');
});

test('returns 404 when viewing a pet belonging to another shelter', function () {
    $otherShelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($otherShelter)->create();

    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    $this->get(route('pets.show', $pet))->assertNotFound();
});

test('links to the edit page', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    $this->get(route('pets.show', $pet))->assertSee(route('pets.edit', $pet), false);
});

test('links to the adoption registration page unless the pet is already adopted', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create(['status' => 'available']);

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    $this->get(route('pets.show', $pet))->assertSee(route('pets.adopt', $pet), false);

    $pet->update(['status' => 'adopted']);

    $this->get(route('pets.show', $pet))->assertDontSee(route('pets.adopt', $pet), false);
});

test('links to the sponsorship registration page only when the pet is sponsorable', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create(['is_sponsorable' => true]);

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    $this->get(route('pets.show', $pet))->assertSee(route('pets.sponsor', $pet), false);

    $pet->update(['is_sponsorable' => false]);

    $this->get(route('pets.show', $pet))->assertDontSee(route('pets.sponsor', $pet), false);
});

test('links to the adoption registration page only when the pet is not adopted', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create(['status' => 'available']);

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    $this->get(route('pets.show', $pet))->assertSee(route('pets.adopt', $pet), false);

    $pet->update(['status' => 'adopted']);

    $this->get(route('pets.show', $pet))->assertDontSee(route('pets.adopt', $pet), false);
});

test('links back to the pets list scoped to the pet species', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    $this->get(route('pets.show', $pet))
        ->assertSee(route('pets.index', ['speciesFilter' => $pet->species_id]), false)
        ->assertSee($pet->species->name_plural);
});

test('does not display placeholder text when color and fur type are not assigned', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create([
        'primary_color_id' => null,
        'secondary_color_id' => null,
        'fur_type_id' => null,
        'size_id' => null,
    ]);

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    $this->get(route('pets.show', $pet))
        ->assertOk()
        ->assertDontSee('No Color Assigned')
        ->assertDontSee('No Fur Type Assigned');
});

test('shows the pet\'s size when assigned', function () {
    $shelter = Shelter::factory()->create();
    $species = Species::factory()->create();
    $size = Size::factory()->for($species)->create(['name' => 'Grande']);
    $pet = Pet::factory()->for($shelter)->for($species)->create(['size_id' => $size->id]);

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    $this->get(route('pets.show', $pet))
        ->assertOk()
        ->assertSee(__('Size'))
        ->assertSee('Grande');
});

test('shows a placeholder when the species has sizes but the pet has none assigned', function () {
    $shelter = Shelter::factory()->create();
    $species = Species::factory()->create();
    Size::factory()->for($species)->create(['name' => 'Grande']);
    $pet = Pet::factory()->for($shelter)->for($species)->create(['size_id' => null]);

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    $this->get(route('pets.show', $pet))
        ->assertOk()
        ->assertSee(__('Size'));
});

test('hides the size field entirely when the species has no sizes registered', function () {
    $shelter = Shelter::factory()->create();
    $species = Species::factory()->create();
    $pet = Pet::factory()->for($shelter)->for($species)->create(['size_id' => null]);

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    $this->get(route('pets.show', $pet))
        ->assertOk()
        ->assertDontSee(__('Size'));
});

test('shows "(Pure)" next to the breed when the species has pure breeds enabled and the pet is a pure breed', function () {
    $shelter = Shelter::factory()->create();
    $species = Species::factory()->create(['has_pure_breed_field' => true]);
    $breed = Breed::factory()->for($species)->create(['name' => 'Labrador']);
    $pet = Pet::factory()->for($shelter)->for($species)->for($breed)->create(['is_pure_breed' => true]);

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    $this->get(route('pets.show', $pet))
        ->assertOk()
        ->assertSeeText('Labrador (Pure)');
});

test('does not show "(Pure)" when the pet is a pure breed but the species has pure breeds disabled', function () {
    $shelter = Shelter::factory()->create();
    $species = Species::factory()->create(['has_pure_breed_field' => false]);
    $breed = Breed::factory()->for($species)->create(['name' => 'Labrador']);
    $pet = Pet::factory()->for($shelter)->for($species)->for($breed)->create(['is_pure_breed' => true]);

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    $this->get(route('pets.show', $pet))
        ->assertOk()
        ->assertDontSee(__('Pure'));
});

test('does not show "(Pure)" when the species has pure breeds enabled but the pet is not a pure breed', function () {
    $shelter = Shelter::factory()->create();
    $species = Species::factory()->create(['has_pure_breed_field' => true]);
    $breed = Breed::factory()->for($species)->create(['name' => 'Labrador']);
    $pet = Pet::factory()->for($shelter)->for($species)->for($breed)->create(['is_pure_breed' => false]);

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    $this->get(route('pets.show', $pet))
        ->assertOk()
        ->assertDontSee(__('Pure'));
});

test('shows the birth date and death date', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create([
        'birth_date' => '2018-05-01',
        'date_of_death' => '2024-03-15',
    ]);

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    $this->get(route('pets.show', $pet))
        ->assertOk()
        ->assertSeeInOrder(['Birth Date', '01/05/2018', 'Death Date', '15/03/2024']);
});

test('shows the checkin date', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create(['checkin_date' => '2023-01-10']);

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    $this->get(route('pets.show', $pet))
        ->assertOk()
        ->assertSeeInOrder(['Checkin Date', '10/01/2023']);
});

test('shows the checkout date after the checkin date', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create([
        'checkin_date' => '2023-01-10',
        'checkout_date' => '2023-06-20',
    ]);

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    $this->get(route('pets.show', $pet))
        ->assertOk()
        ->assertSeeInOrder(['Checkin Date', '10/01/2023', 'Checkout Date', '20/06/2023']);
});

test('shows the pet description as rendered HTML in its own box at the end', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create(['description' => '<p>Loves <b>belly rubs</b>.</p>']);

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    $this->get(route('pets.show', $pet))
        ->assertOk()
        ->assertSeeInOrder(['Checkin Date', 'Description', '<b>belly rubs</b>'], false);
});

test('shows a placeholder when the pet has no description', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create(['description' => null]);

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    $this->get(route('pets.show', $pet))
        ->assertOk()
        ->assertSeeInOrder(['Description', '—']);
});

test('shows the pet notes in their own box after the description', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create(['description' => 'Loves belly rubs.', 'notes' => 'Needs a quiet home.']);

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    $this->get(route('pets.show', $pet))
        ->assertOk()
        ->assertSeeInOrder(['Description', 'Loves belly rubs.', 'Notes', 'Needs a quiet home.']);
});

test('shows a placeholder when the pet has no notes', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create(['notes' => null]);

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    $this->get(route('pets.show', $pet))
        ->assertOk()
        ->assertSeeInOrder(['Notes', '—']);
});

test('shows the cage field as facility, then wing, then cage code', function () {
    $shelter = Shelter::factory()->create();
    $facility = Facility::factory()->for($shelter)->create(['name' => 'North Campus']);
    $wing = Wing::factory()->for($facility)->create(['name' => 'Dog Wing']);
    $cage = Cage::factory()->for($wing)->create(['code' => 'D12']);
    $pet = Pet::factory()->for($shelter)->create(['cage_id' => $cage->id]);

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    $this->get(route('pets.show', $pet))
        ->assertOk()
        ->assertSeeInOrder(['Cage', 'North Campus', 'Dog Wing', 'D12']);
});

test('shows the sponsorship box when the pet has a sponsorship', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create(['is_sponsorable' => true]);
    Sponsorship::factory()->for($pet)->create([
        'name' => 'Maria Silva',
        'email' => 'maria@example.com',
        'phone' => '912345678',
        'address' => 'Rua das Flores, 10',
        'postal_code' => '1000-001',
        'city' => 'Lisboa',
        'send_feedback' => true,
        'send_newsletter' => false,
        'notes' => 'Prefers monthly updates',
    ]);

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    $this->get(route('pets.show', $pet))
        ->assertOk()
        ->assertSeeInOrder(['Sponsorship', 'Maria Silva', 'maria@example.com', '912345678', 'Rua das Flores, 10', '1000-001', 'Lisboa', 'Prefers monthly updates']);
});

test('shows every sponsorship, most recent first, when the pet has more than one', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create(['is_sponsorable' => true]);
    Sponsorship::factory()->for($pet)->create(['name' => 'Old Sponsor', 'created_at' => now()->subDay()]);
    Sponsorship::factory()->for($pet)->create(['name' => 'New Sponsor', 'created_at' => now()]);

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    $this->get(route('pets.show', $pet))
        ->assertOk()
        ->assertSeeInOrder(['New Sponsor', 'Old Sponsor']);
});

test('does not show the sponsorship box when the pet has no sponsorship', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create(['is_sponsorable' => false]);

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    $this->get(route('pets.show', $pet))
        ->assertOk()
        // Plain "Sponsorship" isn't safe here: the sidebar's "Sponsorships"
        // nav item renders on every page, so assert against a phrase that
        // only appears inside the sponsorship box itself.
        ->assertDontSeeText('Sponsorship Payments');
});

test('shows the adoption box after the description and notes', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create(['status' => 'adopted', 'description' => 'Loves belly rubs.', 'notes' => 'Needs a quiet home.']);
    Adoption::factory()->for($pet)->create([
        'name' => 'Maria Silva',
        'email' => 'maria@example.com',
        'phone' => '912345678',
        'address' => 'Rua das Flores, 10',
        'postal_code' => '1000-001',
        'city' => 'Lisboa',
        'adoption_date' => '2026-01-15',
        'return_date' => null,
        'adoption_fee' => '25.50',
        'application_status' => 'Approved',
        'notes' => 'Great home visit',
    ]);

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    $this->get(route('pets.show', $pet))
        ->assertOk()
        ->assertSeeInOrder([
            'Description', 'Loves belly rubs.',
            'Notes', 'Needs a quiet home.',
            'Adoption', 'Maria Silva', 'maria@example.com', '912345678',
            'Rua das Flores, 10', '1000-001', 'Lisboa', '15/01/2026',
            '25,50', 'Approved', 'Great home visit',
        ])
        ->assertSee(route('pets.adopt.edit', [$pet, $pet->adoptions()->first()]));
});

test('does not show the adoption box when the pet has never been adopted', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create(['status' => 'available']);

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    $this->get(route('pets.show', $pet))
        ->assertOk()
        ->assertDontSeeText('Adoption Fee');
});

test('still shows the adoption box after the pet has been returned', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create(['status' => 'available', 'checkout_date' => null]);
    Adoption::factory()->for($pet)->create([
        'name' => 'Maria Silva',
        'adoption_date' => '2026-01-15',
        'return_date' => '2026-03-01',
    ]);

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    $this->get(route('pets.show', $pet))
        ->assertOk()
        ->assertSeeInOrder(['Adoption', 'Maria Silva', '15/01/2026', '01/03/2026']);
});

test('shows every adoption, most recent first, when the pet has more than one', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create(['status' => 'adopted']);
    Adoption::factory()->for($pet)->create(['name' => 'Old Adopter', 'adoption_date' => '2025-01-01']);
    Adoption::factory()->for($pet)->create(['name' => 'New Adopter', 'adoption_date' => '2026-01-01']);

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    $this->get(route('pets.show', $pet))
        ->assertOk()
        ->assertSeeInOrder(['New Adopter', 'Old Adopter']);
});

test('shows the payments made for a sponsorship', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create(['is_sponsorable' => true]);
    $sponsorship = Sponsorship::factory()->for($pet)->create();
    SponsorshipPayment::factory()->for($sponsorship)->create([
        'start_date' => '2026-01-01',
        'end_date' => '2026-01-31',
        'payment_date' => '2026-01-01',
        'payment_value' => 25,
        'notes' => 'January contribution',
    ]);

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    $this->get(route('pets.show', $pet))
        ->assertOk()
        ->assertSeeInOrder(['Sponsorship Payments', '01/01/2026', '31/01/2026', '25,00', 'January contribution']);
});

test('shows a message when a sponsorship has no payments', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create(['is_sponsorable' => true]);
    Sponsorship::factory()->for($pet)->create();

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    $this->get(route('pets.show', $pet))
        ->assertOk()
        ->assertSee('No sponsorship payments registered');
});

test('defaults the payment dates to today and the end date to one year later when opening the create payment form', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create(['is_sponsorable' => true]);
    $sponsorship = Sponsorship::factory()->for($pet)->create();

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    $today = now()->format('Y-m-d');
    $oneYearFromToday = now()->addYear()->format('Y-m-d');

    Livewire::test(PetShow::class, ['pet' => $pet])
        ->call('createPayment', $sponsorship->id)
        ->assertSet('paymentStartDate', $today)
        ->assertSet('paymentEndDate', $oneYearFromToday)
        ->assertSet('paymentDate', $today)
        ->call('savePayment')
        ->assertHasNoErrors();
});

test('creates a sponsorship payment', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create(['is_sponsorable' => true]);
    $sponsorship = Sponsorship::factory()->for($pet)->create();

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    Livewire::test(PetShow::class, ['pet' => $pet])
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
    expect($payment->notes)->toBe('February contribution');
});

test('requires the payment fields', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create(['is_sponsorable' => true]);
    $sponsorship = Sponsorship::factory()->for($pet)->create();

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    Livewire::test(PetShow::class, ['pet' => $pet])
        ->call('createPayment', $sponsorship->id)
        ->set('paymentStartDate', '')
        ->set('paymentEndDate', '')
        ->set('paymentDate', '')
        ->set('paymentValue', '')
        ->call('savePayment')
        ->assertHasErrors([
            'paymentStartDate' => 'required',
            'paymentEndDate' => 'required',
            'paymentDate' => 'required',
            'paymentValue' => 'required',
        ]);
});

test('requires the payment end date to be on or after the start date', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create(['is_sponsorable' => true]);
    $sponsorship = Sponsorship::factory()->for($pet)->create();

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    Livewire::test(PetShow::class, ['pet' => $pet])
        ->call('createPayment', $sponsorship->id)
        ->set('paymentStartDate', '2026-02-10')
        ->set('paymentEndDate', '2026-02-01')
        ->set('paymentDate', '2026-02-01')
        ->set('paymentValue', '10.00')
        ->call('savePayment')
        ->assertHasErrors(['paymentEndDate' => 'after_or_equal']);
});

test('loads an existing payment for editing', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create(['is_sponsorable' => true]);
    $sponsorship = Sponsorship::factory()->for($pet)->create();
    $payment = SponsorshipPayment::factory()->for($sponsorship)->create([
        'payment_value' => 15,
        'notes' => 'Original notes',
    ]);

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    Livewire::test(PetShow::class, ['pet' => $pet])
        ->call('editPayment', $payment->id)
        ->assertSet('paymentValue', '15.00')
        ->assertSet('paymentNotes', 'Original notes');
});

test('updates an existing sponsorship payment', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create(['is_sponsorable' => true]);
    $sponsorship = Sponsorship::factory()->for($pet)->create();
    $payment = SponsorshipPayment::factory()->for($sponsorship)->create(['payment_value' => 15]);

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    Livewire::test(PetShow::class, ['pet' => $pet])
        ->call('editPayment', $payment->id)
        ->set('paymentValue', '45.00')
        ->call('savePayment')
        ->assertHasNoErrors();

    expect($sponsorship->payments()->count())->toBe(1);
    expect($payment->fresh()->payment_value)->toBe('45.00');
});

test('deletes a sponsorship payment', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create(['is_sponsorable' => true]);
    $sponsorship = Sponsorship::factory()->for($pet)->create();
    $payment = SponsorshipPayment::factory()->for($sponsorship)->create();

    $user = User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]);
    $this->actingAs($user);

    Livewire::test(PetShow::class, ['pet' => $pet])->call('deletePayment', $payment->id);

    expect($payment->fresh()->trashed())->toBeTrue();
    expect($payment->fresh()->deleted_by)->toBe($user->id);
});

test('cannot create, edit or delete a payment for a sponsorship belonging to another pet', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create(['is_sponsorable' => true]);
    $otherPet = Pet::factory()->for($shelter)->create(['is_sponsorable' => true]);
    $otherSponsorship = Sponsorship::factory()->for($otherPet)->create();
    $otherPayment = SponsorshipPayment::factory()->for($otherSponsorship)->create();

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    expect(fn () => Livewire::test(PetShow::class, ['pet' => $pet])->call('createPayment', $otherSponsorship->id))
        ->toThrow(ModelNotFoundException::class);

    expect(fn () => Livewire::test(PetShow::class, ['pet' => $pet])->call('editPayment', $otherPayment->id))
        ->toThrow(ModelNotFoundException::class);

    expect(fn () => Livewire::test(PetShow::class, ['pet' => $pet])->call('deletePayment', $otherPayment->id))
        ->toThrow(ModelNotFoundException::class);
});

test('lists the species sicknesses next to neutered status, marking which ones the pet has', function () {
    $shelter = Shelter::factory()->create();
    $species = Species::factory()->create();
    $otherSpecies = Species::factory()->create();
    $pet = Pet::factory()->for($shelter)->create(['species_id' => $species->id, 'is_neutered' => false]);

    $diagnosed = Sickness::factory()->create(['name' => 'Parvovirus']);
    $diagnosed->species()->attach($species);
    $pet->sicknesses()->attach($diagnosed, ['diagnosed_at' => now()]);

    $notDiagnosed = Sickness::factory()->create(['name' => 'Ringworm']);
    $notDiagnosed->species()->attach($species);

    $unrelated = Sickness::factory()->create(['name' => 'Feline Leukemia']);
    $unrelated->species()->attach($otherSpecies);

    $this->actingAs(User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]));

    $this->get(route('pets.show', $pet))
        ->assertOk()
        ->assertSeeInOrder(['Neutered / Spayed', 'No', 'Parvovirus', 'Yes', 'Ringworm', 'No'])
        ->assertDontSee('Feline Leukemia');
});
