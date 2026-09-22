<?php

use App\Models\Pet;
use App\Models\PetVaccine;
use App\Models\Shelter;
use App\Models\User;
use App\Models\Vaccine;
use App\Notifications\VaccinationDueNotification;
use Illuminate\Support\Facades\Notification;

test('emails users with vaccination notifications enabled about vaccines due within 7 days', function () {
    Notification::fake();

    $shelter = Shelter::factory()->create();
    $recipient = User::factory()->forShelter($shelter, 'staff', true)->create();
    User::factory()->forShelter($shelter, 'staff', false)->create();

    $pet = Pet::factory()->create(['shelter_id' => $shelter->id]);
    $vaccine = Vaccine::factory()->create();
    $pet->vaccines()->attach($vaccine, ['due_date' => today()->addDays(3), 'status' => 'scheduled']);

    $this->artisan('app:send-vaccination-due-notifications')->assertSuccessful();

    Notification::assertSentTo(
        $recipient,
        VaccinationDueNotification::class,
        fn (VaccinationDueNotification $notification) => $notification->dueVaccinations->pluck('vaccine_id')->contains($vaccine->id),
    );
});

test('does not email a shelter with no users subscribed to vaccination notifications', function () {
    Notification::fake();

    $shelter = Shelter::factory()->create();
    User::factory()->forShelter($shelter, 'staff', false)->create();

    $pet = Pet::factory()->create(['shelter_id' => $shelter->id]);
    $vaccine = Vaccine::factory()->create();
    $pet->vaccines()->attach($vaccine, ['due_date' => today()->addDays(3), 'status' => 'scheduled']);

    $this->artisan('app:send-vaccination-due-notifications')->assertSuccessful();

    Notification::assertNothingSent();
});

test('excludes vaccinations due outside the next 7 days', function () {
    Notification::fake();

    $shelter = Shelter::factory()->create();
    $recipient = User::factory()->forShelter($shelter, 'staff', true)->create();

    $pet = Pet::factory()->create(['shelter_id' => $shelter->id]);
    $tooFar = Vaccine::factory()->create();
    $pet->vaccines()->attach($tooFar, ['due_date' => today()->addDays(8), 'status' => 'scheduled']);

    $this->artisan('app:send-vaccination-due-notifications')->assertSuccessful();

    Notification::assertNotSentTo($recipient, VaccinationDueNotification::class);
});

test('excludes vaccinations that have already been administered', function () {
    Notification::fake();

    $shelter = Shelter::factory()->create();
    $recipient = User::factory()->forShelter($shelter, 'staff', true)->create();

    $pet = Pet::factory()->create(['shelter_id' => $shelter->id]);
    $administered = Vaccine::factory()->create();
    $pet->vaccines()->attach($administered, [
        'administered_date' => today(),
        'due_date' => today()->addDays(3),
        'status' => 'administered',
    ]);

    $this->artisan('app:send-vaccination-due-notifications')->assertSuccessful();

    Notification::assertNotSentTo($recipient, VaccinationDueNotification::class);
});

test('continues to the next shelter when an earlier shelter has no subscribed users', function () {
    Notification::fake();

    $shelterWithoutSubscribers = Shelter::factory()->create();
    User::factory()->forShelter($shelterWithoutSubscribers, 'staff', false)->create();

    $shelter = Shelter::factory()->create();
    $recipient = User::factory()->forShelter($shelter, 'staff', true)->create();
    $pet = Pet::factory()->create(['shelter_id' => $shelter->id]);
    $vaccine = Vaccine::factory()->create();
    $pet->vaccines()->attach($vaccine, ['due_date' => today()->addDays(3), 'status' => 'scheduled']);

    $this->artisan('app:send-vaccination-due-notifications')->assertSuccessful();

    Notification::assertSentTo($recipient, VaccinationDueNotification::class);
});

