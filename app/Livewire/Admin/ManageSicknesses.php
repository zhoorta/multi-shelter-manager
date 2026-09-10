<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Models\Sickness;
use App\Models\Species;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Manage Sicknesses')]
class ManageSicknesses extends Component
{
    public ?int $editingSicknessId = null;

    public string $sicknessName = '';

    public string $sicknessDescription = '';

    /**
     * @var array<int, int>
     */
    public array $sicknessSpeciesIds = [];

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
     * @return Collection<int, Sickness>
     */
    #[Computed]
    public function sicknesses(): Collection
    {
        return Sickness::query()
            ->with('species')
            ->orderBy('name')
            ->get();
    }

    public function createSickness(): void
    {
        $this->resetSicknessForm();
    }

    public function editSickness(int $sicknessId): void
    {
        $sickness = Sickness::query()->with('species')->findOrFail($sicknessId);

        $this->editingSicknessId = $sickness->id;
        $this->sicknessName = $sickness->name;
        $this->sicknessDescription = (string) $sickness->description;
        $this->sicknessSpeciesIds = $sickness->species->pluck('id')->all();
    }

    public function saveSickness(): void
    {
        $validated = $this->validate([
            'sicknessName' => ['required', 'string', 'max:255'],
            'sicknessDescription' => ['nullable', 'string'],
            'sicknessSpeciesIds' => ['array'],
            'sicknessSpeciesIds.*' => ['integer', 'exists:species,id'],
        ], [], [
            'sicknessName' => __('Name'),
            'sicknessDescription' => __('Description'),
            'sicknessSpeciesIds' => __('Species'),
        ]);

        if ($this->editingSicknessId !== null) {
            $sickness = Sickness::query()->findOrFail($this->editingSicknessId);
            $sickness->update([
                'name' => $validated['sicknessName'],
                'description' => $validated['sicknessDescription'] !== '' ? $validated['sicknessDescription'] : null,
            ]);

            Flux::toast(variant: 'success', text: __('Record updated successfully'));
        } else {
            $sickness = Sickness::query()->create([
                'name' => $validated['sicknessName'],
                'description' => $validated['sicknessDescription'] !== '' ? $validated['sicknessDescription'] : null,
            ]);

            Flux::toast(variant: 'success', text: __('Record created successfully'));
        }

        $sickness->species()->sync($validated['sicknessSpeciesIds']);

        $this->resetSicknessForm();
        unset($this->sicknesses);

        Flux::modal('sickness-form')->close();
    }

    public function deleteSickness(int $sicknessId): void
    {
        Sickness::query()->findOrFail($sicknessId)->delete();

        if ($this->editingSicknessId === $sicknessId) {
            $this->resetSicknessForm();
        }

        unset($this->sicknesses);

        Flux::toast(variant: 'success', text: __('Record deleted successfully'));
    }

    protected function resetSicknessForm(): void
    {
        $this->reset(['editingSicknessId', 'sicknessName', 'sicknessDescription', 'sicknessSpeciesIds']);
        $this->resetErrorBag();
    }

    public function render(): View
    {
        return view('livewire.admin.manage-sicknesses');
    }
}
