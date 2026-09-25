<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Livewire\Concerns\ShowsPublicPets;
use App\Models\Pet;
use App\Models\Shelter;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Public page of a single partner shelter: its details and contacts, plus
 * every pet it has published for adoption on the portal.
 */
class PartnerShelterShow extends Component
{
    use ShowsPublicPets, WithPagination;

    public Shelter $shelter;

    /**
     * Set only on the initial request of a shared pet link (?animal=), so
     * the view opens the details modal once the page has loaded.
     */
    protected bool $opensSharedPetOnLoad = false;

    public function mount(Shelter $shelter): void
    {
        $this->shelter = $shelter->load('region');

        $sharedPet = request()->filled('animal')
            ? $this->shelter->publishedPets()->find(request()->integer('animal'))
            : null;

        if ($sharedPet !== null) {
            $this->selectPet($sharedPet);
            $this->opensSharedPetOnLoad = true;
        }
    }

    /**
     * @return LengthAwarePaginator<int, Pet>
     */
    #[Computed]
    public function pets(): LengthAwarePaginator
    {
        return $this->shelter->publishedPets()
            ->with(['species', 'breed', 'size', 'shelter.region', 'images', 'cage:id,wing_id', 'cage.wing:id,is_foster'])
            ->orderByDesc('is_featured')
            ->latest('checkin_date')
            ->latest('id')
            ->paginate(12);
    }

    #[Layout('layouts::public')]
    public function render(): View
    {
        $logoUrl = $this->shelter->logo_path ? url(Storage::url($this->shelter->logo_path)) : null;

        // A shared pet link (?animal=) previews that pet, not the shelter, on social media.
        if ($pet = $this->selectedPet()) {
            $petImage = $pet->images->firstWhere('is_main', true) ?? $pet->images->first();

            return view('livewire.partner-shelter-show', ['opensSharedPetOnLoad' => $this->opensSharedPetOnLoad])
                ->title($pet->name.' - '.$this->shelter->name)
                ->layoutData([
                    'description' => __(':name is looking for a family!', ['name' => $pet->name]).' '.strip_tags((string) $pet->description),
                    'image' => $petImage ? url(Storage::url($petImage->image_path)) : $logoUrl,
                    'canonicalUrl' => route('shelters.show', ['shelter' => $this->shelter, 'animal' => $pet->id]),
                    'feedUrl' => route('shelters.feed', $this->shelter),
                ]);
        }

        return view('livewire.partner-shelter-show', ['opensSharedPetOnLoad' => false])
            ->title($this->shelter->name)
            ->layoutData([
                'feedUrl' => route('shelters.feed', $this->shelter),
                'description' => filled($this->shelter->description)
                    ? $this->shelter->description
                    : __(':name in :city: meet the animals waiting for adoption.', ['name' => $this->shelter->name, 'city' => $this->shelter->city]),
                'image' => $logoUrl,
                'structuredData' => $this->structuredData($logoUrl),
            ]);
    }

    /**
     * Schema.org AnimalShelter data so search engines can show the shelter's
     * address and contacts.
     *
     * @return array<string, mixed>
     */
    private function structuredData(?string $logoUrl): array
    {
        return array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'AnimalShelter',
            'name' => $this->shelter->name,
            'url' => route('shelters.show', $this->shelter),
            'description' => $this->shelter->description,
            'logo' => $logoUrl,
            'telephone' => $this->shelter->phone,
            'email' => $this->shelter->email,
            'sameAs' => $this->shelter->website,
            'address' => array_filter([
                '@type' => 'PostalAddress',
                'streetAddress' => $this->shelter->address,
                'postalCode' => $this->shelter->postal_code,
                'addressLocality' => $this->shelter->city,
                'addressRegion' => $this->shelter->region?->name,
            ]),
        ]);
    }
}
