<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Models\Species;
use App\Models\Vaccine;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Manage Vaccines')]
class ManageVaccines extends Component
{
    public ?int $editingVaccineId = null;

    public string $vaccineName = '';

    /**
     * @var array<int, int>
     */
    public array $vaccineSpeciesIds = [];

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
        return Species::query()->orderBy('name')->get();
    }

    /**
     * @return Collection<int, Vaccine>
     */
    #[Computed]
    public function vaccines(): Collection
    {
        return Vaccine::query()
            ->with('species')
            ->orderBy('name')
            ->get();
    }

    public function createVaccine(): void
    {
        $this->resetVaccineForm();
    }

    public function editVaccine(int $vaccineId): void
    {
        $vaccine = Vaccine::query()->with('species')->findOrFail($vaccineId);

        $this->editingVaccineId = $vaccine->id;
        $this->vaccineName = $vaccine->name;
        $this->vaccineSpeciesIds = $vaccine->species->pluck('id')->all();
    }

    public function saveVaccine(): void
    {
        $validated = $this->validate([
            'vaccineName' => ['required', 'string', 'max:255'],
            'vaccineSpeciesIds' => ['array'],
            'vaccineSpeciesIds.*' => ['integer', 'exists:species,id'],
        ], [], [
            'vaccineName' => __('Name'),
            'vaccineSpeciesIds' => __('Species'),
        ]);

        if ($this->editingVaccineId !== null) {
            $vaccine = Vaccine::query()->findOrFail($this->editingVaccineId);
            $vaccine->update(['name' => $validated['vaccineName']]);

            Flux::toast(variant: 'success', text: __('Record updated successfully'));
        } else {
            $vaccine = Vaccine::query()->create(['name' => $validated['vaccineName']]);

            Flux::toast(variant: 'success', text: __('Record created successfully'));
        }

        $vaccine->species()->sync($validated['vaccineSpeciesIds']);

        $this->resetVaccineForm();
        unset($this->vaccines);

        Flux::modal('vaccine-form')->close();
    }

    public function deleteVaccine(int $vaccineId): void
    {
        Vaccine::query()->findOrFail($vaccineId)->delete();

        if ($this->editingVaccineId === $vaccineId) {
            $this->resetVaccineForm();
        }

        unset($this->vaccines);

        Flux::toast(variant: 'success', text: __('Record deleted successfully'));
    }

    protected function resetVaccineForm(): void
    {
        $this->reset(['editingVaccineId', 'vaccineName', 'vaccineSpeciesIds']);
        $this->resetErrorBag();
    }

    public function render(): View
    {
        return view('livewire.admin.manage-vaccines');
    }
}
