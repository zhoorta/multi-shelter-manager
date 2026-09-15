<?php

declare(strict_types=1);

namespace App\Livewire\Pets\Concerns;

use App\Models\Pet;
use Illuminate\Database\Eloquent\Builder;

/**
 * Shared pet-list filtering (search + status/location/species/missing-data
 * filters) for the two pages that browse the shelter's pet list: ManagePets
 * (the interactive table) and PetPrintList (its printable counterpart,
 * which reads the same filters from the query string so the printout
 * matches whatever was on screen). Consumers must expose the five filter
 * properties below (same names/types as ManagePets already had).
 */
trait FiltersPetsList
{
    public string $search = '';

    public string $statusFilter = '';

    public string $locationFilter = '';

    public string $speciesFilter = '';

    public string $missingDataFilter = '';

    /**
     * @return Builder<Pet>
     */
    protected function filteredPetsQuery(): Builder
    {
        return Pet::query()
            ->with(['species', 'breed', 'cage.wing.facility', 'images', 'primaryColor', 'secondaryColor', 'furType', 'size', 'latestAdoption'])
            ->when(
                $this->search !== '',
                fn (Builder $query) => $query->where(
                    fn (Builder $query) => $query->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('ref', 'like', '%'.$this->search.'%')
                        ->orWhere('chip', 'like', '%'.$this->search.'%')
                        ->orWhere('internal_notes', 'like', '%'.$this->search.'%')
                ),
            )
            ->when(
                $this->statusFilter !== '',
                fn (Builder $query) => $query->where('status', $this->statusFilter),
            )
            ->when(
                $this->locationFilter !== '',
                function (Builder $query): void {
                    [$type, $id] = explode(':', $this->locationFilter, 2);

                    match ($type) {
                        'facility' => $query->whereHas('cage.wing', fn (Builder $q) => $q->where('facility_id', $id)),
                        'wing' => $query->whereHas('cage', fn (Builder $q) => $q->where('wing_id', $id)),
                        'cage' => $query->where('cage_id', $id),
                        default => null,
                    };
                },
            )
            ->when(
                $this->speciesFilter !== '',
                fn (Builder $query) => $query->where('species_id', $this->speciesFilter),
            )
            ->when(
                $this->missingDataFilter !== '',
                function (Builder $query): void {
                    match ($this->missingDataFilter) {
                        'no_age' => $query->whereNull('birth_date'),
                        'no_photo' => $query->whereDoesntHave('images'),
                        'no_checkin_date' => $query->whereNull('checkin_date'),
                        'no_location' => $query->whereNull('cage_id')->whereNotIn('status', ['adopted', 'deceased']),
                        default => null,
                    };
                },
            )
            ->orderBy('name');
    }
}
