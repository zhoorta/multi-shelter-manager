<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Models\Shelter;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Manage Shelters')]
class ManageShelters extends Component
{
    public function mount(): void
    {
        abort_unless(Auth::user()->is_admin, 403);
    }

    /**
     * @return Collection<int, Shelter>
     */
    #[Computed]
    public function shelters(): Collection
    {
        return Shelter::query()
            ->withCount(['users', 'pets'])
            ->orderBy('name')
            ->get();
    }

    public function deleteShelter(int $shelterId): void
    {
        Shelter::query()->findOrFail($shelterId)->delete();

        unset($this->shelters);

        Flux::toast(variant: 'success', text: __('Record deleted successfully'));
    }

    public function render(): View
    {
        return view('livewire.admin.manage-shelters');
    }
}
