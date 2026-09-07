<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Models\Breed;
use App\Models\Species;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Manage Breeds')]
class ManageBreeds extends Component
{
    public string $filterSpeciesId = '';

    public ?int $editingBreedId = null;

    public ?int $breedSpeciesId = null;

    public string $breedName = '';

    public bool $breedIsDefault = false;

    public function mount(): void
    {
        abort_unless(Auth::user()->role === 'admin', 403);
    }

    /**
     * @return Collection<int, Species>
     */
    #[Computed]
    public function species(): Collection
    {
        return Species::query()->orderBy('name')->get();
    }

    /**
     * @return Collection<int, Breed>
     */
    #[Computed]
    public function breeds(): Collection
    {
        return Breed::query()
            ->with('species')
            ->whereHas('species')
            ->when(
                $this->filterSpeciesId !== '',
                fn ($query) => $query->where('species_id', (int) $this->filterSpeciesId),
            )
            ->orderBy(Species::select('name')->whereColumn('species.id', 'breeds.species_id'))
            ->orderBy('name')
            ->get();
    }

    public function updatedFilterSpeciesId(): void
    {
        unset($this->breeds);
    }

    public function createBreed(): void
    {
        $this->resetBreedForm();
        $this->breedSpeciesId = $this->filterSpeciesId !== '' ? (int) $this->filterSpeciesId : null;
    }

    public function editBreed(int $breedId): void
    {
        $breed = Breed::query()->findOrFail($breedId);

        $this->editingBreedId = $breed->id;
        $this->breedSpeciesId = $breed->species_id;
        $this->breedName = $breed->name;
        $this->breedIsDefault = $breed->is_default;
    }

    public function saveBreed(): void
    {
        $validated = $this->validate([
            'breedSpeciesId' => ['required', 'integer', 'exists:species,id'],
            'breedName' => ['required', 'string', 'max:255'],
            'breedIsDefault' => ['boolean'],
        ], [], [
            'breedSpeciesId' => __('Species'),
            'breedName' => __('Name'),
        ]);

        if ($this->editingBreedId !== null) {
            Breed::query()->findOrFail($this->editingBreedId)->update([
                'species_id' => $validated['breedSpeciesId'],
                'name' => $validated['breedName'],
                'is_default' => $validated['breedIsDefault'],
            ]);

            Flux::toast(variant: 'success', text: __('Record updated successfully'));
        } else {
            Breed::query()->create([
                'species_id' => $validated['breedSpeciesId'],
                'name' => $validated['breedName'],
                'is_default' => $validated['breedIsDefault'],
            ]);

            Flux::toast(variant: 'success', text: __('Record created successfully'));
        }

        $this->resetBreedForm();
        unset($this->breeds);

        Flux::modal('breed-form')->close();
    }

    public function deleteBreed(int $breedId): void
    {
        Breed::query()->findOrFail($breedId)->delete();

        if ($this->editingBreedId === $breedId) {
            $this->resetBreedForm();
        }

        unset($this->breeds);

        Flux::toast(variant: 'success', text: __('Record deleted successfully'));
    }

    protected function resetBreedForm(): void
    {
        $this->reset(['editingBreedId', 'breedSpeciesId', 'breedName', 'breedIsDefault']);
        $this->resetErrorBag();
    }

    public function render(): View
    {
        return view('livewire.admin.manage-breeds');
    }
}
