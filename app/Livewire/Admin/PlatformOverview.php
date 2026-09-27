<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Models\Adoption;
use App\Models\Pet;
use App\Models\Shelter;
use App\Models\Species;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;

/**
 * The admin's dashboard: how the platform as a whole is doing, how much each
 * shelter is using it and what still needs setting up. Totals only, never
 * the shelters' animals or people.
 */
class PlatformOverview extends Component
{
    /**
     * Users who logged in within this many days count as active.
     */
    private const ACTIVE_USER_DAYS = 30;

    public function mount(): void
    {
        abort_unless(Auth::user()->is_admin, 403);
    }

    /**
     * @return array{shelters: int, activeUsers: int, petsInCare: int, adoptionsThisYear: int}
     */
    #[Computed]
    public function totals(): array
    {
        return [
            'shelters' => Shelter::query()->count(),
            'activeUsers' => User::query()->where('last_login', '>=', now()->subDays(self::ACTIVE_USER_DAYS))->count(),
            'petsInCare' => Pet::query()->whereHas('shelter')->where('status', '!=', 'adopted')->whereNull('date_of_death')->count(),
            'adoptionsThisYear' => Adoption::query()
                ->whereHas('pet', fn (Builder $query) => $query->whereHas('shelter'))
                ->where('application_status', 'Approved')
                ->whereYear('adoption_date', today()->year)
                ->whereNull('return_date')
                ->count(),
        ];
    }

    /**
     * Every shelter with its usage figures, the least recently used first so
     * the ones that went quiet stand out.
     *
     * @return Collection<int, Shelter>
     */
    #[Computed]
    public function shelters(): Collection
    {
        return Shelter::query()
            ->with('region')
            ->withCount([
                'pets as pets_in_care_count' => fn (Builder $query) => $query->where('status', '!=', 'adopted')->whereNull('date_of_death'),
                'pets as adoptions_this_year_count' => fn (Builder $query) => $query->whereHas(
                    'adoptions',
                    fn (Builder $query) => $query->where('application_status', 'Approved')
                        ->whereYear('adoption_date', today()->year)
                        ->whereNull('return_date'),
                ),
            ])
            ->withMax('users', 'last_login')
            ->withMax('pets', 'updated_at')
            ->orderBy('users_max_last_login')
            ->orderBy('name')
            ->get();
    }

    /**
     * Shelters their users can't work in yet: no species enabled, no cage
     * to place animals in, or nobody invited.
     *
     * @return array{withoutSpecies: Collection<int, Shelter>, withoutCages: Collection<int, Shelter>, withoutUsers: Collection<int, Shelter>}
     */
    #[Computed]
    public function sheltersToSetUp(): array
    {
        return [
            'withoutSpecies' => Shelter::query()->whereDoesntHave('species')->orderBy('name')->get(),
            'withoutCages' => Shelter::query()->whereDoesntHave('facilities.wings.cages')->orderBy('name')->get(),
            'withoutUsers' => Shelter::query()->whereDoesntHave('users')->orderBy('name')->get(),
        ];
    }

    /**
     * Species enabled for at least one shelter that have no breeds, which
     * blocks registering animals of that species.
     *
     * @return Collection<int, Species>
     */
    #[Computed]
    public function speciesWithoutBreeds(): Collection
    {
        return Species::query()
            ->whereDoesntHave('breeds')
            ->whereIn('id', DB::table('shelter_species')->select('species_id'))
            ->orderBy('name')
            ->get();
    }

    /**
     * Invited users who have never logged in, oldest invitation first.
     *
     * @return Collection<int, User>
     */
    #[Computed]
    public function pendingInvitations(): Collection
    {
        return User::query()
            ->with('shelters')
            ->where('is_admin', false)
            ->whereNull('last_login')
            ->orderBy('created_at')
            ->get();
    }

    public function render(): View
    {
        return view('livewire.admin.platform-overview');
    }
}
