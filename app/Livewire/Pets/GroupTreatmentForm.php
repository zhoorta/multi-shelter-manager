<?php

declare(strict_types=1);

namespace App\Livewire\Pets;

use App\Livewire\Pets\Concerns\FillsDueDateFromFrequency;
use App\Models\Facility;
use App\Models\Pet;
use App\Models\PetTreatment;
use App\Models\Treatment;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * Records one round of a preventive treatment (e.g. deworming every three
 * months) for many resident pets at once. Every resident of the treatment's
 * species in the chosen location starts ticked; staff untick exceptions.
 *
 * @property-read Collection<int, Treatment> $treatments
 * @property-read Treatment|null $selectedTreatment
 * @property-read Collection<int, Facility> $facilities
 * @property-read Collection<int, Pet> $candidatePets
 */
#[Title('Group Treatment')]
class GroupTreatmentForm extends Component
{
    use FillsDueDateFromFrequency;

    public string $treatmentId = '';

    public string $speciesFilter = '';

    public string $locationFilter = '';

    /**
     * @var array<int, string>
     */
    public array $selectedPetIds = [];

    public string $product = '';

    public string $veterinarianName = '';

    public string $treatmentNotes = '';

    public function mount(): void
    {
        abort_unless(! Auth::user()->is_admin, 403);
        abort_unless(Auth::user()->canEditArea('health'), 403);

        $this->administeredDate = today()->toDateString();
    }

    /**
     * Treatments that apply to at least one species of the shelter.
     *
     * @return Collection<int, Treatment>
     */
    #[Computed]
    public function treatments(): Collection
    {
        $shelterSpeciesIds = Auth::user()->currentShelter?->species()->pluck('species.id') ?? collect();

        return Treatment::query()
            ->with(['species' => fn ($query) => $query->whereKey($shelterSpeciesIds)->orderBy('name')])
            ->whereHas('species', fn (Builder $query) => $query->whereKey($shelterSpeciesIds))
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function selectedTreatment(): ?Treatment
    {
        return $this->treatments->firstWhere('id', (int) $this->treatmentId);
    }

    /**
     * Facilities and wings of the shelter, for the location filter.
     *
     * @return Collection<int, Facility>
     */
    #[Computed]
    public function facilities(): Collection
    {
        return Facility::query()
            ->with(['wings' => fn ($query) => $query->orderBy('name')])
            ->orderBy('name')
            ->get();
    }

    /**
     * Resident pets the selected treatment can be given to, narrowed by the
     * species and location filters. Empty until a treatment is chosen.
     *
     * @return Collection<int, Pet>
     */
    #[Computed]
    public function candidatePets(): Collection
    {
        if ($this->selectedTreatment === null) {
            return new Collection;
        }

        return $this->candidatePetsQuery()
            ->with(['species', 'cage.wing'])
            ->orderBy('name')
            ->get();
    }

    /**
     * @return Builder<Pet>
     */
    protected function candidatePetsQuery(): Builder
    {
        $speciesIds = $this->selectedTreatment?->species->pluck('id')->all() ?? [];

        return Pet::query()
            ->resident()
            ->whereIn('species_id', $speciesIds)
            ->when(
                $this->speciesFilter !== '',
                fn (Builder $query) => $query->where('species_id', (int) $this->speciesFilter),
            )
            ->when(
                $this->locationFilter !== '',
                function (Builder $query): void {
                    [$type, $id] = explode(':', $this->locationFilter, 2);

                    match ($type) {
                        'facility' => $query->whereHas('cage.wing', fn (Builder $query) => $query->where('facility_id', (int) $id)),
                        'wing' => $query->whereHas('cage', fn (Builder $query) => $query->where('wing_id', (int) $id)),
                        default => null,
                    };
                },
            );
    }

    public function updatedTreatmentId(): void
    {
        unset($this->selectedTreatment);

        if ($this->selectedTreatment?->species->doesntContain('id', (int) $this->speciesFilter)) {
            $this->speciesFilter = '';
        }

        $this->fillDueDateFromFrequency();
        $this->selectAllCandidates();
    }

    public function updatedSpeciesFilter(): void
    {
        $this->selectAllCandidates();
    }

    public function updatedLocationFilter(): void
    {
        $this->selectAllCandidates();
    }

    protected function selectedFrequencyMonths(): ?int
    {
        return $this->selectedTreatment?->frequency_months;
    }

    /**
     * Tick every pet currently listed.
     */
    public function selectAllCandidates(): void
    {
        unset($this->candidatePets);

        $this->selectedPetIds = $this->candidatePets->map(fn (Pet $pet): string => (string) $pet->id)->all();
    }

    public function deselectAllCandidates(): void
    {
        $this->selectedPetIds = [];
    }

    public function saveGroupTreatment(): void
    {
        $validated = $this->validate([
            'treatmentId' => ['required', 'integer'],
            'administeredDate' => ['required', 'date'],
            'dueDate' => ['nullable', 'date', 'after:administeredDate'],
            'selectedPetIds' => ['required', 'array', 'min:1'],
            'selectedPetIds.*' => ['integer'],
            'product' => ['nullable', 'string', 'max:255'],
            'veterinarianName' => ['nullable', 'string', 'max:255'],
            'treatmentNotes' => ['nullable', 'string'],
        ], [
            'selectedPetIds.required' => __('Select at least one animal.'),
        ], [
            'treatmentId' => __('Treatment'),
            'administeredDate' => __('Administered Date'),
            'dueDate' => __('Next Due Date'),
            'selectedPetIds' => __('Animals'),
            'product' => __('Product'),
            'veterinarianName' => __('Veterinarian'),
            'treatmentNotes' => __('Notes'),
        ]);

        abort_if($this->selectedTreatment === null, 404);

        $treatmentId = $this->selectedTreatment->id;

        // Re-query through the candidate scope so a tampered id can't reach a
        // pet of another shelter, species or a non-resident one.
        $petIds = $this->candidatePetsQuery()
            ->whereKey(array_map('intval', $validated['selectedPetIds']))
            ->pluck('id');

        $treatmentAttributes = [
            'administered_date' => $validated['administeredDate'],
            'due_date' => $validated['dueDate'] !== '' ? $validated['dueDate'] : null,
            'status' => 'administered',
            'product' => $validated['product'] !== '' ? $validated['product'] : null,
            'veterinarian_name' => $validated['veterinarianName'] !== '' ? $validated['veterinarianName'] : null,
            'notes' => $validated['treatmentNotes'] !== '' ? $validated['treatmentNotes'] : null,
        ];

        DB::transaction(function () use ($petIds, $treatmentId, $treatmentAttributes): void {
            foreach ($petIds as $petId) {
                $scheduledTreatment = PetTreatment::scheduledRecordFulfilledBy($petId, $treatmentId, $treatmentAttributes['administered_date']);

                if ($scheduledTreatment !== null) {
                    $scheduledTreatment->update($treatmentAttributes);

                    continue;
                }

                PetTreatment::query()->create([...$treatmentAttributes, 'pet_id' => $petId, 'treatment_id' => $treatmentId]);
            }
        });

        Flux::toast(
            variant: 'success',
            text: trans_choice('Treatment recorded for :count animal.|Treatment recorded for :count animals.', $petIds->count(), ['count' => $petIds->count()]),
        );

        $this->redirect(route('pets.treatments.index'), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.pets.group-treatment-form');
    }
}
