<?php

declare(strict_types=1);

namespace App\Livewire\Pets\Concerns;

use App\Actions\ExportShelterData;
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
     * Pseudo month that stands for the whole plan year (January to December).
     */
    protected const WHOLE_YEAR = 13;

    /**
     * Pending vaccinations of resident pets due in the given month of the
     * year (0 = before the year, WHOLE_YEAR = all twelve months), optionally of
     * one vaccine.
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
                    ->whereDate('due_date', '>=', $yearStart->copy()->month($month === self::WHOLE_YEAR ? 1 : $month)->toDateString())
                    ->whereDate('due_date', '<=', $yearStart->copy()->month($month === self::WHOLE_YEAR ? 12 : $month)->endOfMonth()->toDateString()),
            )
            ->when($vaccineId !== null, fn (Builder $query) => $query->where('vaccine_id', $vaccineId));
    }

    /**
     * The due vaccinations grouped per pet, each with the vaccines it needs,
     * for the vet list. Sorted by facility, wing and box (natural order, so box 2 comes
     * before box 10) to walk the corridors in sequence; pets without a box
     * come last, and ties fall back to the name.
     *
     * @return Collection<int, array{pet: Pet, vaccinations: EloquentCollection<int, PetVaccine>}>
     */
    protected function dueVaccinationsByPet(int $year, int $month, ?int $vaccineId = null): Collection
    {
        $vaccinations = $this->dueVaccinationsQuery($year, $month, $vaccineId)
            ->with(['pet.species', 'pet.cage.wing.facility', 'vaccine'])
            ->orderBy('due_date')
            ->get();

        return $vaccinations
            ->map(fn (PetVaccine $petVaccine): Pet => $petVaccine->pet)
            ->unique('id')
            ->sort(fn (Pet $first, Pet $second): int => $this->boxSortKey($first) === $this->boxSortKey($second)
                ? strnatcasecmp($first->name, $second->name)
                : strnatcasecmp($this->boxSortKey($first), $this->boxSortKey($second)))
            ->values()
            ->map(fn (Pet $pet): array => ['pet' => $pet, 'vaccinations' => $vaccinations->where('pet_id', $pet->id)->values()]);
    }

    /**
     * "facility wing box" of a pet for ordering; a leading "~" sorts unhoused pets last.
     */
    private function boxSortKey(Pet $pet): string
    {
        return $pet->cage === null ? '~' : trim(($pet->cage->wing?->facility?->name ?? '').' '.($pet->cage->wing?->name ?? '').' '.$pet->cage->code);
    }

    /**
     * Spreadsheet (CSV) of the vet list: one row per pet with its chip, birth
     * date and box, and the last and next dose date of each vaccine listed.
     *
     * @param  Collection<int, array{pet: Pet, vaccinations: EloquentCollection<int, PetVaccine>}>  $duePets
     */
    protected function dueVaccinationsCsv(Collection $duePets): string
    {
        $vaccines = $duePets
            ->flatMap(fn (array $row) => $row['vaccinations']->pluck('vaccine'))
            ->unique('id')
            ->sortBy('name')
            ->values();

        return ExportShelterData::csv(
            [
                __('Name'),
                __('Microchip / Chip'),
                __('Birth Date'),
                __('Facility'),
                __('Wing'),
                __('Cage'),
                ...$vaccines->flatMap(fn ($vaccine): array => [
                    $vaccine->name.' — '.__('Administered Date'),
                    $vaccine->name.' — '.__('Next Due Date'),
                ])->all(),
            ],
            $duePets->map(function (array $row) use ($vaccines): array {
                $pet = $row['pet'];

                return [
                    $pet->name,
                    $pet->chip,
                    $pet->birth_date?->format('d/m/Y'),
                    $pet->cage?->wing?->facility?->name,
                    $pet->cage?->wing?->name,
                    $pet->cage?->code,
                    ...$vaccines->flatMap(function ($vaccine) use ($row): array {
                        $vaccination = $row['vaccinations']->firstWhere('vaccine_id', $vaccine->id);

                        return [$vaccination?->administered_date?->format('d/m/Y'), $vaccination?->due_date->format('d/m/Y')];
                    })->all(),
                ];
            }),
        );
    }

    /**
     * Title of a plan month, e.g. "maio 2028", or "Before 2028" for month 0.
     */
    protected function planMonthLabel(int $year, int $month): string
    {
        return match ($month) {
            0 => __('Before :year', ['year' => $year]),
            self::WHOLE_YEAR => (string) $year,
            default => Carbon::create($year, $month)->translatedFormat('F Y'),
        };
    }
}
