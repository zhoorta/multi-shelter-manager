<?php

declare(strict_types=1);

namespace App\Livewire\Pets;

use App\Models\AdoptionApplication;
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
 * Adoption applications sent through the public portal. Approving one opens
 * the adoption form prefilled with the applicant's details; the application
 * is only marked approved once that adoption is saved.
 */
#[Title('Adoption Applications')]
class ManageAdoptionApplications extends Component
{
    use WithPagination;

    public string $search = '';

    #[Url]
    public string $statusFilter = 'pending';

    public function mount(): void
    {
        abort_unless(! Auth::user()->is_admin, 403);
        abort_if(Auth::user()->isViewerOfCurrentShelter(), 403);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    /**
     * @return LengthAwarePaginator<int, AdoptionApplication>
     */
    #[Computed]
    public function applications(): LengthAwarePaginator
    {
        return $this->scopedApplicationsQuery()
            ->with(['pet.species', 'pet.images', 'reviewer'])
            ->when($this->statusFilter !== '', fn (Builder $query) => $query->where('status', $this->statusFilter))
            ->when(
                $this->search !== '',
                fn (Builder $query) => $query->where(
                    fn (Builder $query) => $query->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('phone', 'like', '%'.$this->search.'%')
                        ->orWhere('email', 'like', '%'.$this->search.'%')
                        ->orWhere('city', 'like', '%'.$this->search.'%')
                        ->orWhereHas('pet', fn (Builder $query) => $query->where('name', 'like', '%'.$this->search.'%')
                            ->orWhere('ref', 'like', '%'.$this->search.'%'))
                ),
            )
            ->latest()
            ->paginate(20);
    }

    /**
     * Pending applications for pets that have meanwhile been adopted (or
     * are otherwise no longer available), offered for rejection in bulk.
     */
    #[Computed]
    public function staleApplicationsCount(): int
    {
        return $this->staleApplicationsQuery()->count();
    }

    public function rejectApplication(int $applicationId): void
    {
        abort_unless(Auth::user()->canEditCurrentShelter(), 403);

        $this->scopedApplicationsQuery()
            ->where('status', 'pending')
            ->findOrFail($applicationId)
            ->update(['status' => 'rejected', 'reviewed_by' => Auth::id(), 'reviewed_at' => now()]);

        $this->refreshApplications();

        Flux::toast(variant: 'success', text: __('Application rejected'));
    }

    public function rejectStaleApplications(): void
    {
        abort_unless(Auth::user()->canEditCurrentShelter(), 403);

        $rejectedCount = 0;

        // Saved one by one so Blameable stamps updated_by on each row.
        foreach ($this->staleApplicationsQuery()->get() as $application) {
            $application->update(['status' => 'rejected', 'reviewed_by' => Auth::id(), 'reviewed_at' => now()]);
            $rejectedCount++;
        }

        $this->refreshApplications();

        Flux::toast(variant: 'success', text: trans_choice(':count application rejected|:count applications rejected', $rejectedCount));
    }

    public function deleteApplication(int $applicationId): void
    {
        abort_unless(Auth::user()->canEditCurrentShelter(), 403);

        $this->scopedApplicationsQuery()->findOrFail($applicationId)->delete();

        $this->refreshApplications();

        Flux::toast(variant: 'success', text: __('Record deleted successfully'));
    }

    /**
     * whereHas('pet') relies on Pet's MultiShelterTrait global scope to keep
     * this scoped to the acting user's shelter, since AdoptionApplication
     * has no shelter_id (same as Adoption).
     *
     * @return Builder<AdoptionApplication>
     */
    private function scopedApplicationsQuery(): Builder
    {
        return AdoptionApplication::query()->whereHas('pet');
    }

    /**
     * @return Builder<AdoptionApplication>
     */
    private function staleApplicationsQuery(): Builder
    {
        return AdoptionApplication::query()
            ->where('status', 'pending')
            ->whereHas('pet', fn (Builder $query) => $query->where('status', '!=', 'available'));
    }

    private function refreshApplications(): void
    {
        unset($this->applications, $this->staleApplicationsCount);
    }

    public function render(): View
    {
        return view('livewire.pets.manage-adoption-applications');
    }
}