test('orders the due vaccinations by due date ascending', function () {
    Notification::fake();

    $shelter = Shelter::factory()->create();
    $recipient = User::factory()->forShelter($shelter, 'staff', true)->create();

    $pet = Pet::factory()->create(['shelter_id' => $shelter->id]);
    $laterVaccine = Vaccine::factory()->create();
    $soonerVaccine = Vaccine::factory()->create();
    $middleVaccine = Vaccine::factory()->create();
    $pet->vaccines()->attach($laterVaccine, ['due_date' => today()->addDays(6), 'status' => 'scheduled']);
    $pet->vaccines()->attach($soonerVaccine, ['due_date' => today()->addDays(1), 'status' => 'scheduled']);
    $pet->vaccines()->attach($middleVaccine, ['due_date' => today()->addDays(3), 'status' => 'scheduled']);

    $this->artisan('app:send-vaccination-due-notifications')->assertSuccessful();

    Notification::assertSentTo(
        $recipient,
        VaccinationDueNotification::class,
        fn (VaccinationDueNotification $notification) => $notification->dueVaccinations->pluck('vaccine_id')->all()
            === [$soonerVaccine->id, $middleVaccine->id, $laterVaccine->id],
    );
});

test('breaks ties on the same due date by pet name ascending', function () {
    Notification::fake();

    $shelter = Shelter::factory()->create();
    $recipient = User::factory()->forShelter($shelter, 'staff', true)->create();

    $zeltaPet = Pet::factory()->create(['shelter_id' => $shelter->id, 'name' => 'Zelta']);
    $amaraPet = Pet::factory()->create(['shelter_id' => $shelter->id, 'name' => 'Amara']);
    $mikePet = Pet::factory()->create(['shelter_id' => $shelter->id, 'name' => 'Mike']);
    $vaccine = Vaccine::factory()->create();
    $zeltaPet->vaccines()->attach($vaccine, ['due_date' => today()->addDays(3), 'status' => 'scheduled']);
    $amaraPet->vaccines()->attach($vaccine, ['due_date' => today()->addDays(3), 'status' => 'scheduled']);
    $mikePet->vaccines()->attach($vaccine, ['due_date' => today()->addDays(3), 'status' => 'scheduled']);

    $this->artisan('app:send-vaccination-due-notifications')->assertSuccessful();

    Notification::assertSentTo(
        $recipient,
        VaccinationDueNotification::class,
        fn (VaccinationDueNotification $notification) => $notification->dueVaccinations->pluck('pet.name')->all()
            === ['Amara', 'Mike', 'Zelta'],
    );
});

test('excludes vaccinations already notified', function () {
    Notification::fake();

    $shelter = Shelter::factory()->create();
    $recipient = User::factory()->forShelter($shelter, 'staff', true)->create();

    $pet = Pet::factory()->create(['shelter_id' => $shelter->id]);
    $alreadyNotified = Vaccine::factory()->create();
    $pet->vaccines()->attach($alreadyNotified, [
        'due_date' => today()->addDays(3),
        'status' => 'scheduled',
        'notification_date' => now(),
    ]);

    $this->artisan('app:send-vaccination-due-notifications')->assertSuccessful();

    Notification::assertNotSentTo($recipient, VaccinationDueNotification::class);
});

test('sets notification_date and notification_recipients on the notified vaccinations and leaves other shelters untouched', function () {
    Notification::fake();

    $shelter = Shelter::factory()->create();
    $recipient = User::factory()->forShelter($shelter, 'staff', true)->create();

    $pet = Pet::factory()->create(['shelter_id' => $shelter->id]);
    $vaccine = Vaccine::factory()->create();
    $pet->vaccines()->attach($vaccine, ['due_date' => today()->addDays(3), 'status' => 'scheduled']);
    $petVaccine = PetVaccine::query()->where('pet_id', $pet->id)->where('vaccine_id', $vaccine->id)->firstOrFail();

    $otherShelter = Shelter::factory()->create();
    $otherPet = Pet::factory()->create(['shelter_id' => $otherShelter->id]);
    $otherVaccine = Vaccine::factory()->create();
    $otherPet->vaccines()->attach($otherVaccine, ['due_date' => today()->addDays(3), 'status' => 'scheduled']);
    $otherPetVaccine = PetVaccine::query()->where('pet_id', $otherPet->id)->where('vaccine_id', $otherVaccine->id)->firstOrFail();

    $this->artisan('app:send-vaccination-due-notifications')->assertSuccessful();

    expect($petVaccine->fresh()->notification_date)->not->toBeNull()
        ->and($petVaccine->fresh()->notification_recipients)->toBe([$recipient->email])
        ->and($otherPetVaccine->fresh()->notification_date)->toBeNull()
        ->and($otherPetVaccine->fresh()->notification_recipients)->toBeNull();
});
