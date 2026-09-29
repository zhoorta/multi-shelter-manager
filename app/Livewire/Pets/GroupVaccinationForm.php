<?php

declare(strict_types=1);

namespace App\Livewire\Pets;

use App\Livewire\Pets\Concerns\FillsDueDateFromFrequency;
use App\Models\Facility;
use App\Models\Pet;
use App\Models\PetVaccine;
use App\Models\Vaccine;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

/**
 * Records the same vaccine for many resident pets at once, e.g. on the day
 * the vet vaccinates a group. The "due" filter lists the pets whose
 * vaccination of that vaccine falls due in one month (the same pets as the
 * vaccination plan's cell, which links here that way), the overdue ones,
 * or every resident of the vaccine's species. Listed pets start ticked;
 * staff untick exceptions.
 *
 * @property-read Collection<int, Vaccine> $vaccines
 * @property-read Vaccine|null $selectedVaccine
 * @property-read Collection<int, Facility> $facilities
 * @property-read Collection<int, Pet> $candidatePets
 * @property-read array<int, string> $pendingDueDates
 * @property-read array<string, string> $dueMonthOptions
 */
#[Title('Group Vaccination')]
class GroupVaccinationForm extends Component
{
    use FillsDueDateFromFrequency;

    #[Url(as: 'vaccine')]
    public string $vaccineId = '';

    /**
     * "Y-m" month the pets' vaccination falls due in, "overdue", or "all"
     * for every resident of the vaccine's species. Defaults to the current
     * month.
     */
    #[Url(as: 'due')]
    public string $dueMonth = '';

    public string $speciesFilter = '';

    public string $locationFilter = '';

    /**
     * @var array<int, string>
     */
    public array $selectedPetIds = [];

    public string $lotNumber = '';

    public string $veterinarianName = '';

    public string $vaccinationNotes = '';

    public function mount(): void
    {
        abort_unless(! Auth::user()->is_admin, 403);
        abort_unless(Auth::user()->canEditCurrentShelter(), 403);

        $this->administeredDate = today()->toDateString();

        if (! $this->isValidDueMonth()) {
            $this->dueMonth = today()->format('Y-m');
        }

        if ($this->selectedVaccine === null) {
            $this->vaccineId = '';

            return;
        }

        $this->fillDueDateFromFrequency();
        $this->selectAllCandidates();
    }

