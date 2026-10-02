<?php

declare(strict_types=1);

namespace App\Livewire\Pets;

use App\Models\Pet;
use App\Models\PetSickness;
use App\Models\Sickness;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Component;

class DiagnosisForm extends Component
{
    public Pet $pet;

    public ?PetSickness $petSickness = null;

    public string $sicknessId = '';

    public string $diagnosedAt = '';

    public string $status = 'active';

    public string $resolvedAt = '';

    public string $treatmentNotes = '';

    public function mount(Pet $pet, ?PetSickness $petSickness = null): void
    {
        abort_unless(! Auth::user()->is_admin, 403);
        abort_unless(Auth::user()->canEditArea('health'), 403);
        abort_if($petSickness !== null && $petSickness->pet_id !== $pet->id, 404);

        $this->pet = $pet;
        $this->petSickness = $petSickness;

        if ($petSickness === null) {
            $this->diagnosedAt = now()->toDateString();

            return;
        }

        $this->sicknessId = (string) $petSickness->sickness_id;
        $this->diagnosedAt = $petSickness->diagnosed_at->toDateString();
        $this->status = $petSickness->status;
        $this->resolvedAt = (string) $petSickness->resolved_at?->toDateString();
        $this->treatmentNotes = (string) $petSickness->treatment_notes;
    }

    /**
     * Sicknesses that can affect the pet's species (see sickness_species
     * pivot) — mirrors VaccinationForm::vaccines().
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

    /**
     * Marking a diagnosis as treated suggests today as the resolution date;
     * any other status has no resolution date.
     */
    public function updatedStatus(): void
    {
        if ($this->status !== 'treated') {
            $this->resolvedAt = '';

            return;
        }

        if ($this->resolvedAt === '') {
            $this->resolvedAt = now()->toDateString();
        }
    }

    public function saveDiagnosis(): void
    {
        $validated = $this->validate([
            'sicknessId' => [
                'required',
                'integer',
                Rule::exists('sickness_species', 'sickness_id')->where('species_id', $this->pet->species_id),
            ],
            'diagnosedAt' => ['required', 'date'],
            'status' => ['required', Rule::in(PetSickness::STATUSES)],
            'resolvedAt' => ['nullable', 'date', 'after_or_equal:diagnosedAt'],
            'treatmentNotes' => ['nullable', 'string'],
        ], [], [
            'sicknessId' => __('Sickness'),
            'diagnosedAt' => __('Diagnosis Date'),
            'status' => __('Status'),
            'resolvedAt' => __('Resolution Date'),
            'treatmentNotes' => __('Treatment Notes'),
        ]);

        $isTreated = $validated['status'] === 'treated';

        $diagnosisAttributes = [
            'diagnosed_at' => $validated['diagnosedAt'],
            'status' => $validated['status'],
            'resolved_at' => $isTreated && $validated['resolvedAt'] !== '' ? $validated['resolvedAt'] : null,
            'treatment_notes' => $validated['treatmentNotes'] !== '' ? $validated['treatmentNotes'] : null,
        ];

        $sicknessId = (int) $validated['sicknessId'];
        $isEditing = $this->petSickness !== null;

        if ($isEditing) {
            $this->petSickness->update([...$diagnosisAttributes, 'sickness_id' => $sicknessId]);
        } else {
            $this->pet->sicknesses()->attach($sicknessId, $diagnosisAttributes);
        }

        Flux::toast(
            variant: 'success',
            text: $isEditing ? __('Record updated successfully') : __('Record created successfully'),
        );

        $this->redirect(route('pets.show', $this->pet), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.pets.diagnosis-form')->title(
            ($this->petSickness !== null ? __('Edit Diagnosis') : __('New Diagnosis')).' — '.$this->pet->name,
        );
    }
}
