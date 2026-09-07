<?php

declare(strict_types=1);

namespace App\Livewire\Pets;

use App\Models\Pet;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PetShow extends Component
{
    public Pet $pet;

    public function mount(Pet $pet): void
    {
        abort_unless(in_array(Auth::user()->role, ['manager', 'staff'], true), 403);

        $this->pet = $pet->load([
            'species', 'breed', 'cage.wing', 'images', 'primaryColor', 'secondaryColor', 'furType',
        ]);
    }

    public function render(): View
    {
        return view('livewire.pets.pet-show')->title($this->pet->name);
    }
}
