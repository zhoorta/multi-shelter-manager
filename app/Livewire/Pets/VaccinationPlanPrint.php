<?php

declare(strict_types=1);

namespace App\Livewire\Pets;

use App\Livewire\Pets\Concerns\ListsDueVaccinations;
use App\Models\Pet;
use App\Models\PetVaccine;
use App\Models\Shelter;
use App\Models\Vaccine;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

/**
 * Printable list, for the vet, of the resident animals with vaccinations
 * due in one month of the vaccination plan, with blank columns to note the
 * dose given.
 */
class VaccinationPlanPrint extends Component
{
    use ListsDueVaccinations;

    #[Url]
    public ?int $year = null;

    #[Url]
    public int $month = 0;

    #[Url]
    public ?int $vaccine = null;

    public Shelter $shelter;

    public ?Vaccine $selectedVaccine = null;

    /**
     * @var Collection<int, array{pet: Pet, vaccinations: EloquentCollection<int, PetVaccine>}>
     */
    public Collection $duePets;

    public function mount(): void
    {
        abort_unless(! Auth::user()->is_admin, 403);

        $this->year ??= today()->year;
        $this->month = max(0, min(self::WHOLE_YEAR, $this->month));
        $this->shelter = Auth::user()->currentShelter;
        $this->selectedVaccine = $this->vaccine !== null ? Vaccine::withTrashed()->find($this->vaccine) : null;
        $this->duePets = $this->dueVaccinationsByPet($this->year, $this->month, $this->vaccine);
    }

    #[Layout('layouts.print')]
    public function render(): View
    {
        return view('livewire.pets.vaccination-plan-print', [
            'monthLabel' => $this->planMonthLabel($this->year, $this->month),
        ])->title(__('Vaccination Plan'));
    }
}
