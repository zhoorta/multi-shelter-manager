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
 * guide for shelters, and screenshots of the backoffice in the visitor's
 * language. Part of the public portal.
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

    /**
     * The folder (under public/) holding the backoffice screenshots in the
     * current locale, falling back to the English ones for a locale without
     * its own set.
     */
    public function screenshotsPath(): string
    {
        $path = 'images/about/'.app()->getLocale();

        return is_dir(public_path($path)) ? $path : 'images/about/en';
    }

    #[Layout('layouts::public')]
    public function render(): View
    {
        return view('livewire.about', ['contactEmail' => config('app.contact_email'), 'userGuideUrl' => $this->userGuideUrl(), 'screenshotsPath' => $this->screenshotsPath()])
            ->title(__('Free management platform for animal shelters'))
            ->layoutData(['description' => __('Nonprofit platform, free for animal shelters and associations: animals, health, vaccinations, adoptions, sponsorships, volunteers and members in one place.')]);
    }
}
