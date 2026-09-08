<?php

declare(strict_types=1);

namespace App\Livewire\Pets;

use App\Models\Pet;
use App\Models\Species;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Title('Manage Pets')]
class ManagePets extends Component
{
    public string $search = '';

    public string $statusFilter = '';

    #[Url]
    public string $speciesFilter = '';

    public function mount(): void
    {
        abort_unless(in_array(Auth::user()->role, ['manager', 'staff'], true), 403);
    }

    /**
     * @return Collection<int, Pet>
     */
    #[Computed]
    public function pets(): Collection
    {
        return Pet::query()
            ->with(['species', 'breed', 'cage.wing', 'images', 'primaryColor', 'secondaryColor', 'furType'])
            ->when(
                $this->search !== '',
                fn (Builder $query) => $query->where(
                    fn (Builder $query) => $query->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('chip', 'like', '%'.$this->search.'%')
                ),
            )
            ->when(
                $this->statusFilter !== '',
                fn (Builder $query) => $query->where('status', $this->statusFilter),
            )
            ->when(
                $this->speciesFilter !== '',
                fn (Builder $query) => $query->where('species_id', $this->speciesFilter),
            )
            ->orderBy('name')
            ->get();
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
