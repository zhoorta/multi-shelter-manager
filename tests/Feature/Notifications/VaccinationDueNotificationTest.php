<?php

use App\Models\Pet;
use App\Models\PetVaccine;
use App\Models\Shelter;
use App\Models\User;
use App\Models\Vaccine;
use App\Notifications\VaccinationDueNotification;

test('links each pet name to its pet page in the reminder email', function () {
    $shelter = Shelter::factory()->create();
    $recipient = User::factory()->create(['shelter_id' => $shelter->id]);

    $pet = Pet::factory()->create(['shelter_id' => $shelter->id]);
    $vaccine = Vaccine::factory()->create();
    $pet->vaccines()->attach($vaccine, ['due_date' => today()->addDays(3), 'status' => 'scheduled']);
    $petVaccine = PetVaccine::query()->where('pet_id', $pet->id)->where('vaccine_id', $vaccine->id)->firstOrFail();

    $html = (new VaccinationDueNotification(PetVaccine::query()->whereKey($petVaccine->id)->get()))
        ->toMail($recipient)
        ->render();

    expect((string) $html)->toContain('href="'.route('pets.show', $pet).'"')
        ->and((string) $html)->toContain("{$pet->name} ({$pet->ref})");
});

test('renders the due vaccinations as an HTML table', function () {
    $shelter = Shelter::factory()->create();
    $recipient = User::factory()->create(['shelter_id' => $shelter->id]);

    $pet = Pet::factory()->create(['shelter_id' => $shelter->id]);
    $vaccine = Vaccine::factory()->create(['name' => 'Antirrábica']);
    $pet->vaccines()->attach($vaccine, ['due_date' => today()->addDays(3), 'status' => 'scheduled']);
    $petVaccine = PetVaccine::query()->where('pet_id', $pet->id)->where('vaccine_id', $vaccine->id)->firstOrFail();

    $html = (string) (new VaccinationDueNotification(PetVaccine::query()->whereKey($petVaccine->id)->get()))
        ->toMail($recipient)
        ->render();

    expect($html)->toContain('<table')
        ->and($html)->toContain('<thead')
        ->and($html)->toContain('>Pet</th>')
        ->and($html)->toContain('>Vaccine</th>')
        ->and($html)->toContain('>Due Date</th>')
        ->and($html)->toContain('>Antirrábica</td>')
        ->and($html)->toContain('>'.$petVaccine->due_date->format('d/m/Y').'</td>');
});
