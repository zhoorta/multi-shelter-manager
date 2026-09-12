---
paths:
  - 'app/Livewire/Pets/PetForm.php,resources/views/livewire/pets/pet-form.blade.php,app/Models/Pet.php'
---

# Pets Models

## Pet size is species-scoped and optional, mirrors breed dropdown but hides when species has no sizes
Pet.size_id (nullable FK to sizes, RESTRICT) is now wired into PetForm: petSizeId property, PetForm::sizes() computed (same shape as breeds(), scoped by petSpeciesId, empty Collection when species has none), reset in updatedPetSpeciesId() alongside breed/sicknesses, validated as nullable + Rule::exists('sizes','id')->where('species_id', ...). pet-form.blade.php renders the "Size" <flux:select> right after Fur Type only when $this->sizes->isNotEmpty() — species without any Size rows (e.g. Cat, unless sizes are added for it) show no size field at all, with a "No Size Assigned" null option when it is shown. Pet::size() belongsTo added alongside the existing species/breed/furType relations. See [[admin]] for the ManageSizes CRUD page that manages the sizes table itself.
