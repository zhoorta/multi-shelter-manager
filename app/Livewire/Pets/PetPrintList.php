<?php

declare(strict_types=1);

namespace App\Livewire\Pets;

use App\Livewire\Pets\Concerns\FiltersPetsList;
use App\Models\Pet;
use App\Models\Shelter;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

class PetPrintList extends Component
{
    use FiltersPetsList;

    #[Url]
    public string $search = '';

    #[Url]
    public string $statusFilter = '';

    #[Url]
    public string $locationFilter = '';

    #[Url]
    public string $speciesFilter = '';

    #[Url]
    public string $missingDataFilter = '';

    public Shelter $shelter;

    /**
     * @var Collection<int, Pet>
     */
    public Collection $pets;

    public function mount(): void
    {
        abort_unless(in_array(Auth::user()->role, ['manager', 'staff'], true), 403);

        $this->shelter = Auth::user()->shelter;
        $this->pets = $this->filteredPetsQuery()->get();
    }

    #[Layout('layouts.print')]
    public function render(): View
    {
        return view('livewire.pets.pet-print-list');
    }
}
