<?php

declare(strict_types=1);

namespace App\Livewire\Concerns;

use App\Models\Pet;
use Flux\Flux;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Computed;

/**
 * Public portal pet listing and the "public-pet-details" modal, shared by the
 * Welcome page and the public shelter page. Pair with the
 * livewire/partials/public-pet-card and public-pet-details-modal views.
 */
trait ShowsPublicPets
{
    public ?int $selectedPetId = null;

    /**
     * Open a published pet's details and count it as one portal view. The
     * count is incremented on the base query so a view does not touch the
     * pet's updated_at/updated_by audit columns.
     */
    public function showPet(int $petId): void
    {
        $this->selectPet($this->publicPetsQuery()->findOrFail($petId));

        Flux::modal('public-pet-details')->show();
    }

    /**
     * Select a published pet for the details modal and count the view.
     */
    protected function selectPet(Pet $pet): void
    {
        $this->selectedPetId = $pet->id;

        Pet::query()->withoutGlobalScopes()->whereKey($this->selectedPetId)->toBase()->increment('view_count');
    }

    #[Computed]
    public function selectedPet(): ?Pet
    {
        if ($this->selectedPetId === null) {
            return null;
        }

        return $this->publicPetsQuery()
            ->with(['species', 'breed', 'size', 'furType', 'shelter.region', 'images'])
            ->find($this->selectedPetId);
    }

    /**
     * Pets every shelter has explicitly published for adoption. The shelter
     * global scope is removed on purpose: this is a cross-shelter public
     * portal, and a logged-in staff member visiting it must see the same
     * list as a guest, not only their own shelter's pets.
     *
     * @return Builder<Pet>
     */
    protected function publicPetsQuery(): Builder
    {
        return Pet::query()
            ->withoutGlobalScope('shelter')
            ->publishedToPortal();
    }
}
