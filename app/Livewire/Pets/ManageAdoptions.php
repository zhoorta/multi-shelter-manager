<?php

declare(strict_types=1);

namespace App\Livewire\Pets;

use App\Models\Adoption;
use Flux\Flux;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Manage Adoptions')]
class ManageAdoptions extends Component
{
    use WithPagination;

    public string $search = '';

    /**
     * '' for all, 'pending' for open adoptions not yet moved to the owner in
     * the pet registry (SIAC in Portugal), 'transferred' for the ones already done.
     */
    public string $siacFilter = '';

    public function mount(): void
    {
        abort_unless(! Auth::user()->is_admin, 403);
        abort_if(Auth::user()->isViewerOfCurrentShelter(), 403);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingSiacFilter(): void
    {
        $this->resetPage();
    }

    /**
     * @return LengthAwarePaginator<int, Adoption>
     */
    #[Computed]
    public function adoptions(): LengthAwarePaginator
    {
        $search = trim($this->search);

        return Adoption::query()
            // whereHas('pet') relies on Pet's MultiShelterTrait global scope
            // to keep this scoped to the acting user's shelter, since
            // Adoption itself has no shelter_id (see .ai/rules/pets.md).
            ->whereHas('pet')
            ->with(['pet.species', 'pet.images'])
            ->when(
                $search !== '',
                fn (Builder $query) => $query->where(
                    fn (Builder $query) => $query->where('name', 'like', '%'.$search.'%')
                        ->orWhere('phone', 'like', '%'.$search.'%')
                        ->orWhere('email', 'like', '%'.$search.'%')
                        ->orWhere('notes', 'like', '%'.$search.'%')
                        ->orWhereHas('pet', fn (Builder $query) => $query->where('name', 'like', '%'.$search.'%')
                            ->orWhere('ref', 'like', '%'.$search.'%'))
                ),
            )
            ->when($this->siacFilter === 'pending', fn (Builder $query) => $query->whereNull('siac_transferred_at')->whereNull('return_date'))
            ->when($this->siacFilter === 'transferred', fn (Builder $query) => $query->whereNotNull('siac_transferred_at'))
            ->latest('adoption_date')
            ->paginate(20);
    }

    public function deleteAdoption(int $adoptionId): void
    {
        abort_unless(Auth::user()->canEditArea('adoptions'), 403);
        $adoption = Adoption::query()->whereHas('pet')->with('pet')->findOrFail($adoptionId);

        DB::transaction(function () use ($adoption): void {
            $wasOpenAdoption = $adoption->return_date === null;

            $adoption->delete();

            if ($wasOpenAdoption) {
                $adoption->pet->update([
                    'status' => $adoption->pet->determineStatus(),
                    'checkout_date' => null,
                ]);
            }
        });

        unset($this->adoptions);

        Flux::toast(variant: 'success', text: __('Record deleted successfully'));
    }

    public function render(): View
    {
        return view('livewire.pets.manage-adoptions');
    }
}
