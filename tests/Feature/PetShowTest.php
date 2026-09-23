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
use App\Models\Vaccine;
use App\Models\Wing;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Livewire;

test('guests are redirected to the login page', function () {
    $pet = Pet::factory()->create();

    $this->get(route('pets.show', $pet))->assertRedirect(route('login'));
});

test('admins are forbidden from viewing the page', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $pet = Pet::factory()->create();

    $this->get(route('pets.show', $pet))->assertForbidden();
});

test('managers and staff can view a pet belonging to their shelter', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create(['name' => 'Rex']);

    $manager = User::factory()->forShelter($shelter, 'manager')->create();
    $this->actingAs($manager);
    $this->get(route('pets.show', $pet))->assertOk()->assertSee('Rex');

    $staff = User::factory()->forShelter($shelter, 'staff')->create();
    $this->actingAs($staff);
    $this->get(route('pets.show', $pet))->assertOk()->assertSee('Rex');
});

test('returns 404 when viewing a pet belonging to another shelter', function () {
    $otherShelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($otherShelter)->create();

    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    $this->get(route('pets.show', $pet))->assertNotFound();
});

test('shows the public portal publication, featured flag and view count', function () {
    config(['app.public_portal_enabled' => true]);

    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create(['publish_to_portal' => true, 'is_featured' => false, 'view_count' => 37]);

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    $this->get(route('pets.show', $pet))
        ->assertOk()
        ->assertSeeInOrder([
            __('Public Portal'),
            __('Publish to Portal'), __('Yes'),
            __('Is Featured'), __('No'),
            __('View Count'), '37',
            __('Accommodation'),
        ]);
});

test('hides the public portal section when the public portal is disabled', function () {
    config(['app.public_portal_enabled' => false]);

    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    $this->get(route('pets.show', $pet))
        ->assertOk()
        ->assertDontSee(__('Public Portal'))
        ->assertDontSee(__('View Count'));
});

test('links to the edit page', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    $this->get(route('pets.show', $pet))->assertSee(route('pets.edit', $pet), false);
});

test('links to the adoption registration page unless the pet is already adopted', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create(['status' => 'available']);

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    $this->get(route('pets.show', $pet))->assertSee(route('pets.adopt', $pet), false);

    $pet->update(['status' => 'adopted']);

    $this->get(route('pets.show', $pet))->assertDontSee(route('pets.adopt', $pet), false);
});

test('links to the sponsorship registration page only when the pet is sponsorable', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create(['is_sponsorable' => true]);

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    $this->get(route('pets.show', $pet))->assertSee(route('pets.sponsor', $pet), false);

    $pet->update(['is_sponsorable' => false]);

    $this->get(route('pets.show', $pet))->assertDontSee(route('pets.sponsor', $pet), false);
});

test('links to the adoption registration page only when the pet is not adopted', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create(['status' => 'available']);

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    $this->get(route('pets.show', $pet))->assertSee(route('pets.adopt', $pet), false);

    $pet->update(['status' => 'adopted']);

    $this->get(route('pets.show', $pet))->assertDontSee(route('pets.adopt', $pet), false);
});

test('links back to the pets list scoped to the pet species', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

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

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

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

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

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

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    $this->get(route('pets.show', $pet))
        ->assertOk()
        ->assertSee(__('Size'));
});

test('hides the size field entirely when the species has no sizes registered', function () {
    $shelter = Shelter::factory()->create();
    $species = Species::factory()->create();
    $pet = Pet::factory()->for($shelter)->for($species)->create(['size_id' => null]);

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    $this->get(route('pets.show', $pet))
        ->assertOk()
        ->assertDontSee(__('Size'));
});

test('shows "(Pure)" next to the breed when the species has pure breeds enabled and the pet is a pure breed', function () {
    $shelter = Shelter::factory()->create();
    $species = Species::factory()->create(['has_pure_breed_field' => true]);
    $breed = Breed::factory()->for($species)->create(['name' => 'Labrador']);
    $pet = Pet::factory()->for($shelter)->for($species)->for($breed)->create(['is_pure_breed' => true]);

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    $this->get(route('pets.show', $pet))
        ->assertOk()
        ->assertSeeText('Labrador (Pure)');
});

test('does not show "(Pure)" when the pet is a pure breed but the species has pure breeds disabled', function () {
    $shelter = Shelter::factory()->create();
    $species = Species::factory()->create(['has_pure_breed_field' => false]);
    $breed = Breed::factory()->for($species)->create(['name' => 'Labrador']);
    $pet = Pet::factory()->for($shelter)->for($species)->for($breed)->create(['is_pure_breed' => true]);

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    $this->get(route('pets.show', $pet))
        ->assertOk()
        ->assertDontSee(__('Pure'));
});

