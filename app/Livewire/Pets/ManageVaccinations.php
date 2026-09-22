<?php

declare(strict_types=1);

namespace App\Livewire\Pets;

use App\Models\PetVaccine;
use Flux\Flux;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Manage Vaccinations')]
class ManageVaccinations extends Component
{
    use WithPagination;

    public string $search = '';

    public string $nextDueFilter = '';

    public function mount(): void
    {
        abort_unless(! Auth::user()->is_admin, 403);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingNextDueFilter(): void
    {
        $this->resetPage();
    }

    /**
     * @return LengthAwarePaginator<int, PetVaccine>
     */
    #[Computed]
    public function vaccinations(): LengthAwarePaginator
    {
        return PetVaccine::query()
            // whereHas('pet') relies on Pet's MultiShelterTrait global scope
            // to keep this scoped to the acting user's shelter, since
            // PetVaccine itself has no shelter_id (see .ai/rules/pets.md).
            ->whereHas('pet')
            ->with(['pet.species', 'pet.images', 'vaccine'])
            ->when(
                $this->search !== '',
                fn (Builder $query) => $query->where(
                    fn (Builder $query) => $query->where('lot_number', 'like', '%'.$this->search.'%')
                        ->orWhere('veterinarian_name', 'like', '%'.$this->search.'%')
                        ->orWhere('notes', 'like', '%'.$this->search.'%')
                        ->orWhereHas('vaccine', fn (Builder $query) => $query->where('name', 'like', '%'.$this->search.'%'))
                        ->orWhereHas('pet', fn (Builder $query) => $query->where('name', 'like', '%'.$this->search.'%')
                            ->orWhere('ref', 'like', '%'.$this->search.'%'))
                ),
            )
            ->when(
                $this->nextDueFilter !== '',
                function (Builder $query): void {
                    match ($this->nextDueFilter) {
                        'within_week' => $query->whereBetween('due_date', [today(), today()->addWeek()]),
                        'within_two_weeks' => $query->whereBetween('due_date', [today(), today()->addWeeks(2)]),
                        'within_month' => $query->whereBetween('due_date', [today(), today()->addMonth()]),
                        'overdue' => $query->where('status', 'scheduled')->where('due_date', '<', today()),
                        default => null,
                    };
                },
            )
            ->orderByRaw('COALESCE(administered_date, due_date) desc')
            ->paginate(20);
    }

    public function deleteVaccination(int $petVaccineId): void
    {
        PetVaccine::query()->whereHas('pet')->findOrFail($petVaccineId)->delete();

        unset($this->vaccinations);

        Flux::toast(variant: 'success', text: __('Record deleted successfully'));
    }

    public function render(): View
    {
        return view('livewire.pets.manage-vaccinations');
    }
}
