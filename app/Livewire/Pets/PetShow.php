<?php

declare(strict_types=1);

namespace App\Livewire\Pets;

use App\Models\Pet;
use App\Models\Sickness;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

class PetShow extends Component
{
    public Pet $pet;

    public function mount(Pet $pet): void
    {
        abort_unless(in_array(Auth::user()->role, ['manager', 'staff'], true), 403);

        $this->pet = $pet->load([
            'species', 'breed', 'cage.wing', 'images', 'primaryColor', 'secondaryColor', 'furType', 'sicknesses',
        ]);
    }

    /**
     * Sicknesses that can affect the pet's species (see sickness_species
     * pivot), each to be shown alongside whether the pet has it.
     *
     * @return Collection<int, Sickness>
     */
    #[Computed]
    public function sicknesses(): Collection
    {
        return Sickness::query()
            ->whereHas('species', fn (Builder $query) => $query->whereKey($this->pet->species_id))
            ->orderBy('name')
            ->get();
    }

    public function render(): View
    {
        return view('livewire.pets.pet-show')->title($this->pet->name);
    }
}
