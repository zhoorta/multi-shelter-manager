<?php

declare(strict_types=1);

namespace App\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;

/**
 * Public "About" page with a short description of the nonprofit project, its
 * contact e-mail (config('app.contact_email')) and a link to the PDF user
 * guide for shelters. Part of the public portal.
 */
class About extends Component
{
    /**
     * The user guide PDFs on the public disk, uploaded by hand (they are not
     * in the repository). Portuguese visitors get the Portuguese guide and
     * every other locale the English one.
     *
     * @var array{pt: string, en: string}
     */
    public const USER_GUIDES = [
        'pt' => 'docs/focinhos-guia-utilizacao.pdf',
        'en' => 'docs/focinhos-user-guide.pdf',
    ];

    /**
     * The public URL of the user guide for the current locale, or null when
     * that PDF hasn't been uploaded (e.g. on a self-hosted installation).
     */
    public function userGuideUrl(): ?string
    {
        $path = app()->getLocale() === 'pt' ? self::USER_GUIDES['pt'] : self::USER_GUIDES['en'];

        return Storage::disk('public')->exists($path) ? Storage::disk('public')->url($path) : null;
    }

    #[Layout('layouts::public')]
    public function render(): View
    {
        return view('livewire.about', ['contactEmail' => config('app.contact_email'), 'userGuideUrl' => $this->userGuideUrl()])
            ->title(__('About'))
            ->layoutData(['description' => __('A nonprofit project that helps animal shelters care for their animals and find them a loving home.')]);
    }
}
