<?php

declare(strict_types=1);

namespace App\Livewire\Pets;

use App\Models\Pet;
use App\Models\PetVaccine;
use App\Models\Vaccine;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Component;

class VaccinationForm extends Component
{
    public Pet $pet;

    public ?PetVaccine $petVaccine = null;

    public string $vaccineId = '';

    public string $administeredAt = '';

    public string $nextDueAt = '';

    public string $lotNumber = '';

    public string $veterinarianName = '';

    public string $vaccinationNotes = '';

    public function mount(Pet $pet, ?PetVaccine $petVaccine = null): void
    {
        abort_unless(in_array(Auth::user()->role, ['manager', 'staff'], true), 403);
        abort_if($petVaccine !== null && $petVaccine->pet_id !== $pet->id, 404);

        $this->pet = $pet;
        $this->petVaccine = $petVaccine;

        if ($petVaccine === null) {
            $this->administeredAt = now()->toDateString();

            return;
        }

        $this->vaccineId = (string) $petVaccine->vaccine_id;
        $this->administeredAt = $petVaccine->administered_at->toDateString();
        $this->nextDueAt = (string) $petVaccine->next_due_at?->toDateString();
        $this->lotNumber = (string) $petVaccine->lot_number;
        $this->veterinarianName = (string) $petVaccine->veterinarian_name;
        $this->vaccinationNotes = (string) $petVaccine->notes;
    }

    /**
     * Vaccines that can be administered to the pet's species (see
     * vaccine_species pivot) — mirrors PetShow::sicknesses().
     *
     * @return Collection<int, Vaccine>
     */
    #[Computed]
    public function vaccines(): Collection
    {
        return Vaccine::query()
            ->whereHas('species', fn (Builder $query) => $query->whereKey($this->pet->species_id))
            ->orderBy('name')
            ->get();
    }

    public function saveVaccination(): void
    {
        $validated = $this->validate([
            'vaccineId' => [
                'required',
                'integer',
                Rule::exists('vaccine_species', 'vaccine_id')->where('species_id', $this->pet->species_id),
            ],
            'administeredAt' => ['required', 'date'],
            'nextDueAt' => ['nullable', 'date', 'after_or_equal:administeredAt'],
            'lotNumber' => ['nullable', 'string', 'max:255'],
            'veterinarianName' => ['nullable', 'string', 'max:255'],
            'vaccinationNotes' => ['nullable', 'string'],
        ], [], [
            'vaccineId' => __('Vaccine'),
            'administeredAt' => __('Administered Date'),
            'nextDueAt' => __('Next Due Date'),
            'lotNumber' => __('Lot Number'),
            'veterinarianName' => __('Veterinarian'),
            'vaccinationNotes' => __('Notes'),
        ]);

        $vaccineId = (int) $validated['vaccineId'];

        $vaccinationAttributes = [
            'administered_at' => $validated['administeredAt'],
            'next_due_at' => $validated['nextDueAt'] !== '' ? $validated['nextDueAt'] : null,
            'lot_number' => $validated['lotNumber'] !== '' ? $validated['lotNumber'] : null,
            'veterinarian_name' => $validated['veterinarianName'] !== '' ? $validated['veterinarianName'] : null,
            'notes' => $validated['vaccinationNotes'] !== '' ? $validated['vaccinationNotes'] : null,
        ];

        $isEditing = $this->petVaccine !== null;

        if ($isEditing) {
            $this->petVaccine->update([...$vaccinationAttributes, 'vaccine_id' => $vaccineId]);
        } else {
            $this->pet->vaccines()->attach($vaccineId, $vaccinationAttributes);
        }

        Flux::toast(
            variant: 'success',
            text: $isEditing ? __('Record updated successfully') : __('Record created successfully'),
        );

        $this->redirect(route('pets.show', $this->pet), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.pets.vaccination-form')->title(
            ($this->petVaccine !== null ? __('Edit Vaccination') : __('New Vaccination')).' — '.$this->pet->name,
        );
    }
}
