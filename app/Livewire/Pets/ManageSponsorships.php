<?php

declare(strict_types=1);

namespace App\Livewire\Pets;

use App\Models\Sponsorship;
use Flux\Flux;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Manage Sponsorships')]
class ManageSponsorships extends Component
{
    use WithPagination;

    public string $search = '';

    public function mount(): void
    {
        abort_unless(! Auth::user()->is_admin, 403);
        abort_if(Auth::user()->isViewerOfCurrentShelter(), 403);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /**
     * @return LengthAwarePaginator<int, Sponsorship>
     */
    #[Computed]
    public function sponsorships(): LengthAwarePaginator
    {
        return Sponsorship::query()
            // whereHas('pet') relies on Pet's MultiShelterTrait global scope
            // to keep this scoped to the acting user's shelter, since
            // Sponsorship itself has no shelter_id (see .ai/rules/pets.md).
            ->whereHas('pet')
            ->with(['pet.species', 'pet.images', 'payments'])
            ->when(
                $this->search !== '',
                fn (Builder $query) => $query->where(
                    fn (Builder $query) => $query->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('phone', 'like', '%'.$this->search.'%')
                        ->orWhere('email', 'like', '%'.$this->search.'%')
                        ->orWhere('notes', 'like', '%'.$this->search.'%')
                        ->orWhereHas('pet', fn (Builder $query) => $query->where('name', 'like', '%'.$this->search.'%')
                            ->orWhere('ref', 'like', '%'.$this->search.'%'))
                ),
            )
            ->latest()
            ->paginate(20);
    }

    public function deleteSponsorship(int $sponsorshipId): void
    {
        abort_unless(Auth::user()->canEditCurrentShelter(), 403);
        Sponsorship::query()->whereHas('pet')->findOrFail($sponsorshipId)->delete();

        unset($this->sponsorships);

        Flux::toast(variant: 'success', text: __('Record deleted successfully'));
    }

    public function render(): View
    {
        return view('livewire.pets.manage-sponsorships');
    }
}
