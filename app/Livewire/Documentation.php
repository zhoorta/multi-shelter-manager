<?php

declare(strict_types=1);

namespace App\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Documentation')]
class Documentation extends Component
{
    public function render(): View
    {
        return view('livewire.documentation');
    }
}