    /**
     * Vaccines that apply to at least one species of the shelter.
     *
     * @return Collection<int, Vaccine>
     */
    #[Computed]
    public function vaccines(): Collection
    {
        $shelterSpeciesIds = Auth::user()->currentShelter?->species()->pluck('species.id') ?? collect();

        return Vaccine::query()
            ->with(['species' => fn ($query) => $query->whereKey($shelterSpeciesIds)->orderBy('name')])
            ->whereHas('species', fn (Builder $query) => $query->whereKey($shelterSpeciesIds))
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function selectedVaccine(): ?Vaccine
    {
        return $this->vaccines->firstWhere('id', (int) $this->vaccineId);
    }

    /**
     * Choices for the "due" filter: overdue, due in this or one of the next
     * eleven months (plus the month the page was opened with when it lies
     * further ahead), or every resident.
     *
     * @return array<string, string>
     */
    #[Computed]
    public function dueMonthOptions(): array
    {
        $options = ['overdue' => __('Overdue')];
        $months = collect(range(0, 11))->map(fn (int $monthsAhead): CarbonInterface => today()->startOfMonth()->addMonths($monthsAhead));

        if (Carbon::hasFormat($this->dueMonth, 'Y-m') && $months->doesntContain(fn (CarbonInterface $month): bool => $month->format('Y-m') === $this->dueMonth)) {
            $months->push(CarbonImmutable::parse($this->dueMonth.'-01'));
        }

        foreach ($months->sort() as $month) {
            $options[$month->format('Y-m')] = __('Due in :month', ['month' => $month->translatedFormat('F Y')]);
        }

        return [...$options, 'all' => __('All resident animals')];
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
     * Resident pets the selected vaccine can be given to, narrowed by the
     * "due by" month and the species and location filters. Empty until a
     * vaccine is chosen.
     *
     * @return Collection<int, Pet>
     */
    #[Computed]
    public function candidatePets(): Collection
    {
        if ($this->selectedVaccine === null) {
            return new Collection;
        }

        return $this->candidatePetsQuery()
            ->with(['species', 'cage.wing'])
            ->orderBy('name')
            ->get();
    }

    /**
     * Earliest open due date of the selected vaccine per listed pet id, shown
     * next to each pet.
     *
     * @return array<int, string>
     */
    #[Computed]
    public function pendingDueDates(): array
    {
        return $this->pendingVaccinationsQuery()
            ->whereIn('pet_id', $this->candidatePets->modelKeys())
            ->orderBy('due_date')
            ->get(['pet_id', 'due_date'])
            ->unique('pet_id')
            ->mapWithKeys(fn (PetVaccine $petVaccine): array => [$petVaccine->pet_id => $petVaccine->due_date->format('d/m/Y')])
            ->all();
    }

    /**
     * @return Builder<Pet>
     */
    protected function candidatePetsQuery(): Builder
    {
        $speciesIds = $this->selectedVaccine?->species->pluck('id')->all() ?? [];

        return Pet::query()
            ->resident()
            ->whereIn('species_id', $speciesIds)
            ->when(
                $this->dueMonth !== 'all',
                fn (Builder $query) => $query->whereIn('id', $this->dueMonth === 'overdue'
                    ? $this->pendingVaccinationsQuery()->whereDate('due_date', '<', today()->toDateString())->select('pet_id')
                    : $this->pendingVaccinationsQuery()
                        ->whereDate('due_date', '>=', Carbon::createFromFormat('!Y-m', $this->dueMonth)->startOfMonth()->toDateString())
                        ->whereDate('due_date', '<=', Carbon::createFromFormat('!Y-m', $this->dueMonth)->endOfMonth()->toDateString())
                        ->select('pet_id')),
            )
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

    /**
     * @return Builder<PetVaccine>
     */
    protected function pendingVaccinationsQuery(): Builder
    {
        return PetVaccine::query()
            ->pending()
            ->where('vaccine_id', (int) $this->vaccineId);
    }

    protected function isValidDueMonth(): bool
    {
        return in_array($this->dueMonth, ['all', 'overdue'], true) || Carbon::hasFormat($this->dueMonth, 'Y-m');
    }

    public function updatedVaccineId(): void
    {
        unset($this->selectedVaccine);

        if ($this->selectedVaccine?->species->doesntContain('id', (int) $this->speciesFilter)) {
            $this->speciesFilter = '';
        }

        $this->fillDueDateFromFrequency();
        $this->selectAllCandidates();
    }

    public function updatedDueMonth(): void
    {
        if (! $this->isValidDueMonth()) {
            $this->dueMonth = today()->format('Y-m');
        }

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
        return $this->selectedVaccine?->frequency_months;
    }

    /**
     * Tick every pet currently listed.
     */
    public function selectAllCandidates(): void
    {
        unset($this->candidatePets, $this->pendingDueDates);

        $this->selectedPetIds = $this->candidatePets->map(fn (Pet $pet): string => (string) $pet->id)->all();
    }

    public function deselectAllCandidates(): void
    {
        $this->selectedPetIds = [];
    }

    public function saveGroupVaccination(): void
    {
        $validated = $this->validate([
            'vaccineId' => ['required', 'integer'],
            'administeredDate' => ['required', 'date'],
            'dueDate' => ['nullable', 'date', 'after:administeredDate'],
            'selectedPetIds' => ['required', 'array', 'min:1'],
            'selectedPetIds.*' => ['integer'],
            'lotNumber' => ['nullable', 'string', 'max:255'],
            'veterinarianName' => ['nullable', 'string', 'max:255'],
            'vaccinationNotes' => ['nullable', 'string'],
        ], [
            'selectedPetIds.required' => __('Select at least one animal.'),
        ], [
            'vaccineId' => __('Vaccine'),
            'administeredDate' => __('Administered Date'),
            'dueDate' => __('Next Due Date'),
            'selectedPetIds' => __('Animals'),
            'lotNumber' => __('Lot Number'),
            'veterinarianName' => __('Veterinarian'),
            'vaccinationNotes' => __('Notes'),
        ]);

        abort_if($this->selectedVaccine === null, 404);

        $vaccineId = $this->selectedVaccine->id;

        // Re-query through the candidate scope so a tampered id can't reach a
        // pet of another shelter, species or a non-resident one.
        $petIds = $this->candidatePetsQuery()
            ->whereKey(array_map('intval', $validated['selectedPetIds']))
            ->pluck('id');

        $vaccinationAttributes = [
            'administered_date' => $validated['administeredDate'],
            'due_date' => $validated['dueDate'] !== '' ? $validated['dueDate'] : null,
            'status' => 'administered',
            'lot_number' => $validated['lotNumber'] !== '' ? $validated['lotNumber'] : null,
            'veterinarian_name' => $validated['veterinarianName'] !== '' ? $validated['veterinarianName'] : null,
            'notes' => $validated['vaccinationNotes'] !== '' ? $validated['vaccinationNotes'] : null,
        ];

        DB::transaction(function () use ($petIds, $vaccineId, $vaccinationAttributes): void {
            foreach ($petIds as $petId) {
                $scheduledVaccination = PetVaccine::scheduledRecordFulfilledBy($petId, $vaccineId, $vaccinationAttributes['administered_date']);

                if ($scheduledVaccination !== null) {
                    $scheduledVaccination->update($vaccinationAttributes);

                    continue;
                }

                (new PetVaccine([...$vaccinationAttributes, 'pet_id' => $petId, 'vaccine_id' => $vaccineId]))->save();
            }
        });

        Flux::toast(
            variant: 'success',
            text: trans_choice('Vaccination recorded for :count animal.|Vaccination recorded for :count animals.', $petIds->count(), ['count' => $petIds->count()]),
        );

        $this->redirect(route('pets.vaccinations.index'), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.pets.group-vaccination-form');
    }
}
