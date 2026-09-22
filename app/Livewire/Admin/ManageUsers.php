<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Models\Shelter;
use App\Models\User;
use Flux\Flux;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Manage Users')]
class ManageUsers extends Component
{
    use WithPagination;

    public string $filterShelterId = '';

    public function mount(): void
    {
        $viewer = Auth::user();

        abort_unless($viewer->is_admin || $viewer->isManagerOfCurrentShelter(), 403);
    }

    /**
     * @return LengthAwarePaginator<int, User>
     */
    #[Computed]
    public function users(): LengthAwarePaginator
    {
        $viewer = Auth::user();

        return User::query()
            ->with(['shelters' => fn ($query) => $viewer->is_admin
                ? $query
                : $query->whereIn('shelters.id', $viewer->managedShelterIds())])
            ->when(
                $viewer->is_admin && $this->filterShelterId !== '',
                fn ($query) => $query->whereHas('shelters', fn ($q) => $q->whereKey((int) $this->filterShelterId)),
            )
            ->when(
                ! $viewer->is_admin,
                fn ($query) => $query->whereHas('shelters', fn ($q) => $q->whereKey($viewer->current_shelter_id)),
            )
            ->orderBy('name')
            ->paginate(20);
    }

    /**
     * @return Collection<int, Shelter>
     */
    #[Computed]
    public function shelters(): Collection
    {
        return Shelter::query()->orderBy('name')->get();
    }

    public function updatingFilterShelterId(): void
    {
        $this->resetPage();
    }

    public function deleteUser(int $userId): void
    {
        if ($userId === Auth::id()) {
            Flux::toast(variant: 'danger', text: __('You cannot delete your own account'));

            return;
        }

        $viewer = Auth::user();

        $user = User::query()
            ->when(
                ! $viewer->is_admin,
                fn ($query) => $query->whereHas('shelters', fn ($q) => $q->whereKey($viewer->current_shelter_id)),
            )
            ->findOrFail($userId);

        if (! $viewer->is_admin && $user->shelters()->whereKeyNot($viewer->current_shelter_id)->exists()) {
            $this->removeFromCurrentShelter($user, $viewer->current_shelter_id);

            return;
        }

        $user->delete();

        unset($this->users);

        Flux::toast(variant: 'success', text: __('Record deleted successfully'));
    }

    /**
     * Detach a user who also belongs to other shelters from the manager's
     * current shelter only, keeping their account for those other shelters.
     */
    private function removeFromCurrentShelter(User $user, int $shelterId): void
    {
        $user->shelters()->detach($shelterId);

        if ($user->current_shelter_id === $shelterId) {
            $user->update(['current_shelter_id' => $user->shelters()->value('shelters.id')]);
        }

        unset($this->users);

        Flux::toast(variant: 'success', text: __('User removed from the shelter'));
    }

    public function render(): View
    {
        return view('livewire.admin.manage-users');
    }
}
