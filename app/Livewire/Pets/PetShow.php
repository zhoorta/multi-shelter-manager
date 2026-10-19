<?php

declare(strict_types=1);

namespace App\Livewire\Pets;

use App\Livewire\Pets\Concerns\ManagesSponsorshipPayments;
use App\Models\Pet;
use App\Models\PetSickness;
use App\Models\PetTreatment;
use App\Models\PetVaccine;
use App\Models\Size;
use App\Models\Sponsorship;
use App\Models\SponsorshipPayment;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;

class PetShow extends Component
{
    use ManagesSponsorshipPayments;

    public Pet $pet;

    /**
     * The visible section of the page: 'summary', 'health' or 'people'
     * (adoptions and sponsorships). Kept in the URL so a redirect back from
     * a health or adoption form lands on the right tab.
     */
    #[Url(as: 'tab', except: 'summary')]
    public string $tab = 'summary';

    public function mount(Pet $pet): void
    {
        abort_unless(! Auth::user()->is_admin, 403);

        $this->pet = $pet->load([
            'species', 'breed', 'cage.wing.facility', 'cage.volunteer', 'images', 'primaryColor', 'secondaryColor', 'furType', 'size',
            'adoptions' => fn ($query) => $query->latest('adoption_date'),
            'sponsorships' => fn ($query) => $query->latest()->with(['payments' => fn ($paymentsQuery) => $paymentsQuery->orderByDesc('payment_date')]),
            'vaccines' => fn ($query) => $query->orderByRaw('COALESCE(pet_vaccines.administered_date, pet_vaccines.due_date) desc'),
            'treatments' => fn ($query) => $query->with('treatment')->orderByRaw('COALESCE(administered_date, due_date) desc'),
            'sicknesses' => fn ($query) => $query->orderByPivot('diagnosed_at', 'desc'),
            'clinicalPhotos',
        ]);
    }

    /**
     * The tabs the current user can open. The people tab holds the adopter
     * and sponsor boxes and the buttons to register them, so it is missing
     * for viewers (see .ai/rules/pets.md on viewers and personal data).
     *
     * @return list<string>
     */
    #[Computed]
    public function availableTabs(): array
    {
        $tabs = ['summary', 'health'];

        if (Auth::user()->canEditCurrentShelter()) {
            $tabs[] = 'people';
        }

        return $tabs;
    }

    #[Computed]
    public function activeTab(): string
    {
        return in_array($this->tab, $this->availableTabs, true) ? $this->tab : 'summary';
    }

    public function selectTab(string $tab): void
    {
        $this->tab = in_array($tab, $this->availableTabs, true) ? $tab : 'summary';
    }

    /**
     * Adoptions plus, when the shelter uses them, sponsorships.
     */
    public function peopleCount(): int
    {
        return $this->pet->adoptions->count()
            + (Auth::user()->currentShelterHasModule('sponsorships') ? $this->pet->sponsorships->count() : 0);
    }

    /**
     * Records listed in the health tab: diagnoses plus, when the shelter
     * uses health records, vaccinations, treatments and clinical photos.
     */
    public function healthCount(): int
    {
        return $this->pet->sicknesses->count()
            + (Auth::user()->currentShelterHasModule('health_records') ? $this->pet->vaccines->count() + $this->pet->treatments->count() + $this->pet->clinicalPhotos->count() : 0);
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

    /**
     * Ids of the pet's vaccinations whose due date is still open (see
     * PetVaccine::pending()), used to highlight overdue and due-soon dates.
     *
     * @return array<int, int>
     */
    #[Computed]
    public function pendingVaccinationIds(): array
    {
        return $this->scopedPetVaccineQuery()
            ->pending()
            ->get(['id'])
            ->map(fn (PetVaccine $petVaccine): int => $petVaccine->id)
            ->all();
    }

    public function deleteVaccination(int $petVaccineId): void
    {
        abort_unless(Auth::user()->canEditArea('health'), 403);
        $this->scopedPetVaccineQuery()->findOrFail($petVaccineId)->delete();

        $this->refreshVaccines();
        unset($this->pendingVaccinationIds);

        Flux::toast(variant: 'success', text: __('Record deleted successfully'));
    }

    /**
     * PetVaccine has no shelter_id of its own, so scope it directly through
     * its own pet_id column (unlike Sponsorship/SponsorshipPayment, it does
     * have one).
     *
     * @return Builder<PetVaccine>
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

    /**
     * Ids of the pet's treatments whose due date is still open, used to
     * highlight overdue and due-soon dates.
     *
     * @return array<int, int>
     */
    #[Computed]
    public function pendingTreatmentIds(): array
    {
        return PetTreatment::query()
            ->where('pet_id', $this->pet->id)
            ->pending()
            ->pluck('id')
            ->all();
    }

    public function deleteTreatment(int $petTreatmentId): void
    {
        abort_unless(Auth::user()->canEditArea('health'), 403);
        PetTreatment::query()->where('pet_id', $this->pet->id)->findOrFail($petTreatmentId)->delete();

        $this->pet->load([
            'treatments' => fn ($query) => $query->with('treatment')->orderByRaw('COALESCE(administered_date, due_date) desc'),
        ]);
        unset($this->pendingTreatmentIds);

        Flux::toast(variant: 'success', text: __('Record deleted successfully'));
    }

    /**
     * Soft delete only: the files stay on the private disk so an
     * administrator can restore the photo.
     */
    public function deleteClinicalPhoto(int $photoId): void
    {
        abort_unless(Auth::user()->canEditArea('health'), 403);
        $this->pet->clinicalPhotos()->findOrFail($photoId)->delete();

        $this->pet->load('clinicalPhotos');

        Flux::toast(variant: 'success', text: __('Record deleted successfully'));
    }

    public function deleteDiagnosis(int $petSicknessId): void
    {
        abort_unless(Auth::user()->canEditArea('health'), 403);
        $this->scopedPetSicknessQuery()->findOrFail($petSicknessId)->delete();

        $this->refreshSicknesses();

        Flux::toast(variant: 'success', text: __('Record deleted successfully'));
    }

    /**
     * PetSickness has no shelter_id of its own, so scope it directly through
     * its own pet_id column, like PetVaccine.
     *
     * @return Builder<PetSickness>
     */
    protected function scopedPetSicknessQuery(): Builder
    {
        return PetSickness::query()->where('pet_id', $this->pet->id);
    }

    /**
     * Reload the pet's diagnoses after one is deleted, so the diagnoses
     * table reflects the change without navigation.
     */
    protected function refreshSicknesses(): void
    {
        $this->pet->load([
            'sicknesses' => fn ($query) => $query->orderByPivot('diagnosed_at', 'desc'),
        ]);
    }

    public function render(): View
    {
        return view('livewire.pets.pet-show')->title($this->pet->name);
    }
}
