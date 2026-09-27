<?php

declare(strict_types=1);

namespace App\Livewire\Pets;

use App\Models\Breed;
use App\Models\Cage;
use App\Models\Color;
use App\Models\FurType;
use App\Models\Pet;
use App\Models\PetImage;
use App\Models\Size;
use App\Models\Species;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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

    public bool $petIsPureBreed = false;

    public ?int $petPrimaryColorId = null;

    public ?int $petSecondaryColorId = null;

    public ?int $petFurTypeId = null;

    public ?int $petSizeId = null;

    public string $petGender = '';

    public string $petBirthDate = '';

    public string $petDeathDate = '';

    public string $petChip = '';

    public bool $petIsNeutered = false;

    public string $petNeuteredAt = '';

    /** '' = unknown, '1' = neutered by the shelter, '0' = before arrival. */
    public string $petNeuteredByShelter = '';

    public string $petNeuteringStatus = 'pending';

    public string $petNeuteringScheduledAt = '';

    public string $petNeuteringNotes = '';

    public bool $petIsAdoptable = true;

    public bool $petIsSponsorable = true;

    public bool $petPublishToPortal = false;

    public bool $petIsFeatured = false;

    public ?int $petCageId = null;

    public string $petCheckinDate = '';

    public string $petDescription = '';

    public string $petNotes = '';

    public string $petClinicalNotes = '';

    /**
     * @var array<int, TemporaryUploadedFile>
     */
    public array $petPhotos = [];

    public function mount(?Pet $pet = null): void
    {
        abort_unless(! Auth::user()->is_admin, 403);
        abort_unless(Auth::user()->canEditCurrentShelter(), 403);

        if ($pet === null) {
            if ($this->lockedSpeciesId !== '') {
                $this->petSpeciesId = Species::query()->whereKey($this->lockedSpeciesId)->value('id');
                $this->petBreedId = $this->petSpeciesId !== null
                    ? $this->defaultBreedIdFor($this->petSpeciesId)
                    : null;
            }

            return;
        }

        $this->pet = $pet;
        $this->petName = $pet->name;
        $this->petSpeciesId = $pet->species_id;
        $this->petBreedId = $pet->breed_id;
        $this->petIsPureBreed = (bool) $pet->is_pure_breed;
        $this->petPrimaryColorId = $pet->primary_color_id;
        $this->petSecondaryColorId = $pet->secondary_color_id;
        $this->petFurTypeId = $pet->fur_type_id;
        $this->petSizeId = $pet->size_id;
        $this->petGender = $pet->gender;
        $this->petBirthDate = (string) $pet->birth_date?->format('Y-m-d');
        $this->petDeathDate = (string) $pet->date_of_death?->format('Y-m-d');
        $this->petChip = (string) $pet->chip;
        $this->petIsNeutered = (bool) $pet->is_neutered;
        $this->petNeuteredAt = (string) $pet->neutered_at?->format('Y-m-d');
        $this->petNeuteredByShelter = $pet->neutered_by_shelter === null ? '' : (string) (int) $pet->neutered_by_shelter;
        $this->petNeuteringStatus = (string) $pet->neutering_status;
        $this->petNeuteringScheduledAt = (string) $pet->neutering_scheduled_at?->format('Y-m-d');
        $this->petNeuteringNotes = (string) $pet->neutering_notes;
        $this->petIsAdoptable = (bool) $pet->is_adoptable;
        $this->petIsSponsorable = (bool) $pet->is_sponsorable;
        $this->petPublishToPortal = (bool) $pet->publish_to_portal;
        $this->petIsFeatured = (bool) $pet->is_featured;
        $this->petCageId = $pet->cage_id;
        $this->petCheckinDate = (string) $pet->checkin_date?->format('Y-m-d');
        $this->petDescription = (string) $pet->description;
        $this->petNotes = (string) $pet->notes;
        $this->petClinicalNotes = (string) $pet->clinical_notes;
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
     * Sizes of the currently selected species. Species' default scope
     * excludes soft-deleted rows, so a trashed species never surfaces
     * sizes here (see .ai/rules/admin.md). Empty when the species has
     * no sizes registered, in which case the form hides the field.
     *
     * @return Collection<int, Size>
     */
    #[Computed]
    public function sizes(): Collection
    {
        if ($this->petSpeciesId === null) {
            return new Collection;
        }

        return Size::query()
            ->where('species_id', $this->petSpeciesId)
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
     * Cages available to house the pet, grouped hierarchically for the
     * form's select: Facility -> Wing -> Cage. Each cage is annotated with
     * available_space and availability_color (green/yellow/red) so the
     * select can flag how full it is. Once a species is chosen, only cages
     * destined to it (or to any species) are offered.
     *
     * @return Collection<int, Cage>
     */
    #[Computed]
    public function cages(): Collection
    {
        return $this->scopedCageQuery()
            ->when($this->petSpeciesId !== null, fn (Builder $query) => $query->accepting($this->petSpeciesId))
            ->with('wing.facility')
            ->withCount(['pets as active_pets_count' => function (Builder $query): void {
                $query->where('status', '!=', 'adopted')->whereNull('date_of_death');

                if ($this->pet !== null) {
                    $query->whereKeyNot($this->pet->id);
                }
            }])
            ->get()
            ->each(function (Cage $cage): void {
                $availableSpace = max(0, $cage->capacity - $cage->active_pets_count);

                $cage->available_space = $availableSpace;
                $cage->availability_color = match (true) {
                    $availableSpace <= 0 => 'red',
                    $cage->active_pets_count > $cage->capacity * 0.8 => 'yellow',
                    default => 'green',
                };
            })
            ->sortBy(fn (Cage $cage) => sprintf(
                '%s|%s|%s',
                $cage->wing->facility->name ?? '',
                $cage->wing->name,
                $cage->code,
            ))
            ->values();
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

    /**
     * Switching an already registered pet to neutered means the shelter just
     * had it done, so suggest the scheduled date (or today) and the shelter.
     * New pets arrive with unknown history, so nothing is suggested for them.
     */
    public function updatedPetIsNeutered(): void
    {
        if (! $this->petIsNeutered || $this->pet === null || $this->pet->is_neutered) {
            return;
        }

        if ($this->petNeuteredAt === '') {
            $this->petNeuteredAt = $this->petNeuteringScheduledAt !== '' ? $this->petNeuteringScheduledAt : now()->toDateString();
        }

        if ($this->petNeuteredByShelter === '') {
            $this->petNeuteredByShelter = '1';
        }
    }

    public function updatedPetSpeciesId(): void
    {
        $this->petBreedId = $this->pet === null && $this->petSpeciesId !== null
            ? $this->defaultBreedIdFor($this->petSpeciesId)
            : null;
        $this->petIsPureBreed = false;
        $this->petSizeId = null;

        unset($this->breeds, $this->sizes, $this->currentSpecies, $this->cages);

        if ($this->petCageId !== null && ! $this->cages->contains('id', $this->petCageId)) {
            $this->petCageId = null;
        }
    }

    /**
     * The default (SRD) breed id for a species, when one is registered.
     */
    protected function defaultBreedIdFor(int $speciesId): ?int
    {
        return Breed::query()->where('species_id', $speciesId)->where('is_default', true)->value('id');
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
            'petIsPureBreed' => ['boolean'],
            'petPrimaryColorId' => ['nullable', 'integer', 'exists:colors,id'],
            'petSecondaryColorId' => ['nullable', 'integer', 'exists:colors,id'],
            'petFurTypeId' => ['nullable', 'integer', 'exists:fur_types,id'],
            'petSizeId' => [
                'nullable',
                'integer',
                Rule::exists('sizes', 'id')->where('species_id', $this->petSpeciesId),
            ],
            'petGender' => ['required', 'in:male,female'],
            'petBirthDate' => ['nullable', 'date'],
            'petDeathDate' => ['nullable', 'date'],
            'petChip' => ['nullable', 'string', 'max:255'],
            'petIsNeutered' => ['boolean'],
            'petNeuteredAt' => ['nullable', 'date', 'before_or_equal:today'],
            'petNeuteredByShelter' => ['nullable', 'in:0,1'],
            'petNeuteringStatus' => ['nullable', Rule::in(Pet::NEUTERING_STATUSES)],
            'petNeuteringScheduledAt' => [
                Rule::requiredIf(! $this->petIsNeutered && $this->petNeuteringStatus === 'scheduled'),
                'nullable',
                'date',
            ],
            'petNeuteringNotes' => ['nullable', 'string', 'max:255'],
            'petIsAdoptable' => ['boolean'],
            'petIsSponsorable' => ['boolean'],
            'petPublishToPortal' => ['boolean'],
            'petIsFeatured' => ['boolean'],
            'petCageId' => [
                'nullable',
                'integer',
                Rule::exists('cages', 'id')->where(fn ($query) => $query->where('species_id', $this->petSpeciesId)->orWhereNull('species_id')),
            ],
            'petCheckinDate' => ['nullable', 'date'],
            'petDescription' => ['nullable', 'string'],
            'petNotes' => ['nullable', 'string'],
            'petClinicalNotes' => ['nullable', 'string'],
            'petPhotos' => ['nullable', 'array'],
            'petPhotos.*' => ['image', 'max:2048'],
        ], [], [
            'petName' => __('Name'),
            'petSpeciesId' => __('Species'),
            'petBreedId' => __('Breed'),
            'petIsPureBreed' => __('Pure breed'),
            'petPrimaryColorId' => __('Primary Color'),
            'petSecondaryColorId' => __('Secondary Color'),
            'petFurTypeId' => __('Fur Type'),
            'petSizeId' => __('Size'),
            'petGender' => __('Gender'),
            'petBirthDate' => __('Birth Date'),
            'petDeathDate' => __('Death Date'),
            'petChip' => __('Microchip / Chip'),
            'petIsNeutered' => __('Is Neutered'),
            'petNeuteredAt' => __('Neutering Date'),
            'petNeuteredByShelter' => __('Neutered by'),
            'petNeuteringStatus' => __('Neutering Status'),
            'petNeuteringScheduledAt' => __('Scheduled Date'),
            'petNeuteringNotes' => __('Neutering Notes'),
            'petIsAdoptable' => __('Is Adoptable'),
            'petIsSponsorable' => __('Is Sponsorable'),
            'petPublishToPortal' => __('Publish to Portal'),
            'petIsFeatured' => __('Is Featured'),
            'petCageId' => __('Cage'),
            'petCheckinDate' => __('Checkin Date'),
            'petDescription' => __('Description'),
            'petNotes' => __('Notes'),
            'petClinicalNotes' => __('Clinical Notes'),
            'petPhotos.*' => __('Photo'),
        ]);

        // Cage has no shelter_id of its own, so the exists rule above can't
        // enforce tenancy — re-fetch through the scoped query (transitive
        // through wing.facility) so a cage id from another shelter 404s
        // instead of silently housing the pet there (see .ai/rules/facilities.md).
        $cage = $validated['petCageId'] !== null
            ? $this->scopedCageQuery()->findOrFail($validated['petCageId'])
            : null;

        $isEditing = $this->pet !== null;

        $petAttributes = [
            'cage_id' => $cage?->id,
            'species_id' => $validated['petSpeciesId'],
            'breed_id' => $validated['petBreedId'],
            'is_pure_breed' => $validated['petIsPureBreed'],
            'primary_color_id' => $validated['petPrimaryColorId'],
            'secondary_color_id' => $validated['petSecondaryColorId'],
            'fur_type_id' => $validated['petFurTypeId'],
            'size_id' => $validated['petSizeId'],
            'name' => $validated['petName'],
            'chip' => $validated['petChip'] !== '' ? $validated['petChip'] : null,
            'gender' => $validated['petGender'],
            'birth_date' => $validated['petBirthDate'] !== '' ? $validated['petBirthDate'] : null,
            'date_of_death' => $validated['petDeathDate'] !== '' ? $validated['petDeathDate'] : null,
            'checkin_date' => $validated['petCheckinDate'] !== '' ? $validated['petCheckinDate'] : null,
            'description' => Pet::sanitizeDescription($validated['petDescription']),
            'notes' => $validated['petNotes'] !== '' ? $validated['petNotes'] : null,
            'clinical_notes' => $validated['petClinicalNotes'] !== '' ? $validated['petClinicalNotes'] : null,
            'is_neutered' => $validated['petIsNeutered'],
            ...$this->neuteringAttributes($validated),
            'is_adoptable' => $validated['petIsAdoptable'],
            'is_sponsorable' => $validated['petIsSponsorable'],
            'publish_to_portal' => $validated['petPublishToPortal'],
            'is_featured' => $validated['petIsFeatured'],
        ];

        DB::transaction(function () use ($petAttributes, $isEditing): void {
            if ($isEditing) {
                $this->pet->fill($petAttributes);
                $petAttributes['status'] = $this->pet->determineStatus();
                $this->pet->update($petAttributes);
            } else {
                $petAttributes['status'] = (new Pet($petAttributes))->determineStatus();
                $this->pet = Pet::query()->create([...$petAttributes, 'ref' => '']);
                $this->pet->update(['ref' => $this->generatePetRef($this->pet->id)]);
            }

            $this->storeUploadedPetPhotos($this->pet);
        });

        Flux::toast(
            variant: 'success',
            text: $isEditing ? __('Record updated successfully') : __('Record created successfully'),
        );

        $this->redirect(route('pets.show', $this->pet), navigate: true);
    }

    /**
     * Generate the pet's reference from its id: PET00001, PET00002, etc.
     * Only called on create, once the pet's id is known.
     */
    protected function generatePetRef(int $petId): string
    {
        return 'PET'.str_pad((string) $petId, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Neutering details that apply to the switch's current state: a neutered
     * pet keeps its date and who did it, one that isn't keeps where it
     * stands (with a date only when scheduled). The other side is cleared.
     *
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    protected function neuteringAttributes(array $validated): array
    {
        $isNeutered = $validated['petIsNeutered'];
        $status = $validated['petNeuteringStatus'] ?? '';

        return [
            'neutered_at' => $isNeutered && $validated['petNeuteredAt'] !== '' ? $validated['petNeuteredAt'] : null,
            'neutered_by_shelter' => $isNeutered && $validated['petNeuteredByShelter'] !== '' ? $validated['petNeuteredByShelter'] === '1' : null,
            'neutering_status' => ! $isNeutered && $status !== '' ? $status : null,
            'neutering_scheduled_at' => ! $isNeutered && $status === 'scheduled' ? $validated['petNeuteringScheduledAt'] : null,
            'neutering_notes' => ! $isNeutered && $validated['petNeuteringNotes'] !== '' ? $validated['petNeuteringNotes'] : null,
        ];
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
                'image_path' => $photo->store('pets'),
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

        Storage::delete($image->image_path);
        $image->delete();

        unset($this->petImages);
    }

    /**
     * Cage has no shelter scope of its own, so scope it transitively
     * through its wing's facility (see .ai/rules/facilities.md).
     *
     * @return Builder<Cage>
     */
    protected function scopedCageQuery(): Builder
    {
        return Cage::query()->whereHas(
            'wing.facility',
            fn (Builder $query) => $query->where('shelter_id', Auth::user()->current_shelter_id),
        );
    }

    /**
     * PetImage has no shelter scope of its own, so scope it transitively
     * through its pet (mirrors scopedCageQuery(); see .ai/rules/facilities.md).
     */
    protected function scopedPetImageQuery(): Builder
    {
        return PetImage::query()->whereHas(
            'pet',
            fn (Builder $query) => $query->where('shelter_id', Auth::user()->current_shelter_id),
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
