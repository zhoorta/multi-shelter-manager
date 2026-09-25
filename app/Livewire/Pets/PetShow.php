<?php

declare(strict_types=1);

namespace App\Livewire\Pets;

use App\Livewire\Pets\Concerns\ManagesSponsorshipPayments;
use App\Models\Pet;
use App\Models\PetVaccine;
use App\Models\Sickness;
use App\Models\Size;
use App\Models\Sponsorship;
use App\Models\SponsorshipPayment;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

class PetShow extends Component
{
    use ManagesSponsorshipPayments;

    public Pet $pet;

    public function mount(Pet $pet): void
    {
        abort_unless(! Auth::user()->is_admin, 403);

        $this->pet = $pet->load([
            'species', 'breed', 'cage.wing.facility', 'images', 'primaryColor', 'secondaryColor', 'furType', 'size', 'sicknesses',
            'adoptions' => fn ($query) => $query->latest('adoption_date'),
            'sponsorships' => fn ($query) => $query->latest()->with(['payments' => fn ($paymentsQuery) => $paymentsQuery->orderByDesc('payment_date')]),
            'vaccines' => fn ($query) => $query->orderByRaw('COALESCE(pet_vaccines.administered_date, pet_vaccines.due_date) desc'),
        ]);
    }

    /**
     * Sicknesses that can affect the pet's species (see sickness_species
     * pivot), each to be shown alongside whether the pet has it.
     *
     * @return Collection<int, Sickness>
     */
    #[Computed]
    public function sicknesses(): Collection
    {
        return Sickness::query()
            ->whereHas('species', fn (Builder $query) => $query->whereKey($this->pet->species_id))
            ->orderBy('name')
            ->get();
    }

    /**
     * Whether the pet's species has any sizes registered (see the sizes
     * table), used to hide the Size field entirely for species that don't
     * use it — mirrors PetForm::sizes() (see .ai/rules/pets-models.md).
     */
    #[Computed]
    public function speciesHasSizes(): bool
    {
        return Size::query()->where('species_id', $this->pet->species_id)->exists();
    }

    /**
     * Whether the pet can be announced for adoption on social media: only
     * adoptable pets still waiting for a family.
     */
    #[Computed]
    public function isShareable(): bool
    {
        return $this->pet->is_adoptable && $this->pet->status === 'available' && $this->pet->date_of_death === null;
    }

    /**
     * Public portal link included in the share caption; null when the pet
     * is not published to the portal (see Pet::publicUrl()).
     */
    #[Computed]
    public function shareUrl(): ?string
    {
        return $this->pet->publicUrl();
    }

    #[Computed]
    public function shareCaption(): string
    {
        return $this->pet->shareCaption($this->shareUrl());
    }

    /**
     * Reload the pet's sponsorships and their payments after a payment is
     * created, edited or deleted, so the sponsorship box reflects the
     * change without a full page reload.
     */
    protected function refreshSponsorships(): void
    {
        $this->pet->load([
            'sponsorships' => fn ($query) => $query->latest()->with(['payments' => fn ($paymentsQuery) => $paymentsQuery->orderByDesc('payment_date')]),
        ]);
    }

    /**
     * Sponsorship has no shelter_id of its own, so scope it transitively
     * through pet (see .ai/rules/livewire-pets.md).
     */
    protected function scopedSponsorshipQuery(): Builder
    {
        return Sponsorship::query()->where('pet_id', $this->pet->id);
    }

    /**
     * SponsorshipPayment has no shelter_id of its own, so scope it
     * transitively through sponsorship.pet (see .ai/rules/livewire-pets.md).
     */
    protected function scopedSponsorshipPaymentQuery(): Builder
    {
        return SponsorshipPayment::query()->whereHas(
            'sponsorship',
            fn (Builder $query) => $query->where('pet_id', $this->pet->id),
        );
    }

    public function deleteVaccination(int $petVaccineId): void
    {
        abort_unless(Auth::user()->canEditCurrentShelter(), 403);
        $this->scopedPetVaccineQuery()->findOrFail($petVaccineId)->delete();

        $this->refreshVaccines();

        Flux::toast(variant: 'success', text: __('Record deleted successfully'));
    }

    /**
     * PetVaccine has no shelter_id of its own, so scope it directly through
     * its own pet_id column (unlike Sponsorship/SponsorshipPayment, it does
     * have one).
     */
    protected function scopedPetVaccineQuery(): Builder
    {
        return PetVaccine::query()->where('pet_id', $this->pet->id);
    }

    /**
     * Reload the pet's vaccines after one is deleted, so the vaccinations
     * table reflects the change without navigation.
     */
    protected function refreshVaccines(): void
    {
        $this->pet->load([
            'vaccines' => fn ($query) => $query->orderByRaw('COALESCE(pet_vaccines.administered_date, pet_vaccines.due_date) desc'),
        ]);
    }

    public function render(): View
    {
        return view('livewire.pets.pet-show')->title($this->pet->name);
    }
}
