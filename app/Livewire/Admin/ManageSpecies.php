<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Models\Species;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Manage Species')]
class ManageSpecies extends Component
{
    public ?int $editingSpeciesId = null;

    public string $speciesName = '';

    public string $speciesNamePlural = '';

    public bool $speciesHasPureBreedField = false;

    public function mount(): void
    {
        abort_unless(Auth::user()->is_admin, 403);
    }

    /**
     * @return Collection<int, Species>
     */
    #[Computed]
    public function species(): Collection
    {
        return Species::query()
            ->withCount('breeds')
            ->orderBy('name')
            ->get();
    }

    public function createSpecies(): void
    {
        $this->resetSpeciesForm();
    }

    public function editSpecies(int $speciesId): void
    {
        $species = Species::query()->findOrFail($speciesId);

        $this->editingSpeciesId = $species->id;
        $this->speciesName = $species->name;
        $this->speciesNamePlural = $species->name_plural;
        $this->speciesHasPureBreedField = $species->has_pure_breed_field;
    }

    public function saveSpecies(): void
    {
        $validated = $this->validate([
            'speciesName' => [
                'required',
                'string',
                'max:255',
                Rule::unique('species', 'name')->ignore($this->editingSpeciesId),
            ],
            'speciesNamePlural' => [
                'required',
                'string',
                'max:255',
                Rule::unique('species', 'name_plural')->ignore($this->editingSpeciesId),
            ],
            'speciesHasPureBreedField' => ['boolean'],
        ], [], [
            'speciesName' => __('Name'),
            'speciesNamePlural' => __('Plural Name'),
        ]);

        if ($this->editingSpeciesId !== null) {
            Species::query()->findOrFail($this->editingSpeciesId)->update([
                'name' => $validated['speciesName'],
                'name_plural' => $validated['speciesNamePlural'],
                'has_pure_breed_field' => $validated['speciesHasPureBreedField'],
            ]);

            Flux::toast(variant: 'success', text: __('Record updated successfully'));
        } else {
            Species::query()->create([
                'name' => $validated['speciesName'],
                'name_plural' => $validated['speciesNamePlural'],
                'has_pure_breed_field' => $validated['speciesHasPureBreedField'],
            ]);

            Flux::toast(variant: 'success', text: __('Record created successfully'));
        }

        $this->resetSpeciesForm();
        unset($this->species);

        Flux::modal('species-form')->close();
    }

    public function deleteSpecies(int $speciesId): void
    {
        Species::query()->findOrFail($speciesId)->delete();

        if ($this->editingSpeciesId === $speciesId) {
            $this->resetSpeciesForm();
        }

        unset($this->species);

        Flux::toast(variant: 'success', text: __('Record deleted successfully'));
    }

    protected function resetSpeciesForm(): void
    {
        $this->reset(['editingSpeciesId', 'speciesName', 'speciesNamePlural', 'speciesHasPureBreedField']);
        $this->resetErrorBag();
    }

    public function render(): View
    {
        return view('livewire.admin.manage-species');
    }
}
