<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Cage;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Dashboard')]
class Dashboard extends Component
{
    public int $activePetsCount = 0;

    public int $quarantinedPetsCount = 0;

    public int $availableCapacity = 0;

    public int $staffCount = 0;

    /**
     * @var Collection<int, Pet>
     */
    public Collection $recentIntakes;

    public function mount(): void
    {
        $shelterId = Auth::user()->shelter_id;

        $this->activePetsCount = Pet::query()
            ->where('shelter_id', $shelterId)
            ->where('status', '!=', 'adopted')
            ->whereNull('date_of_death')
            ->count();

        $this->quarantinedPetsCount = Pet::query()
            ->where('shelter_id', $shelterId)
            ->where('status', 'quarantine')
            ->count();

        $totalCapacity = (int) Cage::query()
            ->whereHas('wing', fn ($query) => $query->where('shelter_id', $shelterId))
            ->sum('capacity');

        $this->availableCapacity = max(0, $totalCapacity - $this->activePetsCount);

        $this->staffCount = User::query()
            ->where('shelter_id', $shelterId)
            ->count();

        $this->recentIntakes = Pet::query()
            ->with('cage')
            ->where('shelter_id', $shelterId)
            ->latest()
            ->take(5)
            ->get();
    }

    public function render(): View
    {
        return view('livewire.dashboard');
    }
}
