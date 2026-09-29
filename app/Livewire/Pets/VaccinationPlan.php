<?php

declare(strict_types=1);

namespace App\Livewire\Pets;

use App\Livewire\Pets\Concerns\ListsDueVaccinations;
use App\Models\Pet;
use App\Models\PetVaccine;
use App\Models\Vaccine;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

/**
 * Yearly vaccination plan: how many open vaccinations of the shelter's
 * residents fall due in each month, per vaccine, so a shelter that
 * vaccinates in groups can pick the month and hand the vet a list.
 *
 * @property-read array<int, array<int, int>> $counts
 * @property-read EloquentCollection<int, Vaccine> $vaccines
 * @property-read array<int, int> $yearOptions
 * @property-read Collection<int, array{pet: Pet, vaccinations: EloquentCollection<int, PetVaccine>}> $duePets
 */
#[Title('Vaccination Plan')]
class VaccinationPlan extends Component
{
    use ListsDueVaccinations;

    #[Url]
    public ?int $year = null;

    /**
     * Selected month (1-12, 0 = before the year), or null for none.
     */
    #[Url]
    public ?int $month = null;

    #[Url]
    public ?int $vaccine = null;

    public function mount(): void
    {
        abort_unless(! Auth::user()->is_admin, 403);

        $this->year ??= today()->year;
    }

    /**
     * Open vaccinations per vaccine id and month (0 = before the year),
     * counted in PHP since a shelter only has a few hundred of them.
     *
     * @return array<int, array<int, int>>
     */
    #[Computed]
    public function counts(): array
    {
        $yearStart = today()->setDate($this->year, 1, 1);
        $counts = [];

        $this->dueVaccinationsQuery($this->year + 1, 0)
            ->get(['id', 'pet_id', 'vaccine_id', 'due_date'])
            ->each(function (PetVaccine $petVaccine) use ($yearStart, &$counts): void {
                $month = $petVaccine->due_date->lt($yearStart) ? 0 : $petVaccine->due_date->month;
                $counts[$petVaccine->vaccine_id][$month] = ($counts[$petVaccine->vaccine_id][$month] ?? 0) + 1;
            });

        return $counts;
    }

    /**
     * Vaccines with at least one open vaccination in the plan.
     *
     * @return EloquentCollection<int, Vaccine>
     */
    #[Computed]
    public function vaccines(): EloquentCollection
    {
        return Vaccine::query()
            ->withTrashed()
            ->with('species')
            ->whereKey(array_keys($this->counts))
            ->orderBy('name')
            ->get();
    }

    /**
     * This year and the next few, stretched to the last year anything is
     * due (rabies can be three years ahead).
     *
     * @return array<int, int>
     */
    #[Computed]
    public function yearOptions(): array
    {
        $lastDueYear = PetVaccine::query()->pending()->whereIn('pet_id', Pet::query()->resident()->select('id'))->max('due_date');

        return range(today()->year, max(today()->year + 2, $lastDueYear !== null ? (int) substr((string) $lastDueYear, 0, 4) : 0, $this->year));
    }

    /**
     * @return Collection<int, array{pet: Pet, vaccinations: EloquentCollection<int, PetVaccine>}>
     */
    #[Computed]
    public function duePets(): Collection
    {
        return $this->month !== null
            ? $this->dueVaccinationsByPet($this->year, $this->month, $this->vaccine)
            : collect();
    }

    /**
     * Show the animals due in a month, for one vaccine or all of them.
     */
    public function selectMonth(int $month, ?int $vaccineId = null): void
    {
        $this->month = max(0, min(12, $month));
        $this->vaccine = $vaccineId;
    }

    public function clearSelection(): void
    {
        $this->month = null;
        $this->vaccine = null;
    }

    public function updatedYear(): void
    {
        $this->clearSelection();
    }

    public function monthLabel(): string
    {
        return $this->planMonthLabel($this->year, $this->month ?? 0);
    }

    /**
     * The group vaccination form's "due" filter for the selected month: the
     * month itself, or the overdue ones for "before" (only offered up to the
     * current year, where everything before it is overdue).
     */
    public function groupDueMonth(): ?string
    {
        if ($this->month === 0) {
            return $this->year <= today()->year ? 'overdue' : null;
        }

        return sprintf('%d-%02d', $this->year, $this->month);
    }

    public function render(): View
    {
        return view('livewire.pets.vaccination-plan');
    }
}
