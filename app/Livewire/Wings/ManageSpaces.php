<?php

declare(strict_types=1);

namespace App\Livewire\Wings;

use App\Models\Cage;
use App\Models\Wing;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Manage Wings & Cages')]
class ManageSpaces extends Component
{
    public ?int $editingWingId = null;

    public string $wingName = '';

    public string $wingDescription = '';

    public ?int $editingCageId = null;

    public ?int $cageWingId = null;

    public string $cageCode = '';

    public int $cageCapacity = 1;

    public function mount(): void
    {
        abort_unless(in_array(Auth::user()->role, ['manager', 'staff'], true), 403);
    }

    /**
     * @return Collection<int, Wing>
     */
    #[Computed]
    public function wings(): Collection
    {
        return Wing::query()
            ->with(['cages' => fn ($query) => $query->orderBy('code')])
            ->orderBy('name')
            ->get();
    }

    public function createWing(): void
    {
        $this->resetWingForm();
    }

    public function editWing(int $wingId): void
    {
        $wing = Wing::query()->findOrFail($wingId);

        $this->editingWingId = $wing->id;
        $this->wingName = $wing->name;
        $this->wingDescription = (string) $wing->description;
    }

    public function saveWing(): void
    {
        $validated = $this->validate([
            'wingName' => [
                'required',
                'string',
                'max:255',
                Rule::unique('wings', 'name')
                    ->where(fn ($query) => $query->where('shelter_id', Auth::user()->shelter_id))
                    ->ignore($this->editingWingId),
            ],
            'wingDescription' => ['nullable', 'string'],
        ], [], [
            'wingName' => __('Name'),
            'wingDescription' => __('Notes'),
        ]);

        $description = $validated['wingDescription'] !== '' ? $validated['wingDescription'] : null;

        if ($this->editingWingId !== null) {
            Wing::query()->findOrFail($this->editingWingId)->update([
                'name' => $validated['wingName'],
                'description' => $description,
            ]);

            Flux::toast(variant: 'success', text: __('Record updated successfully'));
        } else {
            Wing::query()->create([
                'name' => $validated['wingName'],
                'description' => $description,
            ]);

            Flux::toast(variant: 'success', text: __('Record created successfully'));
        }

        $this->resetWingForm();
        unset($this->wings);

        Flux::modal('wing-form')->close();
    }

    public function deleteWing(int $wingId): void
    {
        $wing = Wing::query()->findOrFail($wingId);

        // Cage has no shelter scope of its own and the DB's cascadeOnDelete
        // only fires on a hard delete, so soft-delete each cage individually
        // (rather than a bulk ->delete()) so Blameable still stamps deleted_by.
        $wing->cages()->get()->each->delete();

        $wing->delete();

        if ($this->editingWingId === $wingId) {
            $this->resetWingForm();
        }

        unset($this->wings);

        Flux::toast(variant: 'success', text: __('Record deleted successfully'));
    }

    public function createCage(int $wingId): void
    {
        // findOrFail enforces MultiShelterTrait's shelter scope, so a tampered
        // wing id from another shelter 404s before the modal ever opens.
        Wing::query()->findOrFail($wingId);

        $this->resetCageForm();
        $this->cageWingId = $wingId;
    }

    public function editCage(int $cageId): void
    {
        $cage = $this->scopedCageQuery()->findOrFail($cageId);

        $this->editingCageId = $cage->id;
        $this->cageWingId = $cage->wing_id;
        $this->cageCode = $cage->code;
        $this->cageCapacity = $cage->capacity;
    }

    public function saveCage(): void
    {
        $validated = $this->validate([
            'cageWingId' => ['required', 'integer', 'exists:wings,id'],
            'cageCode' => ['required', 'string', 'max:255'],
            'cageCapacity' => ['required', 'integer', 'min:1'],
        ], [], [
            'cageWingId' => __('Wing'),
            'cageCode' => __('Code'),
            'cageCapacity' => __('Capacity'),
        ]);

        // Re-fetch through the scoped query (not just the exists rule above)
        // so a wing belonging to another shelter 404s instead of succeeding.
        $wing = Wing::query()->findOrFail($validated['cageWingId']);

        if ($this->editingCageId !== null) {
            $this->scopedCageQuery()->findOrFail($this->editingCageId)->update([
                'wing_id' => $wing->id,
                'code' => $validated['cageCode'],
                'capacity' => $validated['cageCapacity'],
            ]);

            Flux::toast(variant: 'success', text: __('Record updated successfully'));
        } else {
            Cage::query()->create([
                'wing_id' => $wing->id,
                'code' => $validated['cageCode'],
                'capacity' => $validated['cageCapacity'],
            ]);

            Flux::toast(variant: 'success', text: __('Record created successfully'));
        }

        $this->resetCageForm();
        unset($this->wings);

        Flux::modal('cage-form')->close();
    }

    public function deleteCage(int $cageId): void
    {
        $this->scopedCageQuery()->findOrFail($cageId)->delete();

        if ($this->editingCageId === $cageId) {
            $this->resetCageForm();
        }

        unset($this->wings);

        Flux::toast(variant: 'success', text: __('Record deleted successfully'));
    }

    protected function resetWingForm(): void
    {
        $this->reset(['editingWingId', 'wingName', 'wingDescription']);
        $this->resetErrorBag();
    }

    protected function resetCageForm(): void
    {
        $this->reset(['editingCageId', 'cageWingId', 'cageCode', 'cageCapacity']);
        $this->resetErrorBag();
    }

    /**
     * Cage has no shelter_id of its own, so scope it transitively through
     * its wing (see [[models]] note on MultiShelterTrait) rather than
     * relying on a global scope that doesn't exist for this model.
     */
    protected function scopedCageQuery(): Builder
    {
        return Cage::query()->whereHas(
            'wing',
            fn ($query) => $query->where('shelter_id', Auth::user()->shelter_id),
        );
    }

    public function render(): View
    {
        return view('livewire.wings.manage-spaces');
    }
}
