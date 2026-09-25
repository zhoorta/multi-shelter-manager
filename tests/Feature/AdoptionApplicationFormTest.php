<?php

use App\Livewire\AdoptionApplicationForm;
use App\Models\AdoptionApplication;
use App\Models\Pet;
use App\Models\Shelter;
use App\Models\User;
use App\Notifications\AdoptionApplicationReceived;
use Illuminate\Support\Facades\Notification;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;

beforeEach(function () {
    config(['app.public_portal_enabled' => true]);
});

/**
 * A filled-in form, past the minimum fill time.
 */
function filledApplicationForm(Pet $pet, string $email = 'ana@example.com'): Testable
{
    $form = Livewire::test(AdoptionApplicationForm::class, ['petRef' => $pet->ref])
        ->set('applicantName', 'Ana Costa')
        ->set('applicantEmail', $email)
        ->set('applicantPhone', '912345678')
        ->set('applicantCity', 'Horta')
        ->set('housingType', 'house')
        ->set('hasGarden', true)
        ->set('message', 'We have a big garden and lots of time.')
        ->set('hasConsented', true);

    test()->travel(AdoptionApplicationForm::MINIMUM_FILL_SECONDS + 1)->seconds();

    return $form;
}

test('the public pet modal links to the application form', function () {
    $pet = Pet::factory()->publishedToPortal()->create();

    $this->get(route('shelters.show', [$pet->shelter, 'animal' => $pet->id]))
        ->assertSee(route('adoption-applications.create', $pet->ref));
});

test('guests can open the form for a published pet', function () {
    $pet = Pet::factory()->publishedToPortal()->create(['name' => 'Bolinha']);

    $this->get(route('adoption-applications.create', $pet->ref))
        ->assertOk()
        ->assertSee('Bolinha');
});

test('returns 404 for a pet that is not published', function () {
    $pet = Pet::factory()->create(['publish_to_portal' => false]);

    $this->get(route('adoption-applications.create', $pet->ref))->assertNotFound();
});

test('redirects to the login page when the portal is disabled', function () {
    config(['app.public_portal_enabled' => false]);
    $pet = Pet::factory()->publishedToPortal()->create();

    $this->get(route('adoption-applications.create', $pet->ref))->assertRedirect(route('login'));
});

test('staff from another shelter can open the form', function () {
    $pet = Pet::factory()->publishedToPortal()->create();
    $this->actingAs(User::factory()->forShelter(Shelter::factory()->create())->create());

    $this->get(route('adoption-applications.create', $pet->ref))->assertOk();
});

test('a valid application is saved as pending without changing the pet status', function () {
    $pet = Pet::factory()->publishedToPortal()->create();

    filledApplicationForm($pet)
        ->call('submitApplication')
        ->assertHasNoErrors()
        ->assertSet('isSubmitted', true)
        ->assertSee(__('Application sent!'));

    $application = AdoptionApplication::query()->sole();
    expect($application->pet_id)->toBe($pet->id)
        ->and($application->name)->toBe('Ana Costa')
        ->and($application->status)->toBe('pending')
        ->and($application->has_garden)->toBeTrue()
        ->and($application->consent_at)->not->toBeNull()
        ->and($pet->fresh()->status)->toBe('available');
});

test('notifies subscribed managers and staff, but not viewers or other users', function () {
    Notification::fake();
    $pet = Pet::factory()->publishedToPortal()->create();
    $manager = User::factory()->forShelter($pet->shelter, 'manager', adoptionApplicationNotifications: true)->create();
    $viewer = User::factory()->forShelter($pet->shelter, 'viewer', adoptionApplicationNotifications: true)->create();
    $unsubscribed = User::factory()->forShelter($pet->shelter, 'staff')->create();
    $otherShelterStaff = User::factory()->forShelter(Shelter::factory()->create(), 'staff', adoptionApplicationNotifications: true)->create();

    filledApplicationForm($pet)->call('submitApplication');

    Notification::assertSentTo($manager, AdoptionApplicationReceived::class);
    Notification::assertNotSentTo([$viewer, $unsubscribed, $otherShelterStaff], AdoptionApplicationReceived::class);
});

test('requires the mandatory fields and consent', function () {
    $pet = Pet::factory()->publishedToPortal()->create();

    $form = Livewire::test(AdoptionApplicationForm::class, ['petRef' => $pet->ref]);
    $this->travel(AdoptionApplicationForm::MINIMUM_FILL_SECONDS + 1)->seconds();

    $form->call('submitApplication')
        ->assertHasErrors([
            'applicantName' => 'required',
            'applicantEmail' => 'required',
            'applicantPhone' => 'required',
            'applicantCity' => 'required',
            'housingType' => 'required',
            'message' => 'required',
            'hasConsented' => 'accepted',
        ]);

    expect(AdoptionApplication::query()->count())->toBe(0);
});

test('silently discards submissions with the honeypot filled in', function () {
    $pet = Pet::factory()->publishedToPortal()->create();

    filledApplicationForm($pet)
        ->set('website', 'https://spam.example')
        ->call('submitApplication')
        ->assertSet('isSubmitted', true);

    expect(AdoptionApplication::query()->count())->toBe(0);
});

test('silently discards submissions sent faster than a person can type', function () {
    $pet = Pet::factory()->publishedToPortal()->create();

    Livewire::test(AdoptionApplicationForm::class, ['petRef' => $pet->ref])
        ->set('applicantName', 'Bot')
        ->call('submitApplication')
        ->assertSet('isSubmitted', true);

    expect(AdoptionApplication::query()->count())->toBe(0);
});

test('rejects a second pending application from the same e-mail for the same pet', function () {
    $pet = Pet::factory()->publishedToPortal()->create();
    AdoptionApplication::factory()->for($pet)->create(['email' => 'ana@example.com']);

    filledApplicationForm($pet)
        ->call('submitApplication')
        ->assertHasErrors('applicantEmail')
        ->assertSet('isSubmitted', false);

    expect(AdoptionApplication::query()->count())->toBe(1);
});

test('limits the number of applications per IP address', function () {
    $pets = Pet::factory()->publishedToPortal()->count(AdoptionApplicationForm::MAX_APPLICATIONS_PER_HOUR + 1)->create();

    foreach ($pets->take(AdoptionApplicationForm::MAX_APPLICATIONS_PER_HOUR) as $pet) {
        filledApplicationForm($pet)->call('submitApplication')->assertHasNoErrors();
    }

    filledApplicationForm($pets->last())
        ->call('submitApplication')
        ->assertHasErrors('applicantEmail');

    expect(AdoptionApplication::query()->count())->toBe(AdoptionApplicationForm::MAX_APPLICATIONS_PER_HOUR);
});

test('refuses the application when the pet was adopted after the form was opened', function () {
    $pet = Pet::factory()->publishedToPortal()->create();
    $form = filledApplicationForm($pet);

    $pet->update(['status' => 'adopted']);

    $form->call('submitApplication')->assertHasErrors('message');

    expect(AdoptionApplication::query()->count())->toBe(0);
});
