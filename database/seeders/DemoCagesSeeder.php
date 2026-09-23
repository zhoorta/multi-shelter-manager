<?php

namespace Database\Seeders;

use App\Models\Cage;
use App\Models\Facility;
use App\Models\Pet;
use App\Models\Shelter;
use App\Models\Species;
use App\Models\Wing;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class DemoCagesSeeder extends Seeder
{
    private const FACILITY_NAME = 'Instalações Principais';

    private const CAGE_CAPACITY = 2;

    /**
     * Extra empty cages per wing, so there is room to house new pets.
     */
    private const SPARE_CAGES_PER_WING = 2;

    /**
     * Give every shelter a facility with one wing per species it houses, fill
     * each wing with cages and house every pet still at the shelter (anything
     * not adopted) in a cage of its species' wing. Safe to re-run: existing
     * facilities, wings and cages are reused and pets already caged are kept.
     */
    public function run(): void
    {
        Shelter::query()->each(function (Shelter $shelter): void {
            $petsBySpecies = Pet::query()
                ->where('shelter_id', $shelter->id)
                ->where('status', '!=', 'adopted')
                ->get()
                ->groupBy('species_id');

            if ($petsBySpecies->isEmpty()) {
                return;
            }

            $facility = Facility::query()->firstOrCreate(
                ['shelter_id' => $shelter->id, 'name' => self::FACILITY_NAME],
                ['city' => $shelter->city],
            );

            $species = Species::query()->withTrashed()->findMany($petsBySpecies->keys())->keyBy('id');

            $petsBySpecies->values()->each(function ($pets, int $wingIndex) use ($facility, $species): void {
                $speciesName = $species[$pets->first()->species_id]->name_plural;

                $wing = Wing::query()->firstOrCreate(
                    ['facility_id' => $facility->id, 'name' => 'Ala - '.$speciesName],
                    ['description' => 'Ala destinada a '.mb_strtolower($speciesName).'.'],
                );

                $cages = $this->ensureCages($wing, $pets->first()->species_id, chr(ord('A') + $wingIndex), $pets->count());

                $this->housePets($pets->whereNull('cage_id'), $cages);
            });
        });
    }

    /**
     * Create enough cages in the wing, destined to the given species, to
     * house the given number of pets plus spares.
     *
     * @return Collection<int, Cage>
     */
    private function ensureCages(Wing $wing, int $speciesId, string $codePrefix, int $petCount): Collection
    {
        $cageCount = (int) ceil($petCount / self::CAGE_CAPACITY) + self::SPARE_CAGES_PER_WING;

        return collect(range(1, $cageCount))->map(fn (int $number) => Cage::query()->firstOrCreate(
            ['wing_id' => $wing->id, 'code' => $codePrefix.'-'.str_pad((string) $number, 2, '0', STR_PAD_LEFT)],
            ['capacity' => self::CAGE_CAPACITY, 'species_id' => $speciesId],
        ));
    }

    /**
     * Put each pet in the first cage that still has room.
     *
     * @param  Collection<int, Pet>  $pets
     * @param  Collection<int, Cage>  $cages
     */
    private function housePets(Collection $pets, Collection $cages): void
    {
        $occupancy = $cages->mapWithKeys(fn (Cage $cage) => [$cage->id => $cage->pets()->count()])->all();

        foreach ($pets as $pet) {
            $cage = $cages->first(fn (Cage $cage) => $occupancy[$cage->id] < $cage->capacity);

            if ($cage === null) {
                return;
            }

            $pet->update(['cage_id' => $cage->id]);
            $occupancy[$cage->id]++;
        }
    }
}
