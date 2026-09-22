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

    public string $administeredDate = '';

    public string $dueDate = '';

    public string $lotNumber = '';

    public string $veterinarianName = '';

    public string $vaccinationNotes = '';

    public function mount(Pet $pet, ?PetVaccine $petVaccine = null): void
    {
        abort_unless(! Auth::user()->is_admin, 403);
        abort_if($petVaccine !== null && $petVaccine->pet_id !== $pet->id, 404);

        $this->pet = $pet;
        $this->petVaccine = $petVaccine;

        if ($petVaccine === null) {
            return;
        }

        $this->vaccineId = (string) $petVaccine->vaccine_id;
        $this->administeredDate = (string) $petVaccine->administered_date?->toDateString();
        $this->dueDate = (string) $petVaccine->due_date?->toDateString();
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
            'administeredDate' => ['nullable', 'date'],
            'dueDate' => ['nullable', 'date'],
            'lotNumber' => ['nullable', 'string', 'max:255'],
            'veterinarianName' => ['nullable', 'string', 'max:255'],
            'vaccinationNotes' => ['nullable', 'string'],
        ], [], [
            'vaccineId' => __('Vaccine'),
            'administeredDate' => __('Administered Date'),
            'dueDate' => __('Due Date'),
            'lotNumber' => __('Lot Number'),
            'veterinarianName' => __('Veterinarian'),
            'vaccinationNotes' => __('Notes'),
        ]);

        if ($validated['administeredDate'] === '' && $validated['dueDate'] === '') {
            $this->addError('administeredDate', __('Either the administered date or the due date must be filled in.'));

            return;
        }

        $vaccineId = (int) $validated['vaccineId'];

        $vaccinationAttributes = [
            'administered_date' => $validated['administeredDate'] !== '' ? $validated['administeredDate'] : null,
            'due_date' => $validated['dueDate'] !== '' ? $validated['dueDate'] : null,
            'status' => $validated['administeredDate'] !== '' ? 'administered' : 'scheduled',
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
