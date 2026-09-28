<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Models\Species;
use App\Models\Treatment;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Manage Treatments')]
class ManageTreatments extends Component
{
    public ?int $editingTreatmentId = null;

    public string $treatmentName = '';

    public string $treatmentFrequencyMonths = '';

    /**
     * @var array<int, int>
     */
    public array $treatmentSpeciesIds = [];

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
     * @return Collection<int, Treatment>
     */
    #[Computed]
    public function treatments(): Collection
    {
        return Treatment::query()
            ->with('species')
            ->orderBy('name')
            ->get();
    }

    public function createTreatment(): void
    {
        $this->resetTreatmentForm();
    }

    public function editTreatment(int $treatmentId): void
    {
        $treatment = Treatment::query()->with('species')->findOrFail($treatmentId);

        $this->editingTreatmentId = $treatment->id;
        $this->treatmentName = $treatment->name;
        $this->treatmentFrequencyMonths = (string) $treatment->frequency_months;
        $this->treatmentSpeciesIds = $treatment->species->pluck('id')->all();
    }

    public function saveTreatment(): void
    {
        $validated = $this->validate([
            'treatmentName' => ['required', 'string', 'max:255'],
            'treatmentFrequencyMonths' => ['nullable', 'integer', 'min:1', 'max:120'],
            'treatmentSpeciesIds' => ['array'],
            'treatmentSpeciesIds.*' => ['integer', 'exists:species,id'],
        ], [], [
            'treatmentName' => __('Name'),
            'treatmentFrequencyMonths' => __('Frequency (months)'),
            'treatmentSpeciesIds' => __('Species'),
        ]);

        $treatmentAttributes = [
            'name' => $validated['treatmentName'],
            'frequency_months' => $validated['treatmentFrequencyMonths'] !== '' ? (int) $validated['treatmentFrequencyMonths'] : null,
        ];

        if ($this->editingTreatmentId !== null) {
            $treatment = Treatment::query()->findOrFail($this->editingTreatmentId);
            $treatment->update($treatmentAttributes);

            Flux::toast(variant: 'success', text: __('Record updated successfully'));
        } else {
            $treatment = Treatment::query()->create($treatmentAttributes);

            Flux::toast(variant: 'success', text: __('Record created successfully'));
        }

        $treatment->species()->sync($validated['treatmentSpeciesIds']);

        $this->resetTreatmentForm();
        unset($this->treatments);

        Flux::modal('treatment-form')->close();
    }

    public function deleteTreatment(int $treatmentId): void
    {
        Treatment::query()->findOrFail($treatmentId)->delete();

        if ($this->editingTreatmentId === $treatmentId) {
            $this->resetTreatmentForm();
        }

        unset($this->treatments);

        Flux::toast(variant: 'success', text: __('Record deleted successfully'));
    }

    protected function resetTreatmentForm(): void
    {
        $this->reset(['editingTreatmentId', 'treatmentName', 'treatmentFrequencyMonths', 'treatmentSpeciesIds']);
        $this->resetErrorBag();
    }

    public function render(): View
    {
        return view('livewire.admin.manage-treatments');
    }
}
