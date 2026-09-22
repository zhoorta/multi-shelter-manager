<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Models\Region;
use App\Models\Shelter;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Manage Regions')]
class ManageRegions extends Component
{
    public ?int $editingRegionId = null;

    public string $regionName = '';

    public function mount(): void
    {
        abort_unless(Auth::user()->is_admin, 403);
    }

    /**
     * @return Collection<int, Region>
     */
    #[Computed]
    public function regions(): Collection
    {
        return Region::query()
            ->orderBy('name')
            ->get();
    }

    public function createRegion(): void
    {
        $this->resetRegionForm();
    }

    public function editRegion(int $regionId): void
    {
        $region = Region::query()->findOrFail($regionId);

        $this->editingRegionId = $region->id;
        $this->regionName = $region->name;
    }

    public function saveRegion(): void
    {
        $validated = $this->validate([
            'regionName' => [
                'required',
                'string',
                'max:255',
                Rule::unique('regions', 'name')->ignore($this->editingRegionId),
            ],
        ], [], [
            'regionName' => __('Name'),
        ]);

        if ($this->editingRegionId !== null) {
            Region::query()->findOrFail($this->editingRegionId)->update([
                'name' => $validated['regionName'],
            ]);

            Flux::toast(variant: 'success', text: __('Record updated successfully'));
        } else {
            Region::query()->create([
                'name' => $validated['regionName'],
            ]);

            Flux::toast(variant: 'success', text: __('Record created successfully'));
        }

        $this->resetRegionForm();
        unset($this->regions);

        Flux::modal('region-form')->close();
    }

    /**
     * Soft-delete the region and unassign it from every shelter (including
     * trashed ones), since a soft delete does not trigger the FK's ON DELETE.
     */
    public function deleteRegion(int $regionId): void
    {
        $region = Region::query()->findOrFail($regionId);

        DB::transaction(function () use ($region): void {
            Shelter::withTrashed()->where('region_id', $region->id)->update(['region_id' => null]);

            $region->delete();
        });

        if ($this->editingRegionId === $regionId) {
            $this->resetRegionForm();
        }

        unset($this->regions);

        Flux::toast(variant: 'success', text: __('Record deleted successfully'));
    }

    protected function resetRegionForm(): void
    {
        $this->reset(['editingRegionId', 'regionName']);
        $this->resetErrorBag();
    }

    public function render(): View
    {
        return view('livewire.admin.manage-regions');
    }
}
