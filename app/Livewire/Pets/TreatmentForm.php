<?php

declare(strict_types=1);

namespace App\Livewire\Pets;

use App\Livewire\Pets\Concerns\FillsDueDateFromFrequency;
use App\Models\Pet;
use App\Models\PetTreatment;
use App\Models\Treatment;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Component;

/**
 * Records a preventive treatment (deworming, antiparasitic) for one pet.
 * Mirrors VaccinationForm; GroupTreatmentForm records one for many pets.
 *
 * @property-read Collection<int, Treatment> $treatments
 * @property-read Treatment|null $selectedTreatment
 */
class TreatmentForm extends Component
{
    use FillsDueDateFromFrequency;

    public Pet $pet;

    public ?PetTreatment $petTreatment = null;

    public string $treatmentId = '';

    public string $product = '';

    public string $veterinarianName = '';

    public string $treatmentNotes = '';

    public function mount(Pet $pet, ?PetTreatment $petTreatment = null): void
    {
        abort_unless(! Auth::user()->is_admin, 403);
        abort_unless(Auth::user()->canEditArea('health'), 403);
        abort_if($petTreatment !== null && $petTreatment->pet_id !== $pet->id, 404);

        $this->pet = $pet;
        $this->petTreatment = $petTreatment;

        if ($petTreatment === null) {
            return;
        }

        $this->treatmentId = (string) $petTreatment->treatment_id;
        $this->administeredDate = (string) $petTreatment->administered_date?->toDateString();
        $this->dueDate = (string) $petTreatment->due_date?->toDateString();
        $this->product = (string) $petTreatment->product;
        $this->veterinarianName = (string) $petTreatment->veterinarian_name;
        $this->treatmentNotes = (string) $petTreatment->notes;
    }

    /**
     * Treatments that can be given to the pet's species (see the
     * treatment_species pivot).
     *
     * @return Collection<int, Treatment>
     */
    #[Computed]
    public function treatments(): Collection
    {
        return Treatment::query()
            ->whereHas('species', fn (Builder $query) => $query->whereKey($this->pet->species_id))
            ->orderBy('name')
            ->get();
    }

    /**
     * The treatment currently selected in the form, if any.
     */
    #[Computed]
    public function selectedTreatment(): ?Treatment
    {
        return $this->treatments->firstWhere('id', (int) $this->treatmentId);
    }

    public function updatedTreatmentId(): void
    {
        unset($this->selectedTreatment);

        $this->fillDueDateFromFrequency();
    }

    protected function selectedFrequencyMonths(): ?int
    {
        return $this->selectedTreatment?->frequency_months;
    }

    public function saveTreatment(): void
    {
        $validated = $this->validate([
            'treatmentId' => [
                'required',
                'integer',
                Rule::exists('treatment_species', 'treatment_id')->where('species_id', $this->pet->species_id),
            ],
            'administeredDate' => ['nullable', 'date'],
            'dueDate' => ['nullable', 'date'],
            'product' => ['nullable', 'string', 'max:255'],
            'veterinarianName' => ['nullable', 'string', 'max:255'],
            'treatmentNotes' => ['nullable', 'string'],
        ], [], [
            'treatmentId' => __('Treatment'),
            'administeredDate' => __('Administered Date'),
            'dueDate' => __('Next Due Date'),
            'product' => __('Product'),
            'veterinarianName' => __('Veterinarian'),
            'treatmentNotes' => __('Notes'),
        ]);

        if ($validated['administeredDate'] === '' && $validated['dueDate'] === '') {
            $this->addError('administeredDate', __('Either the administered date or the due date must be filled in.'));

            return;
        }

        $treatmentId = (int) $validated['treatmentId'];

        $treatmentAttributes = [
            'administered_date' => $validated['administeredDate'] !== '' ? $validated['administeredDate'] : null,
            'due_date' => $validated['dueDate'] !== '' ? $validated['dueDate'] : null,
            'status' => $validated['administeredDate'] !== '' ? 'administered' : 'scheduled',
            'product' => $validated['product'] !== '' ? $validated['product'] : null,
            'veterinarian_name' => $validated['veterinarianName'] !== '' ? $validated['veterinarianName'] : null,
            'notes' => $validated['treatmentNotes'] !== '' ? $validated['treatmentNotes'] : null,
        ];

        $isEditing = $this->petTreatment !== null;

        $scheduledTreatment = $isEditing || $treatmentAttributes['administered_date'] === null
            ? null
            : PetTreatment::scheduledRecordFulfilledBy($this->pet->id, $treatmentId, $treatmentAttributes['administered_date']);

        if ($isEditing) {
            $this->petTreatment->update([...$treatmentAttributes, 'treatment_id' => $treatmentId]);
        } elseif ($scheduledTreatment !== null) {
            $scheduledTreatment->update($treatmentAttributes);
        } else {
            $this->pet->treatments()->create([...$treatmentAttributes, 'treatment_id' => $treatmentId]);
        }

        Flux::toast(
            variant: 'success',
            text: $isEditing ? __('Record updated successfully') : __('Record created successfully'),
        );

        $this->redirect(route('pets.show', $this->pet), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.pets.treatment-form')->title(
            ($this->petTreatment !== null ? __('Edit Treatment') : __('New Treatment')).' — '.$this->pet->name,
        );
    }
}
