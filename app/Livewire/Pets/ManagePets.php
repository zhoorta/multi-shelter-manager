<?php

declare(strict_types=1);

namespace App\Livewire\Pets;

use App\Livewire\Pets\Concerns\FiltersPetsList;
use App\Models\Cage;
use App\Models\Facility;
use App\Models\Pet;
use App\Models\Species;
use App\Models\Wing;
use Flux\Flux;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Manage Pets')]
class ManagePets extends Component
{
    use FiltersPetsList, WithPagination;

    #[Url]
    public string $speciesFilter = '';

    public function mount(): void
    {
        abort_unless(! Auth::user()->is_admin, 403);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingLocationFilter(): void
    {
        $this->resetPage();
    }

    public function updatingSpeciesFilter(): void
    {
        $this->resetPage();
    }

    public function updatingMissingDataFilter(): void
    {
        $this->resetPage();
    }

    /**
     * @return LengthAwarePaginator<int, Pet>
     */
    #[Computed]
    public function pets(): LengthAwarePaginator
    {
        return $this->filteredPetsQuery()->paginate(20);
    }

    /**
     * Facilities (with their wings and cages nested) to build the location
     * filter's hierarchical select: a Facility, Wing, or Cage can each be
     * selected directly to filter the list. Facility is scoped by
     * MultiShelterTrait; wings/cages are loaded through it so they inherit
     * the same shelter scoping (see .ai/rules/facilities.md).
     *
     * Each cage is annotated with available_space and availability_color
     * (green/yellow/red), mirroring PetForm::cages() (see
     * .ai/rules/views-livewire-pets.md), so the select can show how full it
     * is.
     *
     * @return Collection<int, Facility>
     */
    #[Computed]
    public function facilities(): Collection
    {
        return Facility::query()
            ->with([
                'wings' => fn (HasMany $query) => $query->orderBy('name'),
                'wings.cages' => fn (HasMany $query) => $query->orderBy('code')
                    ->withCount(['pets as active_pets_count' => fn (Builder $query) => $query->where('status', '!=', 'adopted')->whereNull('date_of_death')]),
            ])
            ->orderBy('name')
            ->get()
            ->each(function (Facility $facility): void {
                $facility->wings->each(function (Wing $wing): void {
                    $wing->cages->each(function (Cage $cage): void {
                        $availableSpace = max(0, $cage->capacity - $cage->active_pets_count);

                        $cage->available_space = $availableSpace;
                        $cage->availability_color = match (true) {
                            $availableSpace <= 0 => 'red',
                            $cage->active_pets_count > $cage->capacity * 0.8 => 'yellow',
                            default => 'green',
                        };
                    });
                });
            });
    }

    /**
     * The species currently selected via the sidebar/filter, if any —
     * drives the page heading and is forwarded to the create link so a
     * pet created from a species-scoped list starts locked to it.
     */
    #[Computed]
    public function selectedSpecies(): ?Species
    {
        if ($this->speciesFilter === '') {
            return null;
        }

        return Species::query()->find($this->speciesFilter);
    }

    public function deletePet(int $petId): void
    {
        // findOrFail relies on MultiShelterTrait's global scope, so a
        // tampered pet id from another shelter 404s (see .ai/rules/models.md).
        Pet::query()->findOrFail($petId)->delete();

        unset($this->pets);

        Flux::toast(variant: 'success', text: __('Record deleted successfully'));
    }

    public function render(): View
    {
        return view('livewire.pets.manage-pets');
    }
}
