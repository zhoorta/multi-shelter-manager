<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Pet;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;

/**
 * Public page of a single pet, so each animal has its own URL that search
 * engines can index and people can share. Published pets show the full
 * profile; pets adopted after being published keep their page (so shared
 * links don't break) as a "found a family" page that is not indexed.
 * Anything else is not found.
 */
class PublicPetShow extends Component
{
    #[Locked]
    public int $petId;

    #[Locked]
    public bool $isAdopted = false;

    public function mount(int $petId, ?string $slug = null): void
    {
        $pet = $this->publishedPetsQuery()->find($petId);

        if ($pet === null) {
            $pet = $this->adoptedPetsQuery()->findOrFail($petId);
            $this->isAdopted = true;
        }

        // The slug is only descriptive: an outdated or missing one moves permanently to the current URL.
        if ($slug !== $pet->publicSlug()) {
            throw new HttpResponseException(redirect()->to($pet->publicPageUrl(), 301));
        }

        $this->petId = $pet->id;

        if (! $this->isAdopted) {
            // Counted on the base query so a view does not touch the pet's updated_at/updated_by audit columns.
            Pet::query()->withoutGlobalScopes()->whereKey($pet->id)->toBase()->increment('view_count');
        }
    }

    #[Layout('layouts::public')]
    public function render(): View
    {
        $pet = $this->loadPet();
        $mainImage = $pet->images->firstWhere('is_main', true) ?? $pet->images->first();
        $shelterLogoUrl = $pet->shelter->logo_path ? url(Storage::url($pet->shelter->logo_path)) : null;

        if ($this->isAdopted) {
            $title = __(':name has already found a family!', ['name' => $pet->name]);
            $description = __(':name was adopted through :shelter. Meet the other animals waiting for a home.', ['name' => $pet->name, 'shelter' => $pet->shelter->name]);
        } else {
            $title = __(':name — :species for adoption in :city', ['name' => $pet->name, 'species' => $pet->species->name, 'city' => $pet->publicLocation()]);
            $description = __(':name (:species, :breed) is waiting for a family at :shelter, in :city.', [
                'name' => $pet->name,
                'species' => $pet->species->name,
                'breed' => $pet->breed->name,
                'shelter' => $pet->shelter->name,
                'city' => $pet->publicLocation(),
            ]).' '.Str::squish(html_entity_decode(strip_tags(str_replace('</p>', '</p> ', (string) $pet->description))));
        }

        return view('livewire.public-pet-show', ['pet' => $pet])
            ->title($title)
            ->layoutData([
                'description' => $description,
                'image' => $mainImage ? url(Storage::url($mainImage->image_path)) : $shelterLogoUrl,
                'canonicalUrl' => $pet->publicPageUrl(),
                'noindex' => $this->isAdopted,
                'structuredData' => [
                    '@context' => 'https://schema.org',
                    '@type' => 'BreadcrumbList',
                    'itemListElement' => [
                        ['@type' => 'ListItem', 'position' => 1, 'name' => config('app.name'), 'item' => route('home')],
                        ['@type' => 'ListItem', 'position' => 2, 'name' => $pet->shelter->name, 'item' => route('shelters.show', $pet->shelter)],
                        ['@type' => 'ListItem', 'position' => 3, 'name' => $pet->name, 'item' => $pet->publicPageUrl()],
                    ],
                ],
            ]);
    }

    private function loadPet(): Pet
    {
        return ($this->isAdopted ? $this->adoptedPetsQuery() : $this->publishedPetsQuery())
            ->with(['species', 'breed', 'size', 'furType', 'shelter.region', 'images', 'cage:id,wing_id', 'cage.wing:id,is_foster'])
            ->findOrFail($this->petId);
    }

    /**
     * The shelter global scope is removed on purpose: this is a public page,
     * and logged-in staff must see the same pet as a guest.
     *
     * @return Builder<Pet>
     */
    private function publishedPetsQuery(): Builder
    {
        return Pet::query()->withoutGlobalScope('shelter')->publishedToPortal();
    }

    /**
     * Pets that were published to the portal and have since been adopted.
     * Deceased pets and pets of a deleted shelter are never shown.
     *
     * @return Builder<Pet>
     */
    private function adoptedPetsQuery(): Builder
    {
        return Pet::query()
            ->withoutGlobalScope('shelter')
            ->where('publish_to_portal', true)
            ->where('status', 'adopted')
            ->whereNull('date_of_death')
            ->whereHas('shelter');
    }
}
