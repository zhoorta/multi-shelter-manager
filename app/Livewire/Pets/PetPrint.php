<?php

declare(strict_types=1);

namespace App\Livewire\Pets;

use App\Models\Pet;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

class PetPrint extends Component
{
    public Pet $pet;

    public function mount(Pet $pet): void
    {
        abort_unless(! Auth::user()->is_admin, 403);

        $this->pet = $pet->load([
            'species', 'breed', 'cage.wing.facility', 'images', 'shelter',
            'adoptions' => fn ($query) => $query->latest('adoption_date'),
        ]);
    }

    #[Layout('layouts.print')]
    public function render(): View
    {
        return view('livewire.pets.pet-print')->title($this->pet->name);
    }
}
