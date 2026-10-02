<?php

declare(strict_types=1);

namespace App\Livewire\Pets;

use App\Models\PetTreatment;
use Flux\Flux;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Shelter-wide list of preventive treatment records, mirrors
 * ManageVaccinations.
 *
 * @property-read LengthAwarePaginator<int, PetTreatment> $petTreatments
 */
#[Title('Treatments')]
class ManagePetTreatments extends Component
{
    use WithPagination;

    public string $search = '';

    #[Url]
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
     * @return LengthAwarePaginator<int, PetTreatment>
     */
    #[Computed]
    public function petTreatments(): LengthAwarePaginator
    {
        $search = trim($this->search);

        return PetTreatment::query()
            // whereHas('pet') relies on Pet's MultiShelterTrait global scope
            // to keep this scoped to the acting user's shelter, since
            // PetTreatment itself has no shelter_id.
            ->whereHas('pet')
            ->with(['pet.species', 'pet.images', 'treatment'])
            ->when(
                $search !== '',
                fn (Builder $query) => $query->where(
                    fn (Builder $query) => $query->where('product', 'like', '%'.$search.'%')
                        ->orWhere('veterinarian_name', 'like', '%'.$search.'%')
                        ->orWhere('notes', 'like', '%'.$search.'%')
                        ->orWhereHas('treatment', fn (Builder $query) => $query->where('name', 'like', '%'.$search.'%'))
                        ->orWhereHas('pet', fn (Builder $query) => $query->where('name', 'like', '%'.$search.'%')
                            ->orWhere('ref', 'like', '%'.$search.'%'))
                ),
            )
            ->when(
                $this->nextDueFilter !== '',
                function (Builder $query): void {
                    match ($this->nextDueFilter) {
                        'within_week' => $query->pending()->whereBetween('due_date', [today(), today()->addWeek()]),
                        'within_two_weeks' => $query->pending()->whereBetween('due_date', [today(), today()->addWeeks(2)]),
                        'within_month' => $query->pending()->whereBetween('due_date', [today(), today()->addMonth()]),
                        'overdue' => $query->pending()->where('due_date', '<', today()),
                        default => null,
                    };
                },
            )
            ->orderByRaw('COALESCE(administered_date, due_date) desc')
            ->paginate(20);
    }

    /**
     * Ids of the listed treatments whose due date is still open (see
     * TracksDueDates::pending()), used to highlight overdue and due-soon dates.
     *
     * @return array<int, int>
     */
    #[Computed]
    public function pendingTreatmentIds(): array
    {
        return PetTreatment::query()
            ->whereKey(array_map(fn (PetTreatment $petTreatment): int => $petTreatment->id, $this->petTreatments->items()))
            ->pending()
            ->pluck('id')
            ->all();
    }

    public function deleteTreatment(int $petTreatmentId): void
    {
        abort_unless(Auth::user()->canEditArea('health'), 403);
        PetTreatment::query()->whereHas('pet')->findOrFail($petTreatmentId)->delete();

        unset($this->petTreatments, $this->pendingTreatmentIds);

        Flux::toast(variant: 'success', text: __('Record deleted successfully'));
    }

    public function render(): View
    {
        return view('livewire.pets.manage-pet-treatments');
    }
}
