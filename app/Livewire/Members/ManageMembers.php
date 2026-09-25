<?php

declare(strict_types=1);

namespace App\Livewire\Members;

use App\Models\Member;
use App\Models\Shelter;
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

#[Title('Members')]
class ManageMembers extends Component
{
    use WithPagination;

    public string $search = '';

    public string $statusFilter = 'active';

    #[Url]
    public bool $inArrearsOnly = false;

    public string $defaultJoiningFee = '0.00';

    public string $defaultMembershipFee = '0.00';

    public string $defaultMembershipFeeFrequency = 'yearly';

    public function mount(): void
    {
        abort_if(Auth::user()->is_admin, 403);
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

    public function updatingInArrearsOnly(): void
    {
        $this->resetPage();
    }

    /**
     * @return LengthAwarePaginator<int, Member>
     */
    #[Computed]
    public function members(): LengthAwarePaginator
    {
        return Member::query()
            ->withMax(['payments as fees_paid_until' => fn (Builder $query) => $query->where('type', 'membership_fee')], 'end_date')
            ->when(
                $this->search !== '',
                fn (Builder $query) => $query->where(
                    fn (Builder $query) => $query->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('member_number', $this->search)
                        ->orWhere('phone', 'like', '%'.$this->search.'%')
                        ->orWhere('email', 'like', '%'.$this->search.'%')
                        ->orWhere('tin', 'like', '%'.$this->search.'%')
                        ->orWhere('notes', 'like', '%'.$this->search.'%')
                ),
            )
            ->when($this->statusFilter !== '', fn (Builder $query) => $query->where('status', $this->statusFilter))
            ->when($this->inArrearsOnly, fn (Builder $query) => $query->inArrears())
            ->orderBy('member_number')
            ->paginate(20);
    }

    /**
     * Ids of the listed members who are in arrears, to flag them in the table.
     *
     * @return array<int, int>
     */
    #[Computed]
    public function inArrearsIds(): array
    {
        return Member::query()
            ->inArrears()
            ->whereKey(array_map(fn (Member $member): int => $member->id, $this->members()->items()))
            ->pluck('id')
            ->all();
    }

    protected function currentShelter(): Shelter
    {
        return Shelter::query()->findOrFail(Auth::user()->current_shelter_id);
    }

    public function editFeeDefaults(): void
    {
        abort_unless(Auth::user()->isManagerOfCurrentShelter(), 403);

        $shelter = $this->currentShelter();
        $this->defaultJoiningFee = (string) $shelter->joining_fee;
        $this->defaultMembershipFee = (string) $shelter->membership_fee;
        $this->defaultMembershipFeeFrequency = $shelter->membership_fee_frequency;
        $this->resetErrorBag();
    }

    public function saveFeeDefaults(): void
    {
        abort_unless(Auth::user()->isManagerOfCurrentShelter(), 403);

        $validated = $this->validate([
            'defaultJoiningFee' => ['required', 'numeric', 'min:0', 'max:999999'],
            'defaultMembershipFee' => ['required', 'numeric', 'min:0', 'max:999999'],
            'defaultMembershipFeeFrequency' => ['required', 'in:monthly,quarterly,semiannual,yearly'],
        ], [], [
            'defaultJoiningFee' => __('Joining Fee'),
            'defaultMembershipFee' => __('Membership Fee'),
            'defaultMembershipFeeFrequency' => __('Frequency'),
        ]);

        $this->currentShelter()->update([
            'joining_fee' => $validated['defaultJoiningFee'],
            'membership_fee' => $validated['defaultMembershipFee'],
            'membership_fee_frequency' => $validated['defaultMembershipFeeFrequency'],
        ]);

        Flux::modal('member-fee-defaults')->close();
        Flux::toast(variant: 'success', text: __('Record updated successfully'));
    }

    public function deleteMember(int $memberId): void
    {
        abort_unless(Auth::user()->isManagerOfCurrentShelter(), 403);

        Member::query()->findOrFail($memberId)->delete();

        unset($this->members);

        Flux::toast(variant: 'success', text: __('Record deleted successfully'));
    }

    public function render(): View
    {
        return view('livewire.members.manage-members');
    }
}
