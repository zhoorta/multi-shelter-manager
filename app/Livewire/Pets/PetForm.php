<?php

declare(strict_types=1);

namespace App\Livewire\Pets;

use App\Models\Breed;
use App\Models\Cage;
use App\Models\Color;
use App\Models\FurType;
use App\Models\Pet;
use App\Models\PetImage;
use App\Models\Sickness;
use App\Models\Species;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class PetForm extends Component
{
    use WithFileUploads;

    public ?Pet $pet = null;

    /**
     * Species id carried over from a species-scoped pets list (see
     * ManagePets::selectedSpecies) so a pet created from there starts
     * locked to that species instead of showing every species.
     */
    #[Url(as: 'species')]
    public string $lockedSpeciesId = '';

    public string $petName = '';

    public ?int $petSpeciesId = null;

    public ?int $petBreedId = null;

    public ?int $petPrimaryColorId = null;

    public ?int $petSecondaryColorId = null;

    public ?int $petFurTypeId = null;

    public string $petGender = '';

    public string $petBirthDate = '';

    public string $petChip = '';

    public bool $petIsNeutered = false;

    /**
     * @var array<int, int>
     */
    public array $petSicknessIds = [];

    public bool $petIsAdoptable = true;

    public bool $petIsSponsorable = true;

    public string $petStatus = 'available';

    public ?int $petCageId = null;

    /**
     * @var array<int, TemporaryUploadedFile>
     */
    public array $petPhotos = [];

    public function mount(?Pet $pet = null): void
    {
        abort_unless(in_array(Auth::user()->role, ['manager', 'staff'], true), 403);

        if ($pet === null) {
            if ($this->lockedSpeciesId !== '') {
                $this->petSpeciesId = Species::query()->whereKey($this->lockedSpeciesId)->value('id');
            }

            return;
        }

        $this->pet = $pet;
        $this->petName = $pet->name;
        $this->petSpeciesId = $pet->species_id;
        $this->petBreedId = $pet->breed_id;
        $this->petPrimaryColorId = $pet->primary_color_id;
        $this->petSecondaryColorId = $pet->secondary_color_id;
        $this->petFurTypeId = $pet->fur_type_id;
        $this->petGender = $pet->gender;
        $this->petBirthDate = (string) $pet->birth_date?->format('Y-m-d');
        $this->petChip = (string) $pet->chip;
        $this->petIsNeutered = (bool) $pet->is_neutered;
        $this->petSicknessIds = $pet->sicknesses()->pluck('sicknesses.id')->all();
        $this->petIsAdoptable = (bool) $pet->is_adoptable;
        $this->petIsSponsorable = (bool) $pet->is_sponsorable;
        $this->petStatus = $pet->status;
        $this->petCageId = $pet->cage_id;
    }

    /**
     * The species backing the current selection, used to label the form
     * and link back to its species-scoped pets list.
     */
    #[Computed]
    public function currentSpecies(): ?Species
    {
        return $this->petSpeciesId !== null
            ? Species::query()->find($this->petSpeciesId)
            : null;
    }

    /**
     * Breeds of the currently selected species. Species' default scope
     * excludes soft-deleted rows, so a trashed species never surfaces
     * breeds here (see .ai/rules/admin.md).
     *
     * @return Collection<int, Breed>
     */
    #[Computed]
    public function breeds(): Collection
    {
        if ($this->petSpeciesId === null) {
            return new Collection;
        }

        return Breed::query()
            ->where('species_id', $this->petSpeciesId)
            ->orderBy('name')
            ->get();
    }

    /**
     * Sicknesses that can affect the currently selected species (see
     * sickness_species pivot).
     *
     * @return Collection<int, Sickness>
     */
    #[Computed]
    public function sicknesses(): Collection
    {
        if ($this->petSpeciesId === null) {
            return new Collection;
        }

        return Sickness::query()
            ->whereHas('species', fn (Builder $query) => $query->whereKey($this->petSpeciesId))
            ->orderBy('name')
            ->get();
    }

    /**
     * @return Collection<int, Color>
     */
    #[Computed]
    public function colors(): Collection
    {
        return Color::query()->orderBy('name')->get();
    }

    /**
     * @return Collection<int, FurType>
     */
    #[Computed]
    public function furTypes(): Collection
    {
        return FurType::query()->orderBy('name')->get();
    }

    /**
     * @return Collection<int, Cage>
     */
    #[Computed]
    public function cages(): Collection
    {
        return $this->scopedCageQuery()->with('wing')->orderBy('code')->get();
    }

    /**
     * Photos already saved for the pet being edited, main photo first.
     *
     * @return Collection<int, PetImage>
     */
    #[Computed]
    public function petImages(): Collection
    {
        if ($this->pet === null) {
            return new Collection;
        }

        return $this->scopedPetImageQuery()
            ->where('pet_id', $this->pet->id)
            ->orderByDesc('is_main')
            ->orderBy('id')
            ->get();
    }

    public function updatedPetSpeciesId(): void
    {
        $this->petBreedId = null;
        $this->petSicknessIds = [];

        unset($this->breeds, $this->sicknesses);
    }

    public function toggleSickness(int $sicknessId): void
    {
        if (in_array($sicknessId, $this->petSicknessIds, true)) {
            $this->petSicknessIds = array_values(array_diff($this->petSicknessIds, [$sicknessId]));

            return;
        }

        $this->petSicknessIds[] = $sicknessId;
    }

    public function savePet(): void
    {
        $validated = $this->validate([
            'petName' => ['required', 'string', 'max:255'],
            'petSpeciesId' => ['required', 'integer', 'exists:species,id'],
            'petBreedId' => [
                'required',
                'integer',
                Rule::exists('breeds', 'id')->where('species_id', $this->petSpeciesId),
            ],
            'petPrimaryColorId' => ['nullable', 'integer', 'exists:colors,id'],
            'petSecondaryColorId' => ['nullable', 'integer', 'exists:colors,id'],
            'petFurTypeId' => ['nullable', 'integer', 'exists:fur_types,id'],
            'petGender' => ['required', 'in:male,female,unknown'],
            'petBirthDate' => ['nullable', 'date'],
            'petChip' => ['nullable', 'string', 'max:255'],
            'petStatus' => ['required', 'in:available,quarantine,adopted,medical'],
            'petIsNeutered' => ['boolean'],
            'petSicknessIds' => ['array'],
            'petSicknessIds.*' => [
                'integer',
                Rule::exists('sickness_species', 'sickness_id')->where('species_id', $this->petSpeciesId),
            ],
            'petIsAdoptable' => ['boolean'],
            'petIsSponsorable' => ['boolean'],
            'petCageId' => ['nullable', 'integer', 'exists:cages,id'],
            'petPhotos' => ['nullable', 'array'],
            'petPhotos.*' => ['image', 'max:2048'],
        ], [], [
            'petName' => __('Name'),
            'petSpeciesId' => __('Species'),
            'petBreedId' => __('Breed'),
            'petPrimaryColorId' => __('Primary Color'),
            'petSecondaryColorId' => __('Secondary Color'),
            'petFurTypeId' => __('Fur Type'),
            'petGender' => __('Gender'),
            'petBirthDate' => __('Birth Date'),
            'petChip' => __('Microchip / Chip'),
            'petStatus' => __('Status'),
            'petIsNeutered' => __('Is Neutered'),
            'petSicknessIds.*' => __('Sicknesses'),
            'petIsAdoptable' => __('Is Adoptable'),
            'petIsSponsorable' => __('Is Sponsorable'),
            'petCageId' => __('Cage'),
            'petPhotos.*' => __('Photo'),
        ]);

        // Cage has no shelter_id of its own, so the exists rule above can't
        // enforce tenancy — re-fetch through the scoped query (transitive
        // through wing) so a cage id from another shelter 404s instead of
        // silently housing the pet there (see .ai/rules/wings.md).
        $cage = $validated['petCageId'] !== null
            ? $this->scopedCageQuery()->findOrFail($validated['petCageId'])
            : null;

        $isEditing = $this->pet !== null;

        $petAttributes = [
            'ref' => $isEditing ? $this->pet->ref : $this->generatePetRef(),
            'cage_id' => $cage?->id,
            'species_id' => $validated['petSpeciesId'],
            'breed_id' => $validated['petBreedId'],
            'primary_color_id' => $validated['petPrimaryColorId'],
            'secondary_color_id' => $validated['petSecondaryColorId'],
            'fur_type_id' => $validated['petFurTypeId'],
            'name' => $validated['petName'],
            'chip' => $validated['petChip'] !== '' ? $validated['petChip'] : null,
            'gender' => $validated['petGender'],
            'birth_date' => $validated['petBirthDate'] !== '' ? $validated['petBirthDate'] : null,
            'status' => $validated['petStatus'],
            'is_neutered' => $validated['petIsNeutered'],
            'is_adoptable' => $validated['petIsAdoptable'],
            'is_sponsorable' => $validated['petIsSponsorable'],
        ];

        DB::transaction(function () use ($petAttributes, $isEditing, $validated): void {
            if ($isEditing) {
                $this->pet->update($petAttributes);
            } else {
                $this->pet = Pet::query()->create($petAttributes);
            }

            $this->syncPetSicknesses($this->pet, $validated['petSicknessIds'] ?? []);
            $this->storeUploadedPetPhotos($this->pet);
        });

        Flux::toast(
            variant: 'success',
            text: $isEditing ? __('Record updated successfully') : __('Record created successfully'),
        );

        $this->redirect(route('pets.show', $this->pet), navigate: true);
    }

    /**
     * Generate a unique, human-readable reference for a newly created pet.
     * pets.ref has no uniqueness constraint of its own, so uniqueness is
     * enforced here before it's assigned.
     */
    protected function generatePetRef(): string
    {
        do {
            $ref = 'PET-'.strtoupper(Str::random(8));
        } while (Pet::withTrashed()->where('ref', $ref)->exists());

        return $ref;
    }

    /**
     * Toggle the pet's diagnosed sicknesses: newly checked ids are attached
     * to pet_sickness with a fresh diagnosis, newly unchecked ids are
     * detached, and already-attached ids are left untouched so an existing
     * diagnosis's status/treatment_notes aren't reset by the toggle.
     *
     * @param  array<int, int>  $sicknessIds
     */
    protected function syncPetSicknesses(Pet $pet, array $sicknessIds): void
    {
        $currentIds = $pet->sicknesses()->pluck('sicknesses.id')->all();

        $idsToAttach = array_diff($sicknessIds, $currentIds);
        $idsToDetach = array_diff($currentIds, $sicknessIds);

        foreach ($idsToAttach as $sicknessId) {
            $pet->sicknesses()->attach($sicknessId, [
                'diagnosed_at' => now()->toDateString(),
                'status' => 'active',
            ]);
        }

        if ($idsToDetach !== []) {
            $pet->sicknesses()->detach($idsToDetach);
        }
    }

    /**
     * Store every newly selected upload as an additional photo for the pet.
     * If the pet doesn't already have a main photo, the first upload in the
     * batch is flagged as main; the rest are added as extra photos.
     */
    protected function storeUploadedPetPhotos(Pet $pet): void
    {
        if ($this->petPhotos === []) {
            return;
        }

        $hasMainImage = $pet->images()->where('is_main', true)->exists();

        foreach ($this->petPhotos as $photo) {
            PetImage::query()->create([
                'pet_id' => $pet->id,
                'image_path' => $photo->store('pets', 'public'),
                'is_main' => ! $hasMainImage,
            ]);

            $hasMainImage = true;
        }
    }

    public function setMainPetImage(int $imageId): void
    {
        $image = $this->scopedPetImageQuery()->findOrFail($imageId);

        DB::transaction(function () use ($image): void {
            PetImage::query()->where('pet_id', $image->pet_id)->update(['is_main' => false]);
            $image->update(['is_main' => true]);
        });

        unset($this->petImages);
    }

    public function deletePetImage(int $imageId): void
    {
        $image = $this->scopedPetImageQuery()->findOrFail($imageId);

        Storage::disk('public')->delete($image->image_path);
        $image->delete();

        unset($this->petImages);
    }

    /**
     * Cage has no shelter scope of its own, so scope it transitively
     * through its wing (see .ai/rules/wings.md).
     */
    protected function scopedCageQuery(): Builder
    {
        return Cage::query()->whereHas(
            'wing',
            fn (Builder $query) => $query->where('shelter_id', Auth::user()->shelter_id),
        );
    }

    /**
     * PetImage has no shelter scope of its own, so scope it transitively
     * through its pet (mirrors scopedCageQuery(); see .ai/rules/wings.md).
     */
    protected function scopedPetImageQuery(): Builder
    {
        return PetImage::query()->whereHas(
            'pet',
            fn (Builder $query) => $query->where('shelter_id', Auth::user()->shelter_id),
        );
    }

    public function render(): View
    {
        return view('livewire.pets.pet-form')->title(
            $this->pet !== null
                ? __('Edit').' — '.$this->pet->name
                : __('Create').' — '.($this->currentSpecies?->name_plural ?? __('Pets')),
        );
    }
}
