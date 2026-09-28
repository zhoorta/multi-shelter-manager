<?php

use App\Models\Pet;
use App\Models\PetTreatment;
use App\Models\Shelter;
use App\Models\Treatment;
use App\Models\User;
use App\Notifications\TreatmentDueNotification;
use Illuminate\Support\Facades\Notification;

test('sends one reminder per treatment round to subscribed users and marks the records notified', function () {
    Notification::fake();

    $shelter = Shelter::factory()->create();
    $recipient = User::factory()->forShelter($shelter, 'staff', true)->create(['email' => 'staff@example.com']);
    $unsubscribed = User::factory()->forShelter($shelter, 'staff', false)->create();

    $treatment = Treatment::factory()->create(['name' => 'Internal deworming']);
    $dueDate = today()->addDays(3);
    $petTreatments = Pet::factory()->count(3)->for($shelter)->create()
        ->map(fn (Pet $pet) => PetTreatment::factory()->for($pet)->for($treatment)->create(['due_date' => $dueDate]));

    $this->artisan('app:send-treatment-due-notifications')->assertSuccessful();

    Notification::assertSentTo(
        $recipient,
        TreatmentDueNotification::class,
        fn (TreatmentDueNotification $notification): bool => count($notification->treatmentRounds()) === 1
            && $notification->treatmentRounds()[0]['treatment'] === 'Internal deworming'
            && count($notification->treatmentRounds()[0]['pets']) === 3,
    );
    Notification::assertNotSentTo($unsubscribed, TreatmentDueNotification::class);

    $petTreatments->each(fn (PetTreatment $petTreatment) => expect($petTreatment->fresh()->notification_date)->not->toBeNull()
        ->and($petTreatment->fresh()->notification_recipients)->toBe(['staff@example.com']));
});

test('does not remind again about records already notified', function () {
    Notification::fake();

    $shelter = Shelter::factory()->create();
    User::factory()->forShelter($shelter, 'staff', true)->create();
    PetTreatment::factory()->for(Pet::factory()->for($shelter))->create(['due_date' => today()->addDays(3)]);

    $this->artisan('app:send-treatment-due-notifications')->assertSuccessful();
    $this->artisan('app:send-treatment-due-notifications')->assertSuccessful();

    Notification::assertSentTimes(TreatmentDueNotification::class, 1);
});

test('skips adopted animals and treatments due outside the next 7 days', function () {
    Notification::fake();

    $shelter = Shelter::factory()->create();
    User::factory()->forShelter($shelter, 'staff', true)->create();
    PetTreatment::factory()->for(Pet::factory()->for($shelter)->state(['status' => 'adopted']))->create(['due_date' => today()->addDays(3)]);
    PetTreatment::factory()->for(Pet::factory()->for($shelter))->create(['due_date' => today()->addDays(8)]);

    $this->artisan('app:send-treatment-due-notifications')->assertSuccessful();

    Notification::assertNothingSent();
});

test('the reminder email lists each round with its animals', function () {
    $shelter = Shelter::factory()->create();
    $recipient = User::factory()->forShelter($shelter, 'staff')->create();
    $treatment = Treatment::factory()->create(['name' => 'Internal deworming']);
    PetTreatment::factory()->for(Pet::factory()->for($shelter)->state(['name' => 'Bolinhas']))->for($treatment)->create(['due_date' => '2026-10-05']);
    PetTreatment::factory()->for(Pet::factory()->for($shelter)->state(['name' => 'Alfie']))->for($treatment)->create(['due_date' => '2026-10-05']);

    $html = (string) (new TreatmentDueNotification(PetTreatment::query()->with(['pet', 'treatment'])->get()))
        ->toMail($recipient)
        ->render();

    expect($html)->toContain('>Internal deworming</td>')
        ->and($html)->toContain('>05/10/2026</td>')
        ->and($html)->toContain('2 animals: Alfie, Bolinhas');
});