test('does not show "(Pure)" when the species has pure breeds enabled but the pet is not a pure breed', function () {
    $shelter = Shelter::factory()->create();
    $species = Species::factory()->create(['has_pure_breed_field' => true]);
    $breed = Breed::factory()->for($species)->create(['name' => 'Labrador']);
    $pet = Pet::factory()->for($shelter)->for($species)->for($breed)->create(['is_pure_breed' => false]);

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

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

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    $this->get(route('pets.show', $pet))
        ->assertOk()
        ->assertSeeInOrder(['Birth Date', '01/05/2018', 'Death Date', '15/03/2024']);
});

test('shows the checkin date', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create(['checkin_date' => '2023-01-10']);

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

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

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    $this->get(route('pets.show', $pet))
        ->assertOk()
        ->assertSeeInOrder(['Checkin Date', '10/01/2023', 'Checkout Date', '20/06/2023']);
});

test('shows the adoption date next to the name when the pet is adopted', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create(['name' => 'Rex', 'status' => 'adopted']);
    Adoption::factory()->for($pet)->create(['adoption_date' => '2026-02-10']);

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    $this->get(route('pets.show', $pet))
        ->assertOk()
        ->assertSeeInOrder(['Rex', 'Adopted', 'at', '10/02/2026'])
        ->assertDontSee('Deceased');
});

test('shows the death date next to the name when the pet is deceased, instead of the adoption date', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create([
        'name' => 'Rex',
        'status' => 'adopted',
        'date_of_death' => '2026-03-15',
    ]);
    Adoption::factory()->for($pet)->create(['adoption_date' => '2026-02-10']);

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    $this->get(route('pets.show', $pet))
        ->assertOk()
        ->assertSeeInOrder(['Rex', 'Deceased', 'at', '15/03/2026']);
});

test('does not show the adopted/deceased info next to the name for a pet that is neither', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create(['name' => 'Rex', 'status' => 'available']);

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    $this->get(route('pets.show', $pet))
        ->assertOk()
        ->assertDontSee('Adopted')
        ->assertDontSee('Deceased');
});

test('shows the pet description as rendered HTML in its own box at the end', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create(['description' => '<p>Loves <b>belly rubs</b>.</p>']);

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    $this->get(route('pets.show', $pet))
        ->assertOk()
        ->assertSeeInOrder(['Checkin Date', 'Description', '<b>belly rubs</b>'], false);
});

test('shows a placeholder when the pet has no description', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create(['description' => null]);

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    $this->get(route('pets.show', $pet))
        ->assertOk()
        ->assertSeeInOrder(['Description', '—']);
});

test('shows the pet notes in their own box after the description', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create(['description' => 'Loves belly rubs.', 'notes' => 'Needs a quiet home.']);

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    $this->get(route('pets.show', $pet))
        ->assertOk()
        ->assertSeeInOrder(['Description', 'Loves belly rubs.', 'Notes', 'Needs a quiet home.']);
});

test('shows a placeholder when the pet has no notes', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create(['notes' => null]);

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    $this->get(route('pets.show', $pet))
        ->assertOk()
        ->assertSeeInOrder(['Notes', '—']);
});

test('shows the pet clinical notes in the health section', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create(['clinical_notes' => 'Allergic to penicillin.']);

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    $this->get(route('pets.show', $pet))
        ->assertOk()
        ->assertSeeInOrder(['Health', 'Clinical Notes', 'Allergic to penicillin.', 'Adoption']);
});

test('hides the clinical notes when the pet has none', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create(['clinical_notes' => null]);

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    $this->get(route('pets.show', $pet))
        ->assertOk()
        ->assertDontSee('Clinical Notes');
});

test('shows the cage field as facility, then wing, then cage code', function () {
    $shelter = Shelter::factory()->create();
    $facility = Facility::factory()->for($shelter)->create(['name' => 'North Campus']);
    $wing = Wing::factory()->for($facility)->create(['name' => 'Dog Wing']);
    $cage = Cage::factory()->for($wing)->create(['code' => 'D12']);
    $pet = Pet::factory()->for($shelter)->create(['cage_id' => $cage->id]);

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

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

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    $this->get(route('pets.show', $pet))
        ->assertOk()
        ->assertSeeInOrder(['Sponsorship', 'Maria Silva', 'maria@example.com', '912345678', 'Rua das Flores, 10', '1000-001', 'Lisboa', 'Prefers monthly updates']);
});

