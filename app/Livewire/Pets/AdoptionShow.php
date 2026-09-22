<?php

declare(strict_types=1);

namespace App\Livewire\Pets;

use App\Models\Adoption;
use App\Models\Pet;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AdoptionShow extends Component
{
    public Pet $pet;

    public Adoption $adoption;

    public function mount(Pet $pet, Adoption $adoption): void
    {
        abort_unless(! Auth::user()->is_admin, 403);
        abort_if($adoption->pet_id !== $pet->id, 404);

        $this->pet = $pet;
        $this->adoption = $adoption;
    }

    public function render(): View
    {
        return view('livewire.pets.adoption-show')->title(__('Adoption').' — '.$this->pet->name);
    }
}
