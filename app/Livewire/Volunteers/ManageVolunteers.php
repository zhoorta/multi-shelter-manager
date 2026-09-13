<?php

declare(strict_types=1);

namespace App\Livewire\Volunteers;

use App\Models\Volunteer;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Manage Volunteers')]
class ManageVolunteers extends Component
{
    public function mount(): void
    {
        abort_unless(in_array(Auth::user()->role, ['manager', 'staff'], true), 403);
    }

    protected function isManager(): bool
    {
        return Auth::user()->role === 'manager';
    }

    /**
     * @return Collection<int, Volunteer>
     */
    #[Computed]
    public function volunteers(): Collection
    {
        return Volunteer::query()
            ->orderBy('name')
            ->get();
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
