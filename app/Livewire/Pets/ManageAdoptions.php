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

    public function mount(): void
    {
        abort_unless(in_array(Auth::user()->role, ['manager', 'staff'], true), 403);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /**
     * @return LengthAwarePaginator<int, Adoption>
     */
    #[Computed]
    public function adoptions(): LengthAwarePaginator
    {
        return Adoption::query()
            // whereHas('pet') relies on Pet's MultiShelterTrait global scope
            // to keep this scoped to the acting user's shelter, since
            // Adoption itself has no shelter_id (see .ai/rules/pets.md).
            ->whereHas('pet')
            ->with(['pet.species', 'pet.images'])
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
            ->latest('adoption_date')
            ->paginate(20);
    }

    public function deleteAdoption(int $adoptionId): void
    {
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
