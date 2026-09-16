<?php

namespace Database\Seeders;

use App\Models\Breed;
use App\Models\Pet;
use App\Models\PetVaccine;
use App\Models\Shelter;
use App\Models\Species;
use App\Models\Vaccine;
use Illuminate\Database\Seeder;

class DemoPetsSeeder extends Seeder
{
    /**
     * Portuguese species names, matching the base DatabaseSeeder's species rows.
     */
    private const DOG_SPECIES = 'Cão';

    private const CAT_SPECIES = 'Gato';

    private const DOG_NAMES = ['Rex', 'Bobby', 'Thor', 'Max', 'Zeca', 'Duque', 'Bento', 'Toby', 'Leo', 'Nero'];

    private const CAT_NAMES = ['Mimi', 'Luna', 'Simba', 'Salem', 'Tigre', 'Mia', 'Bela', 'Nina', 'Oscar', 'Frida'];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $shelter = Shelter::query()->firstOrFail();

        $breedIdsBySpecies = Breed::query()->get()->groupBy('species_id')->map(fn ($breeds) => $breeds->pluck('id')->all());
        $vaccineIdsBySpecies = Vaccine::query()->with('species')->get()
            ->flatMap(fn (Vaccine $vaccine) => $vaccine->species->pluck('id')->map(fn ($speciesId) => [$speciesId, $vaccine->id]))
            ->groupBy(fn ($pair) => $pair[0])
            ->map(fn ($pairs) => $pairs->pluck(1)->all());

        $species = Species::query()->pluck('id', 'name');

        $dogSpeciesId = $species[self::DOG_SPECIES];
        $catSpeciesId = $species[self::CAT_SPECIES];

        $pets = [];

        for ($i = 0; $i < 20; $i++) {
            $isDog = $i % 2 === 0;
            $speciesId = $isDog ? $dogSpeciesId : $catSpeciesId;
            $names = $isDog ? self::DOG_NAMES : self::CAT_NAMES;

            $pet = Pet::create([
                'shelter_id' => $shelter->id,
                'species_id' => $speciesId,
                'breed_id' => $breedIdsBySpecies[$speciesId][array_rand($breedIdsBySpecies[$speciesId])],
                'ref' => '',
                'name' => $names[intdiv($i, 2) % count($names)],
                'gender' => $i % 2 === 0 ? 'male' : 'female',
                'is_neutered' => (bool) ($i % 3 === 0),
                'status' => 'available',
                'is_adoptable' => true,
                'checkin_date' => now()->subDays(random_int(1, 200)),
            ]);

            $pet->update(['ref' => 'PET'.str_pad((string) $pet->id, 5, '0', STR_PAD_LEFT)]);

            $pets[] = $pet;
        }

        // Spread 50 vaccination records across the 20 pets in five 10-record
        // buckets, covering every state the vaccination reminder command
        // (App\Console\Commands\SendVaccinationDueNotifications) needs to
        // discriminate between: overdue, due within 7 days, due later,
        // due within 7 days but already notified, and already administered.
        for ($i = 0; $i < 50; $i++) {
            $pet = $pets[$i % 20];
            $vaccineIds = $vaccineIdsBySpecies[$pet->species_id];
            $vaccineId = $vaccineIds[array_rand($vaccineIds)];

            $bucket = intdiv($i, 10);

            [$administeredDate, $dueDate, $status, $notificationDate] = match ($bucket) {
                0 => [null, now()->subDays(random_int(1, 10))->toDateString(), 'scheduled', null],
                1 => [null, now()->addDays(random_int(0, 7))->toDateString(), 'scheduled', null],
                2 => [null, now()->addDays(random_int(8, 30))->toDateString(), 'scheduled', null],
                3 => [null, now()->addDays(random_int(0, 7))->toDateString(), 'scheduled', now()->subDay()],
                default => [now()->subDays(random_int(30, 400))->toDateString(), null, 'administered', null],
            };

            PetVaccine::create([
                'pet_id' => $pet->id,
                'vaccine_id' => $vaccineId,
                'administered_date' => $administeredDate,
                'due_date' => $dueDate,
                'status' => $status,
                'notification_date' => $notificationDate,
            ]);
        }
    }
}
