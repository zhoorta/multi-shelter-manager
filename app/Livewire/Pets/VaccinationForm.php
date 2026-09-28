<?php

declare(strict_types=1);

namespace App\Livewire\Pets;

use App\Models\Pet;
use App\Models\PetVaccine;
use App\Models\Vaccine;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Component;

/**
 * @property-read Collection<int, Vaccine> $vaccines
 * @property-read Vaccine|null $selectedVaccine
 */
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

    /**
     * The due date last filled in from the vaccine frequency, so it can be
     * recalculated while the user hasn't typed a date of their own.
     */
    #[Locked]
    public string $autoFilledDueDate = '';

    public function mount(Pet $pet, ?PetVaccine $petVaccine = null): void
    {
        abort_unless(! Auth::user()->is_admin, 403);
        abort_unless(Auth::user()->canEditCurrentShelter(), 403);
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

    /**
     * The vaccine currently selected in the form, if any.
     */
    #[Computed]
    public function selectedVaccine(): ?Vaccine
    {
        return $this->vaccines->firstWhere('id', (int) $this->vaccineId);
    }

    public function updatedVaccineId(): void
    {
        unset($this->selectedVaccine);

        $this->fillDueDateFromFrequency();
    }

    public function updatedAdministeredDate(): void
    {
        $this->fillDueDateFromFrequency();
    }

    /**
     * Fill the next due date from the administered date plus the vaccine
     * frequency (e.g. rabies every 36 months). A date the user typed
     * themselves is never overwritten; an auto-filled one is recalculated,
     * or cleared when there's no longer a frequency or dose date to use.
     */
    protected function fillDueDateFromFrequency(): void
    {
        if ($this->dueDate !== '' && $this->dueDate !== $this->autoFilledDueDate) {
            return;
        }

        $frequencyMonths = $this->selectedVaccine?->frequency_months;

        $this->dueDate = $frequencyMonths !== null && Carbon::hasFormat($this->administeredDate, 'Y-m-d')
            ? Carbon::createFromFormat('Y-m-d', $this->administeredDate)->addMonthsNoOverflow($frequencyMonths)->toDateString()
            : '';

        $this->autoFilledDueDate = $this->dueDate;
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
            'dueDate' => __('Next Due Date'),
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

        $scheduledVaccination = $isEditing || $vaccinationAttributes['administered_date'] === null
            ? null
            : $this->scheduledVaccinationFulfilledBy($vaccineId, $vaccinationAttributes['administered_date']);

        if ($isEditing) {
            $this->petVaccine->update([...$vaccinationAttributes, 'vaccine_id' => $vaccineId]);
        } elseif ($scheduledVaccination !== null) {
            $scheduledVaccination->update($vaccinationAttributes);
        } else {
            $this->pet->vaccines()->attach($vaccineId, $vaccinationAttributes);
        }

        Flux::toast(
            variant: 'success',
            text: $isEditing ? __('Record updated successfully') : __('Record created successfully'),
        );

        $this->redirect(route('pets.show', $this->pet), navigate: true);
    }

    /**
     * The open scheduled vaccination (earliest due first) that a newly
     * logged dose fulfils, so the dose closes it instead of leaving it
     * pending next to the new row. A dose older than one already logged for
     * this vaccine is history being backfilled and fulfils nothing.
     */
    protected function scheduledVaccinationFulfilledBy(int $vaccineId, string $administeredDate): ?PetVaccine
    {
        $isBackfilledDose = PetVaccine::query()
            ->where('pet_id', $this->pet->id)
            ->where('vaccine_id', $vaccineId)
            ->where('administered_date', '>', $administeredDate)
            ->exists();

        if ($isBackfilledDose) {
            return null;
        }

        return PetVaccine::query()
            ->where('pet_id', $this->pet->id)
            ->where('vaccine_id', $vaccineId)
            ->where('status', 'scheduled')
            ->orderBy('due_date')
            ->first();
    }

    public function render(): View
    {
        return view('livewire.pets.vaccination-form')->title(
            ($this->petVaccine !== null ? __('Edit Vaccination') : __('New Vaccination')).' — '.$this->pet->name,
        );
    }
}
