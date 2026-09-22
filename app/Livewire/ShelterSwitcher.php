<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Shelter;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

class ShelterSwitcher extends Component
{
    /**
     * Every shelter the acting user belongs to.
     *
     * @return Collection<int, Shelter>
     */
    #[Computed]
    public function memberships(): Collection
    {
        return Auth::user()->shelters()->orderBy('shelters.name')->get();
    }

    public function switchTo(int $shelterId): void
    {
        $user = Auth::user();

        abort_unless($user->belongsToShelter($shelterId), 403);

        $user->update(['current_shelter_id' => $shelterId]);

        $this->redirect(route('dashboard'), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.shelter-switcher');
    }
}