test('shows every sponsorship, most recent first, when the pet has more than one', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create(['is_sponsorable' => true]);
    Sponsorship::factory()->for($pet)->create(['name' => 'Old Sponsor', 'created_at' => now()->subDay()]);
    Sponsorship::factory()->for($pet)->create(['name' => 'New Sponsor', 'created_at' => now()]);

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    $this->get(route('pets.show', $pet))
        ->assertOk()
        ->assertSeeInOrder(['New Sponsor', 'Old Sponsor']);
});

test('does not show the sponsorship box when the pet has no sponsorship', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create(['is_sponsorable' => false]);

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

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

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

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

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

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

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    $this->get(route('pets.show', $pet))
        ->assertOk()
        ->assertSeeInOrder(['Adoption', 'Maria Silva', '15/01/2026', '01/03/2026']);
});

test('shows every adoption, most recent first, when the pet has more than one', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create(['status' => 'adopted']);
    Adoption::factory()->for($pet)->create(['name' => 'Old Adopter', 'adoption_date' => '2025-01-01']);
    Adoption::factory()->for($pet)->create(['name' => 'New Adopter', 'adoption_date' => '2026-01-01']);

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

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

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    $this->get(route('pets.show', $pet))
        ->assertOk()
        ->assertSeeInOrder(['Sponsorship Payments', '01/01/2026', '31/01/2026', '25,00', 'January contribution']);
});

test('shows a message when a sponsorship has no payments', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create(['is_sponsorable' => true]);
    Sponsorship::factory()->for($pet)->create();

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    $this->get(route('pets.show', $pet))
        ->assertOk()
        ->assertSee('No sponsorship payments registered');
});

test('defaults the payment dates to today and the end date to one year later when opening the create payment form', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create(['is_sponsorable' => true]);
    $sponsorship = Sponsorship::factory()->for($pet)->create();

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

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

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

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

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

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

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

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

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

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

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

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

    $user = User::factory()->forShelter($shelter, 'staff')->create();
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

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

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

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    $this->get(route('pets.show', $pet))
        ->assertOk()
        ->assertSeeInOrder(['Neutered / Spayed', 'No', 'Parvovirus', 'Yes', 'Ringworm', 'No'])
        ->assertDontSee('Feline Leukemia');
});

test('links to the vaccination form next to the vaccinations table', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    $this->get(route('pets.show', $pet))
        ->assertOk()
        ->assertSeeHtml('href="'.route('pets.vaccinate', $pet).'"');
});

test('the vaccination link stays visible even when the heart dropdown is hidden', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create(['status' => 'adopted', 'is_sponsorable' => false]);

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    $this->get(route('pets.show', $pet))
        ->assertOk()
        ->assertSeeHtml('href="'.route('pets.vaccinate', $pet).'"');
});

test('shows a message when the pet has no vaccinations', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    $this->get(route('pets.show', $pet))
        ->assertOk()
        ->assertSee('No vaccinations registered');
});

test('lists the vaccines administered to the pet, most recent first', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();

    $rabies = Vaccine::factory()->create(['name' => 'Rabies']);
    $pet->vaccines()->attach($rabies, [
        'administered_date' => '2025-01-10',
        'due_date' => '2026-01-10',
        'lot_number' => 'LOT-OLD',
        'veterinarian_name' => 'Dr. Alves',
        'notes' => 'First dose',
    ]);

    $distemper = Vaccine::factory()->create(['name' => 'Distemper']);
    $pet->vaccines()->attach($distemper, [
        'administered_date' => '2026-02-01',
        'due_date' => null,
        'lot_number' => 'LOT-NEW',
        'veterinarian_name' => 'Dr. Costa',
        'notes' => null,
    ]);

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    $this->get(route('pets.show', $pet))
        ->assertOk()
        ->assertSeeInOrder(['Distemper', 'LOT-NEW', 'Dr. Costa', 'Rabies', 'LOT-OLD', 'Dr. Alves']);
});

