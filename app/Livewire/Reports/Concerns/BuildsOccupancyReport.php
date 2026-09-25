<?php

declare(strict_types=1);

namespace App\Livewire\Reports\Concerns;

use App\Models\Cage;
use App\Models\Pet;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;

/**
 * The Occupancy section of the reports. Cage capacity is only a guideline
 * (a cage may hold more), so occupancy is a neutral percentage, never an
 * "over capacity" warning. Capacity has no history: past months are
 * compared with today's capacity. Foster family wings are not shelter
 * space: their cages are left out of the capacity and the wing list, and
 * their animals are counted apart. Needs BuildsShelterReport.
 */
trait BuildsOccupancyReport
{
    /**
     * @return array{
     *     totals: array{capacity: int, housed: int, withoutCage: int, inFosterFamilies: int, rate: ?int},
     *     wings: list<array{label: string, value: int, display: string}>,
     *     rateBuckets: list<?int>
     * }
     */
    #[Computed]
    public function occupancyReport(): array
    {
        $shelterId = Auth::user()->current_shelter_id;

        $cages = Cage::query()
            ->whereHas('wing', fn (Builder $query) => $query->where('is_foster', false)->whereHas('facility', fn (Builder $query) => $query->where('shelter_id', $shelterId)))
            ->with('wing:id,facility_id,name', 'wing.facility:id,shelter_id,name')
            ->withCount(['pets as active_pets_count' => fn (Builder $query) => $query->where('status', '!=', 'adopted')->whereNull('date_of_death')])
            ->get(['id', 'wing_id', 'capacity']);

        $capacity = (int) $cages->sum('capacity');
        $housed = (int) $cages->sum('active_pets_count');

        $wings = array_values($cages
            ->groupBy('wing_id')
            ->map(function ($wingCages): array {
                $wing = $wingCages->first()->wing;
                $wingCapacity = (int) $wingCages->sum('capacity');
                $wingHoused = (int) $wingCages->sum('active_pets_count');

                return [
                    'label' => trim(($wing->facility->name ? $wing->facility->name.' · ' : '').$wing->name),
                    'value' => $wingCapacity > 0 ? (int) round($wingHoused / $wingCapacity * 100) : 0,
                    'display' => $wingHoused.' / '.$wingCapacity.($wingCapacity > 0 ? ' ('.round($wingHoused / $wingCapacity * 100).'%)' : ''),
                ];
            })
            ->sortBy('label')
            ->all());

        $withoutCage = Pet::query()
            ->where('shelter_id', $shelterId)
            ->where('status', '!=', 'adopted')
            ->whereNull('date_of_death')
            ->whereNull('cage_id')
            ->count();

        $inFosterFamilies = Pet::query()
            ->where('shelter_id', $shelterId)
            ->where('status', '!=', 'adopted')
            ->whereNull('date_of_death')
            ->whereHas('cage.wing', fn (Builder $query) => $query->where('is_foster', true))
            ->count();

        return [
            'totals' => [
                'capacity' => $capacity,
                'housed' => $housed,
                'withoutCage' => $withoutCage,
                'inFosterFamilies' => $inFosterFamilies,
                'rate' => $capacity > 0 ? (int) round(($housed + $withoutCage) / $capacity * 100) : null,
            ],
            'wings' => $wings,
            'rateBuckets' => array_map(
                fn (array $bucket): ?int => $bucket['population'] === null || $capacity === 0 ? null : (int) round($bucket['population'] / $capacity * 100),
                $this->report()['buckets'],
            ),
        ];
    }
}
