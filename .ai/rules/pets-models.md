---
paths:
  - 'app/Livewire/Pets/PetForm.php,resources/views/livewire/pets/pet-form.blade.php,app/Models/Pet.php'
---

# Pets Models

## Pet size is species-scoped and optional, mirrors breed dropdown but hides when species has no sizes
Pet.size_id (nullable FK to sizes, RESTRICT) is now wired into PetForm: petSizeId property, PetForm::sizes() computed (same shape as breeds(), scoped by petSpeciesId, empty Collection when species has none), reset in updatedPetSpeciesId() alongside breed/sicknesses, validated as nullable + Rule::exists('sizes','id')->where('species_id', ...). pet-form.blade.php renders the "Size" <flux:select> right after Fur Type only when $this->sizes->isNotEmpty() — species without any Size rows (e.g. Cat, unless sizes are added for it) show no size field at all, with a "No Size Assigned" null option when it is shown. Pet::size() belongsTo added alongside the existing species/breed/furType relations. See [[admin]] for the ManageSizes CRUD page that manages the sizes table itself.

## Pet's "Pure breed" toggle is gated by the selected species' has_pure_breed_field
pets.is_pure_breed (boolean, default false) was already in the base migration but unused until now. PetForm wires it as petIsPureBreed, placed right after petBreedId (property, mount assignment, validation, petAttributes). The pet-form.blade.php switch only renders when `$this->currentSpecies?->has_pure_breed_field` is true — mirrors the species-gated Size field pattern (see [[admin]] ManageSizes / pets-models.md), but keyed off a boolean flag on Species (has_pure_breed_field, managed in admin/species — see [[admin]]) rather off a per-species child collection. updatedPetSpeciesId() resets petIsPureBreed to false and unsets $this->currentSpecies (needed here since, unlike breeds/sizes/sicknesses, currentSpecies wasn't previously unset on species change — add it back if you see it missing). Label key is "Pure breed" (singular, per-pet) — don't confuse with admin's "Pure breeds" (plural, the species-level toggle).
