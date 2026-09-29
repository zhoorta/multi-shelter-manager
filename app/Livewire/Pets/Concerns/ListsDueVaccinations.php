<?php

declare(strict_types=1);

namespace App\Livewire\Pets\Concerns;

use App\Models\Pet;
use App\Models\PetVaccine;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * The vaccination plan's shared query: open (pending) vaccinations of the
 * shelter's resident animals, bucketed by the month they are due. Month 0
 * stands for everything due before the plan's year, i.e. still open from
 * earlier years.
 */
trait ListsDueVaccinations
{
    /**
     * Pending vaccinations of resident pets due in the given month of the
     * year (0 = before the year), optionally of one vaccine.
     *
     * @return Builder<PetVaccine>
     */
    protected function dueVaccinationsQuery(int $year, int $month, ?int $vaccineId = null): Builder
    {
        $yearStart = Carbon::create($year)->startOfYear();

        return PetVaccine::query()
            ->pending()
            // Pet's MultiShelterTrait scope keeps this to the acting user's
            // shelter, since PetVaccine has no shelter_id of its own.
            ->whereIn('pet_id', Pet::query()->resident()->select('id'))
            ->when(
                $month === 0,
                fn (Builder $query) => $query->whereDate('due_date', '<', $yearStart->toDateString()),
                fn (Builder $query) => $query
                    ->whereDate('due_date', '>=', $yearStart->copy()->month($month)->toDateString())
                    ->whereDate('due_date', '<=', $yearStart->copy()->month($month)->endOfMonth()->toDateString()),
            )
            ->when($vaccineId !== null, fn (Builder $query) => $query->where('vaccine_id', $vaccineId));
    }

    /**
     * The due vaccinations grouped per pet (sorted by name), each with the
     * vaccines it needs, for the vet list.
     *
     * @return Collection<int, array{pet: Pet, vaccinations: EloquentCollection<int, PetVaccine>}>
     */
    protected function dueVaccinationsByPet(int $year, int $month, ?int $vaccineId = null): Collection
    {
        $vaccinations = $this->dueVaccinationsQuery($year, $month, $vaccineId)
            ->with(['pet.species', 'pet.cage.wing', 'vaccine'])
            ->orderBy('due_date')
            ->get();

        return $vaccinations
            ->map(fn (PetVaccine $petVaccine): Pet => $petVaccine->pet)
            ->unique('id')
            ->sortBy(fn (Pet $pet): string => mb_strtolower($pet->name))
            ->values()
            ->map(fn (Pet $pet): array => ['pet' => $pet, 'vaccinations' => $vaccinations->where('pet_id', $pet->id)->values()]);
    }

    /**
     * Title of a plan month, e.g. "maio 2028", or "Before 2028" for month 0.
     */
    protected function planMonthLabel(int $year, int $month): string
    {
        return $month === 0
            ? __('Before :year', ['year' => $year])
            : Carbon::create($year, $month)->translatedFormat('F Y');
    }
}
