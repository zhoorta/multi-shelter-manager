<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Models\FurType;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Manage Fur Types')]
class ManageFurTypes extends Component
{
    public ?int $editingFurTypeId = null;

    public string $furTypeName = '';

    public function mount(): void
    {
        abort_unless(Auth::user()->role === 'admin', 403);
    }

    /**
     * @return Collection<int, FurType>
     */
    #[Computed]
    public function furTypes(): Collection
    {
        return FurType::query()
            ->orderBy('name')
            ->get();
    }

    public function createFurType(): void
    {
        $this->resetFurTypeForm();
    }

    public function editFurType(int $furTypeId): void
    {
        $furType = FurType::query()->findOrFail($furTypeId);

        $this->editingFurTypeId = $furType->id;
        $this->furTypeName = $furType->name;
    }

    public function saveFurType(): void
    {
        $validated = $this->validate([
            'furTypeName' => [
                'required',
                'string',
                'max:255',
                Rule::unique('fur_types', 'name')->ignore($this->editingFurTypeId),
            ],
        ], [], [
            'furTypeName' => __('Name'),
        ]);

        if ($this->editingFurTypeId !== null) {
            FurType::query()->findOrFail($this->editingFurTypeId)->update([
                'name' => $validated['furTypeName'],
            ]);

            Flux::toast(variant: 'success', text: __('Record updated successfully'));
        } else {
            FurType::query()->create([
                'name' => $validated['furTypeName'],
            ]);

            Flux::toast(variant: 'success', text: __('Record created successfully'));
        }

        $this->resetFurTypeForm();
        unset($this->furTypes);

        Flux::modal('fur-type-form')->close();
    }

    public function deleteFurType(int $furTypeId): void
    {
        FurType::query()->findOrFail($furTypeId)->delete();

        if ($this->editingFurTypeId === $furTypeId) {
            $this->resetFurTypeForm();
        }

        unset($this->furTypes);

        Flux::toast(variant: 'success', text: __('Record deleted successfully'));
    }

    protected function resetFurTypeForm(): void
    {
        $this->reset(['editingFurTypeId', 'furTypeName']);
        $this->resetErrorBag();
    }

    public function render(): View
    {
        return view('livewire.admin.manage-fur-types');
    }
}