test('highlights the due date amber when scheduled and due within a week or exactly a week away', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();

    $dueSoon = Vaccine::factory()->create();
    $pet->vaccines()->attach($dueSoon, ['due_date' => now()->addDays(3), 'status' => 'scheduled']);

    $exactlyAWeek = Vaccine::factory()->create();
    $pet->vaccines()->attach($exactlyAWeek, ['due_date' => now()->addWeek(), 'status' => 'scheduled']);

    $dueLater = Vaccine::factory()->create();
    $pet->vaccines()->attach($dueLater, ['due_date' => now()->addMonths(2), 'status' => 'scheduled']);

    $administeredWithSoonDueDate = Vaccine::factory()->create();
    $pet->vaccines()->attach($administeredWithSoonDueDate, ['administered_date' => now(), 'due_date' => now()->addDays(3), 'status' => 'administered']);

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    $response = $this->get(route('pets.show', $pet))->assertOk();

    // Matched against the full class string (rather than a short substring like
    // "bg-amber-50") because Flux's own components elsewhere on the page also
    // use amber/red Tailwind utility classes, causing false-positive matches.
    expect(substr_count($response->getContent(), 'bg-amber-50 text-amber-800 dark:bg-amber-950/40 dark:text-amber-200'))->toBe(2);
    expect(substr_count($response->getContent(), 'bg-red-50 text-red-800 dark:bg-red-950/40 dark:text-red-200'))->toBe(0);
});

test('highlights the due date red when scheduled and overdue', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();

    $overdue = Vaccine::factory()->create();
    $pet->vaccines()->attach($overdue, ['due_date' => now()->subDay(), 'status' => 'scheduled']);

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    $response = $this->get(route('pets.show', $pet))->assertOk();

    expect(substr_count($response->getContent(), 'bg-red-50 text-red-800 dark:bg-red-950/40 dark:text-red-200'))->toBe(1);
    expect(substr_count($response->getContent(), 'bg-amber-50 text-amber-800 dark:bg-amber-950/40 dark:text-amber-200'))->toBe(0);
});

test('does not highlight the due date once the vaccine has been administered, even if overdue', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();

    $vaccine = Vaccine::factory()->create();
    $pet->vaccines()->attach($vaccine, ['administered_date' => now(), 'due_date' => now()->subDay(), 'status' => 'administered']);

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    $response = $this->get(route('pets.show', $pet))->assertOk();

    expect(substr_count($response->getContent(), 'bg-red-50 text-red-800 dark:bg-red-950/40 dark:text-red-200'))->toBe(0);
    expect(substr_count($response->getContent(), 'bg-amber-50 text-amber-800 dark:bg-amber-950/40 dark:text-amber-200'))->toBe(0);
});

test('renders show, edit and delete actions for each vaccination', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();
    $vaccine = Vaccine::factory()->create();
    $pet->vaccines()->attach($vaccine, ['administered_date' => now()]);
    $petVaccine = $pet->vaccines()->first()->pivot;

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    $this->get(route('pets.show', $pet))
        ->assertOk()
        ->assertSee('vaccination-show-'.$petVaccine->id, false)
        ->assertSeeHtml('href="'.route('pets.vaccinate.edit', [$pet, $petVaccine]).'"')
        ->assertSee('confirm-vaccination-deletion-'.$petVaccine->id, false);
});

test('deletes a vaccination record', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();
    $vaccine = Vaccine::factory()->create();
    $pet->vaccines()->attach($vaccine, ['administered_date' => now()]);
    $petVaccine = $pet->vaccines()->first()->pivot;

    $user = User::factory()->forShelter($shelter, 'staff')->create();
    $this->actingAs($user);

    Livewire::test(PetShow::class, ['pet' => $pet])
        ->call('deleteVaccination', $petVaccine->id)
        ->assertDontSee('confirm-vaccination-deletion-'.$petVaccine->id, false);

    expect($petVaccine->fresh()->trashed())->toBeTrue();
    expect($petVaccine->fresh()->deleted_by)->toBe($user->id);
    expect($pet->fresh()->vaccines()->pluck('pet_vaccines.id'))->not->toContain($petVaccine->id);
});

test('cannot delete a vaccination belonging to another pet', function () {
    $shelter = Shelter::factory()->create();
    $pet = Pet::factory()->for($shelter)->create();
    $otherPet = Pet::factory()->for($shelter)->create();
    $vaccine = Vaccine::factory()->create();
    $otherPet->vaccines()->attach($vaccine, ['administered_date' => now()]);
    $otherPetVaccine = $otherPet->vaccines()->first()->pivot;

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    expect(fn () => Livewire::test(PetShow::class, ['pet' => $pet])->call('deleteVaccination', $otherPetVaccine->id))
        ->toThrow(ModelNotFoundException::class);
});
