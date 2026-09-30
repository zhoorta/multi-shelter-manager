<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Adoption;
use App\Models\AdoptionApplication;
use App\Models\Cage;
use App\Models\Member;
use App\Models\Pet;
use App\Models\PetSickness;
use App\Models\PetVaccine;
use App\Models\Shelter;
use App\Models\Sponsorship;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Dashboard')]
class Dashboard extends Component
{
    /**
     * How many days before and after a sponsorship's last paid day it is
     * listed as needing renewal.
     */
    private const SPONSORSHIP_RENEWAL_WINDOW_DAYS = 30;

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

    public function mount(): void
    {
        // Admins get the platform overview instead, which loads its own data.
        if (Auth::user()->is_admin) {
            $this->recentIntakes = new Collection;
            $this->recentAdoptions = new Collection;
            $this->unknownLocationPets = new Collection;
            $this->recentPassings = new Collection;

            return;
        }

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

        // Foster families are not shelter space: their cages don't add
        // capacity and the animals with them don't take any up.
        $totalCapacity = (int) Cage::query()
            ->whereHas('wing', fn ($query) => $query->where('is_foster', false)->whereHas('facility', fn ($query) => $query->where('shelter_id', $shelterId)))
            ->sum('capacity');

        $petsInFosterFamilies = Pet::query()
            ->where('shelter_id', $shelterId)
            ->where('status', '!=', 'adopted')
            ->whereNull('date_of_death')
            ->whereHas('cage.wing', fn ($query) => $query->where('is_foster', true))
            ->count();

        $this->availableCapacity = max(0, $totalCapacity - ($this->activePetsCount - $petsInFosterFamilies));

        $this->showsActionCounters = ! Auth::user()->isViewerOfCurrentShelter();

        if ($this->showsActionCounters) {
            $this->pendingApplicationsCount = AdoptionApplication::query()
                ->where('status', 'pending')
                ->whereHas('pet', fn ($query) => $query->where('shelter_id', $shelterId))
                ->count();

            $this->overdueVaccinationsCount = PetVaccine::query()
                ->pending()
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
            ->with(['species', 'images', 'cage.wing'])
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
    }

    /**
     * Pets still in the shelter's care with an active or chronic diagnosis,
     * most recently diagnosed first.
     *
     * @return Collection<int, Pet>
     */
    #[Computed]
    public function petsWithOpenHealthIssues(): Collection
    {
        return $this->petsInCareQuery()
            ->with(['species', 'images', 'openSicknesses'])
            ->whereHas('openSicknesses')
            ->orderByDesc(
                PetSickness::query()
                    ->select('diagnosed_at')
                    ->whereColumn('pet_id', 'pets.id')
                    ->whereIn('status', PetSickness::OPEN_STATUSES)
                    ->latest('diagnosed_at')
                    ->limit(1)
            )
            ->take(5)
            ->get();
    }

    /**
     * Pets still in the shelter's care that have no photo yet, so they
     * can't be shown on the portal or on social media, newest intakes first.
     *
     * @return Collection<int, Pet>
     */
    #[Computed]
    public function petsWithoutPhoto(): Collection
    {
        return $this->petsInCareQuery()
            ->with(['species', 'images'])
            ->whereDoesntHave('images')
            ->orderByDesc('checkin_date')
            ->orderByDesc('created_at')
            ->take(5)
            ->get();
    }

    /**
     * Pets available for adoption that have waited the longest since their
     * check-in, the ones most in need of being promoted.
     *
     * @return Collection<int, Pet>
     */
    #[Computed]
    public function longestWaitingPets(): Collection
    {
        return Pet::query()
            ->with(['species', 'images'])
            ->where('shelter_id', Auth::user()->current_shelter_id)
            ->where('status', 'available')
            ->whereNotNull('checkin_date')
            ->orderBy('checkin_date')
            ->orderBy('created_at')
            ->take(5)
            ->get();
    }

    /**
     * Sponsorships of pets still in care whose last paid day falls within
     * the renewal window (recently expired or about to), soonest first. The
     * sponsor has to be contacted, so only users who manage sponsorships
     * get them.
     *
     * @return Collection<int, Sponsorship>
     */
    #[Computed]
    public function sponsorshipsToRenew(): Collection
    {
        if (! $this->showsActionCounters || ! Auth::user()->currentShelterHasModule('sponsorships')) {
            return new Collection;
        }

        $shelterId = Auth::user()->current_shelter_id;
        $windowStart = today()->subDays(self::SPONSORSHIP_RENEWAL_WINDOW_DAYS)->toDateString();
        $windowEnd = today()->addDays(self::SPONSORSHIP_RENEWAL_WINDOW_DAYS)->toDateString();

        return Sponsorship::query()
            ->with(['pet.species', 'pet.images'])
            ->withMax('payments', 'end_date')
            ->whereHas('pet', fn ($query) => $query->where('shelter_id', $shelterId)->where('status', '!=', 'adopted')->whereNull('date_of_death'))
            ->whereHas('payments', fn ($query) => $query->whereBetween('end_date', [$windowStart, $windowEnd]))
            ->whereDoesntHave('payments', fn ($query) => $query->where('end_date', '>', $windowEnd))
            ->orderBy('payments_max_end_date')
            ->take(5)
            ->get();
    }

    /**
     * Pets of the current shelter that are neither adopted nor deceased.
     *
     * @return Builder<Pet>
     */
    private function petsInCareQuery(): Builder
    {
        return Pet::query()
            ->where('shelter_id', Auth::user()->current_shelter_id)
            ->where('status', '!=', 'adopted')
            ->whereNull('date_of_death');
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

    /**
     * The species/breeds warnings ask users to contact the site administrator,
     * so they show the installation's contact e-mail when one is configured.
     */
    public function render(): View
    {
        return view('livewire.dashboard', ['administratorEmail' => config('app.contact_email')]);
    }
}
