<?php

declare(strict_types=1);

namespace App\Livewire\Volunteers;

use App\Models\Activity;
use App\Models\Species;
use App\Models\Volunteer;
use Flux\Flux;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Manage Volunteers')]
class ManageVolunteers extends Component
{
    use WithPagination;

    public string $search = '';

    public string $speciesFilter = '';

    public string $dayFilter = '';

    public string $activityFilter = '';

    public function mount(): void
    {
        abort_unless(! Auth::user()->is_admin, 403);
        abort_if(Auth::user()->isViewerOfCurrentShelter(), 403);
    }

    protected function isManager(): bool
    {
        return Auth::user()->isManagerOfCurrentShelter();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingSpeciesFilter(): void
    {
        $this->resetPage();
    }

    public function updatingDayFilter(): void
    {
        $this->resetPage();
    }

    public function updatingActivityFilter(): void
    {
        $this->resetPage();
    }

    /**
     * @return LengthAwarePaginator<int, Volunteer>
     */
    #[Computed]
    public function volunteers(): LengthAwarePaginator
    {
        return Volunteer::query()
            ->with(['activities', 'species', 'availabilities'])
            ->when(
                $this->search !== '',
                fn (Builder $query) => $query->where(
                    fn (Builder $query) => $query->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('phone', 'like', '%'.$this->search.'%')
                        ->orWhere('email', 'like', '%'.$this->search.'%')
                        ->orWhere('tin', 'like', '%'.$this->search.'%')
                        ->orWhere('notes', 'like', '%'.$this->search.'%')
                ),
            )
            ->when(
                $this->speciesFilter !== '',
                fn (Builder $query) => $query->whereHas(
                    'species',
                    fn (Builder $query) => $query->where('species.id', $this->speciesFilter),
                ),
            )
            ->when(
                $this->dayFilter !== '',
                fn (Builder $query) => $query->whereHas(
                    'availabilities',
                    fn (Builder $query) => $query->where('day_index', $this->dayFilter),
                ),
            )
            ->when(
                $this->activityFilter !== '',
                fn (Builder $query) => $query->whereHas(
                    'activities',
                    fn (Builder $query) => $query->where('activities.id', $this->activityFilter),
                ),
            )
            ->orderBy('name')
            ->paginate(20);
    }

    /**
     * @return Collection<int, Species>
     */
    #[Computed]
    public function species(): Collection
    {
        return Species::query()->orderBy('name')->get();
    }

    /**
     * @return Collection<int, Activity>
     */
    #[Computed]
    public function activities(): Collection
    {
        return Activity::query()->orderBy('name')->get();
    }

    public function deleteVolunteer(int $volunteerId): void
    {
        abort_unless($this->isManager(), 403);

        Volunteer::query()->findOrFail($volunteerId)->delete();

        unset($this->volunteers);

        Flux::toast(variant: 'success', text: __('Record deleted successfully'));
    }

    public function render(): View
    {
        return view('livewire.volunteers.manage-volunteers');
    }
}
