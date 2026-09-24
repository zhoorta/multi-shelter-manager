<?php

declare(strict_types=1);

namespace App\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

/**
 * Public "About" page with a short description of the nonprofit project and
 * its contact e-mail (config('app.contact_email')). Stays public even when
 * the adoption portal is disabled, like the privacy policy.
 */
class About extends Component
{
    #[Layout('layouts::public')]
    public function render(): View
    {
        return view('livewire.about', ['contactEmail' => config('app.contact_email')])
            ->title(__('About'))
            ->layoutData(['description' => __('A nonprofit project that helps animal shelters care for their animals and find them a loving home.')]);
    }
}
