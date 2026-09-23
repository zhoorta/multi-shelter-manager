<?php

declare(strict_types=1);

namespace App\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

/**
 * Public privacy policy page. The long-form text lives in per-locale Blade
 * partials (resources/views/livewire/privacy-policy/content-{locale}.blade.php),
 * the same way the Documentation page does.
 */
class PrivacyPolicy extends Component
{
    #[Layout('layouts::public')]
    public function render(): View
    {
        return view('livewire.privacy-policy')
            ->title(__('Privacy Policy'))
            ->layoutData(['description' => __('How we collect, use and protect personal data on this platform.')]);
    }
}
