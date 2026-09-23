<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Livewire\Concerns\ShowsPublicPets;
use App\Models\Pet;
use App\Models\Shelter;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Public page of a single partner shelter: its details and contacts, plus
 * every pet it has published for adoption on the portal.
 */
class PartnerShelterShow extends Component
{
    use ShowsPublicPets, WithPagination;

    public Shelter $shelter;

    public function mount(Shelter $shelter): void
    {
        $this->shelter = $shelter->load('region');
    }

    /**
     * @return LengthAwarePaginator<int, Pet>
     */
    #[Computed]
    public function pets(): LengthAwarePaginator
    {
        return $this->shelter->publishedPets()
            ->with(['species', 'breed', 'size', 'shelter.region', 'images'])
            ->orderByDesc('is_featured')
            ->latest('checkin_date')
            ->latest('id')
            ->paginate(12);
    }

    #[Layout('layouts::public')]
    public function render(): View
    {
        return view('livewire.partner-shelter-show')->title($this->shelter->name);
    }
}
