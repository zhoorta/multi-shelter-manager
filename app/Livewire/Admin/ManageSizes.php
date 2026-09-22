<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Models\Size;
use App\Models\Species;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Manage Sizes')]
class ManageSizes extends Component
{
    public string $filterSpeciesId = '';

    public ?int $editingSizeId = null;

    public ?int $sizeSpeciesId = null;

    public string $sizeName = '';

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
     * @return Collection<int, Size>
     */
    #[Computed]
    public function sizes(): Collection
    {
        return Size::query()
            ->with('species')
            ->whereHas('species')
            ->when(
                $this->filterSpeciesId !== '',
                fn ($query) => $query->where('species_id', (int) $this->filterSpeciesId),
            )
            ->orderBy(Species::select('name')->whereColumn('species.id', 'sizes.species_id'))
            ->orderBy('name')
            ->get();
    }

    public function updatedFilterSpeciesId(): void
    {
        unset($this->sizes);
    }

    public function createSize(): void
    {
        $this->resetSizeForm();
        $this->sizeSpeciesId = $this->filterSpeciesId !== '' ? (int) $this->filterSpeciesId : null;
    }

    public function editSize(int $sizeId): void
    {
        $size = Size::query()->findOrFail($sizeId);

        $this->editingSizeId = $size->id;
        $this->sizeSpeciesId = $size->species_id;
        $this->sizeName = $size->name;
    }

    public function saveSize(): void
    {
        $validated = $this->validate([
            'sizeSpeciesId' => ['required', 'integer', 'exists:species,id'],
            'sizeName' => ['required', 'string', 'max:255'],
        ], [], [
            'sizeSpeciesId' => __('Species'),
            'sizeName' => __('Name'),
        ]);

        if ($this->editingSizeId !== null) {
            Size::query()->findOrFail($this->editingSizeId)->update([
                'species_id' => $validated['sizeSpeciesId'],
                'name' => $validated['sizeName'],
            ]);

            Flux::toast(variant: 'success', text: __('Record updated successfully'));
        } else {
            Size::query()->create([
                'species_id' => $validated['sizeSpeciesId'],
                'name' => $validated['sizeName'],
            ]);

            Flux::toast(variant: 'success', text: __('Record created successfully'));
        }

        $this->resetSizeForm();
        unset($this->sizes);

        Flux::modal('size-form')->close();
    }

    public function deleteSize(int $sizeId): void
    {
        Size::query()->findOrFail($sizeId)->delete();

        if ($this->editingSizeId === $sizeId) {
            $this->resetSizeForm();
        }

        unset($this->sizes);

        Flux::toast(variant: 'success', text: __('Record deleted successfully'));
    }

    protected function resetSizeForm(): void
    {
        $this->reset(['editingSizeId', 'sizeSpeciesId', 'sizeName']);
        $this->resetErrorBag();
    }

    public function render(): View
    {
        return view('livewire.admin.manage-sizes');
    }
}
