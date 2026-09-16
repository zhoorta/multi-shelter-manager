---
paths:
  - 'app/Livewire/Pets/PetForm.php,resources/views/livewire/pets/pet-form.blade.php,app/Models/Pet.php'
  - 'app/Livewire/Pets/ManageVaccinations.php,resources/views/livewire/pets/manage-vaccinations.blade.php,app/Models/PetVaccine.php'
---

# Pets Models

## Pet size is species-scoped and optional, mirrors breed dropdown but hides when species has no sizes
Pet.size_id (nullable FK to sizes, RESTRICT) is now wired into PetForm: petSizeId property, PetForm::sizes() computed (same shape as breeds(), scoped by petSpeciesId, empty Collection when species has none), reset in updatedPetSpeciesId() alongside breed/sicknesses, validated as nullable + Rule::exists('sizes','id')->where('species_id', ...). pet-form.blade.php renders the "Size" <flux:select> right after Fur Type only when $this->sizes->isNotEmpty() — species without any Size rows (e.g. Cat, unless sizes are added for it) show no size field at all, with a "No Size Assigned" null option when it is shown. Pet::size() belongsTo added alongside the existing species/breed/furType relations. See [[admin]] for the ManageSizes CRUD page that manages the sizes table itself.

## Pet's "Pure breed" toggle is gated by the selected species' has_pure_breed_field
pets.is_pure_breed (boolean, default false) was already in the base migration but unused until now. PetForm wires it as petIsPureBreed, placed right after petBreedId (property, mount assignment, validation, petAttributes). The pet-form.blade.php switch only renders when `$this->currentSpecies?->has_pure_breed_field` is true — mirrors the species-gated Size field pattern (see [[admin]] ManageSizes / pets-models.md), but keyed off a boolean flag on Species (has_pure_breed_field, managed in admin/species — see [[admin]]) rather off a per-species child collection. updatedPetSpeciesId() resets petIsPureBreed to false and unsets $this->currentSpecies (needed here since, unlike breeds/sizes/sicknesses, currentSpecies wasn't previously unset on species change — add it back if you see it missing). Label key is "Pure breed" (singular, per-pet) — don't confuse with admin's "Pure breeds" (plural, the species-level toggle).

## ManageVaccinations lists PetVaccine directly; PetVaccine gained pet()/vaccine() relations
App\Livewire\Pets\ManageVaccinations (route pets.vaccinations.index, "pets/vaccinations", sidebar item under "Pets" after "Adoptions") mirrors ManageSponsorships/ManageAdoptions but queries PetVaccine::query() directly (not Pet::vaccines()) since it's a shelter-wide list, not per-pet — scoped via whereHas('pet') like Sponsorship, since PetVaccine has no shelter_id despite having a direct pet_id column.

This required adding pet(): BelongsTo and vaccine(): BelongsTo to App\Models\PetVaccine (previously it only had the implicit Pivot magic-attribute access used by PetShow/VaccinationForm via ->pivot). Don't remove these relations — ManageVaccinations eager-loads pet.species/pet.images/vaccine via them.

The overdue/due-soon Next Due Date highlighting (red/amber, same classes as pet-show's vaccinations table) is reused via a new public method ManageVaccinations::hasNewerDoseOfSameVaccine(PetVaccine $petVaccine): bool, which runs a scoped exists() query instead of scanning an in-memory $pet->vaccines collection (pet-show's approach) — the list page has no single pet's full vaccine collection loaded. Route 'pets/vaccinations' is registered before 'pets/{pet}' in routes/web.php per the existing static-route-ordering rule. Show action reuses the pet-show vaccination-show modal markup inline (no dedicated VaccinationShow page exists); Edit links to the existing pets.vaccinate.edit route; Delete soft-deletes via a scoped whereHas('pet') query, same pattern as ManageSponsorships::deleteSponsorship().
