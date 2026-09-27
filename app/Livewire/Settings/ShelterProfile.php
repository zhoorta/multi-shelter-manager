<?php

declare(strict_types=1);

namespace App\Livewire\Settings;

use App\Livewire\Concerns\EditsShelterProfile;
use App\Models\Shelter;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

/**
 * Lets a manager update the contacts and public profile of their current
 * shelter. The name and species stay admin-only (see Admin\ShelterForm).
 */
#[Title('Shelter settings')]
class ShelterProfile extends Component
{
    use EditsShelterProfile;
    use WithFileUploads;

    #[Locked]
    public int $shelterId;

    public string $shelterName = '';

    /**
     * The shelter's public page on the portal, shown read-only (only admins
     * change its address).
     */
    #[Locked]
    public string $publicPageUrl = '';

    public function mount(): void
    {
        $user = Auth::user();

        abort_unless($user->isManagerOfCurrentShelter(), 403);

        $shelter = $user->currentShelter;

        $this->shelterId = $shelter->id;
        $this->shelterName = $shelter->name;
        $this->publicPageUrl = route('shelters.show', $shelter);
        $this->fillShelterProfile($shelter);
    }

    public function saveShelter(): void
    {
        abort_unless(Auth::user()->isManagerOf($this->shelterId), 403);

        $validated = $this->validate($this->shelterProfileRules(), [], $this->shelterProfileAttributes());

        $shelter = Shelter::query()->findOrFail($this->shelterId);
        $shelter->update($this->shelterProfileData($validated));

        $this->existingLogoPath = $shelter->logo_path;
        $this->shelterLogo = null;

        Flux::toast(variant: 'success', text: __('Record updated successfully'));
    }
}
