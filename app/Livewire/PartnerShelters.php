<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Region;
use App\Models\Shelter;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

/**
 * Public page listing every partner shelter with its contacts and how many
 * animals it currently has published for adoption on the portal.
 */
class PartnerShelters extends Component
{
    #[Url(as: 'region')]
    public string $regionFilter = '';

    /**
     * @return Collection<int, Shelter>
     */
    #[Computed]
    public function shelters(): Collection
    {
        return Shelter::query()
            ->with('region')
            ->withCount('publishedPets')
            ->when($this->regionFilter !== '', fn (Builder $query) => $query->where('region_id', $this->regionFilter))
            ->orderBy('name')
            ->get();
    }

    /**
     * Only regions that actually have a shelter, so the filter never offers
     * a district with an empty result.
     *
     * @return Collection<int, Region>
     */
    #[Computed]
    public function regions(): Collection
    {
        return Region::query()->whereHas('shelters')->orderBy('name')->get();
    }

    #[Layout('layouts::public')]
    public function render(): View
    {
        return view('livewire.partner-shelters')
            ->title(__('Partner shelters'))
            ->layoutData(['description' => __('Meet the animal shelters that are part of our network, their contacts and how many animals each one has waiting for adoption.')]);
    }
}
