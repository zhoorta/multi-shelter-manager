<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Adoption;
use App\Models\AdoptionApplication;
use App\Models\Cage;
use App\Models\Member;
use App\Models\Pet;
use App\Models\PetVaccine;
use App\Models\Shelter;
use App\Models\Sponsorship;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Dashboard')]
class Dashboard extends Component
{
    public int $activePetsCount = 0;

    public int $adoptionsThisYearCount = 0;

    public int $availableCapacity = 0;

    /**
     * Whether the user sees the counters that call for action; viewers and
     * admins can't open the lists they link to.
     */
    public bool $showsActionCounters = false;

    public int $pendingApplicationsCount = 0;

    public int $overdueVaccinationsCount = 0;

    public int $membersInArrearsCount = 0;

    public bool $hasCages = true;

    public bool $speciesConfigured = true;

    /**
     * @var Collection<int, Pet>
     */
    public Collection $recentIntakes;

    /**
     * @var Collection<int, Adoption>
     */
    public Collection $recentAdoptions;

    /**
     * @var Collection<int, Pet>
     */
    public Collection $unknownLocationPets;

    /**
     * @var Collection<int, Pet>
     */
    public Collection $recentPassings;

    /**
     * @var Collection<int, Sponsorship>
     */
    public Collection $recentSponsorships;

    public function mount(): void
    {
        $shelterId = Auth::user()->current_shelter_id;

        $this->activePetsCount = Pet::query()
            ->where('shelter_id', $shelterId)
            ->where('status', '!=', 'adopted')
            ->whereNull('date_of_death')
            ->count();

        $this->adoptionsThisYearCount = Adoption::query()
            ->whereHas('pet', fn ($query) => $query->where('shelter_id', $shelterId))
            ->where('application_status', 'Approved')
            ->whereYear('adoption_date', today()->year)
            ->whereNull('return_date')
            ->count();

        $totalCapacity = (int) Cage::query()
            ->whereHas('wing.facility', fn ($query) => $query->where('shelter_id', $shelterId))
            ->sum('capacity');

        $this->availableCapacity = max(0, $totalCapacity - $this->activePetsCount);

        $this->showsActionCounters = ! Auth::user()->is_admin && ! Auth::user()->isViewerOfCurrentShelter();

        if ($this->showsActionCounters) {
            $this->pendingApplicationsCount = AdoptionApplication::query()
                ->where('status', 'pending')
                ->whereHas('pet', fn ($query) => $query->where('shelter_id', $shelterId))
                ->count();

            $this->overdueVaccinationsCount = PetVaccine::query()
                ->where('status', 'scheduled')
                ->where('due_date', '<', today())
                ->whereHas('pet', fn ($query) => $query->where('shelter_id', $shelterId))
                ->count();

            $this->membersInArrearsCount = Member::query()
                ->where('shelter_id', $shelterId)
                ->inArrears()
                ->count();
        }

        $this->hasCages = Cage::query()
            ->whereHas('wing.facility', fn ($query) => $query->where('shelter_id', $shelterId))
            ->exists();

        $this->speciesConfigured = Shelter::query()
            ->whereKey($shelterId)
            ->whereHas('species')
            ->exists();

        $this->recentIntakes = Pet::query()
            ->with(['species', 'images'])
            ->where('shelter_id', $shelterId)
            ->whereNotNull('checkin_date')
            ->orderByDesc('checkin_date')
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        $this->recentAdoptions = Adoption::query()
            ->with(['pet.species', 'pet.images'])
            ->whereHas('pet', fn ($query) => $query->where('shelter_id', $shelterId)->where('status', 'adopted'))
            ->orderByDesc('adoption_date')
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        $this->unknownLocationPets = Pet::query()
            ->with(['species', 'images'])
            ->where('shelter_id', $shelterId)
            ->where('status', '!=', 'adopted')
            ->whereNull('date_of_death')
            ->whereNull('cage_id')
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        $this->recentPassings = Pet::query()
            ->with(['species', 'images'])
            ->where('shelter_id', $shelterId)
            ->whereNotNull('date_of_death')
            ->orderByDesc('date_of_death')
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        $this->recentSponsorships = Sponsorship::query()
            ->with(['pet.species', 'pet.images'])
            ->whereHas('pet', fn ($query) => $query->where('shelter_id', $shelterId))
            ->orderByDesc('created_at')
            ->take(5)
            ->get();
    }

    /**
     * Names of the species enabled for the shelter that have no breeds defined.
     *
     * @return SupportCollection<int, string>
     */
    #[Computed]
    public function speciesWithoutBreeds(): SupportCollection
    {
        $shelter = Shelter::query()->find(Auth::user()->current_shelter_id);

        return $shelter
            ? $shelter->species()->whereDoesntHave('breeds')->pluck('name')
            : collect();
    }

    public function render(): View
    {
        return view('livewire.dashboard');
    }
}
