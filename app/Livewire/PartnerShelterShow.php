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

    public function mount(Shelter $shelter): void
    {
        $this->shelter = $shelter->load('region');
    }

    /**
     * @return LengthAwarePaginator<int, Pet>
     */
    #[Computed]
    public function pets(): LengthAwarePaginator
    {
        return $this->shelter->publishedPets()
            ->with(['species', 'breed', 'size', 'shelter.region', 'images'])
            ->orderByDesc('is_featured')
            ->latest('checkin_date')
            ->latest('id')
            ->paginate(12);
    }

    #[Layout('layouts::public')]
    public function render(): View
    {
        $logoUrl = $this->shelter->logo_path ? url(Storage::url($this->shelter->logo_path)) : null;

        return view('livewire.partner-shelter-show')
            ->title($this->shelter->name)
            ->layoutData([
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
