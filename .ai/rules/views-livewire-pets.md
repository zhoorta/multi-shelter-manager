---
paths:
  - 'app/Livewire/Pets/PetShow.php,resources/views/livewire/pets/pet-show.blade.php'
  - 'app/Livewire/Pets/ManagePets.php,resources/views/livewire/pets/manage-pets.blade.php'
  - 'app/Livewire/Pets/AdoptionForm.php,resources/views/livewire/pets/adoption-form.blade.php'
  - 'app/Livewire/Pets/SponsorshipForm.php,resources/views/livewire/pets/sponsorship-form.blade.php'
---

# Views Livewire Pets

## PetShow hides Size row when the pet's species has no sizes registered
PetShow::speciesHasSizes() (Computed, Size::query()->where('species_id', pet.species_id)->exists()) gates the "Size" row in pet-show.blade.php — same rule as PetForm::sizes() hiding the size dropdown ([[pets-models]]). mount() eager-loads 'size' alongside furType/breed/etc. When shown, the value falls back to '—' if the pet itself has no size_id (species has sizes but this pet wasn't given one). Also: "Sizes"/"Size"/"No Size Assigned"/"No sizes registered" translation keys were missing from lang/en.json + lang/pt.json when the sizes feature first shipped — added now. Check lang/*.json for a matching key whenever adding a new admin lookup label; __() silently falls back to the raw English key so a missing PT entry doesn't error, it just leaks English into the PT UI.

## Pets list Characteristics column: size shown right after breed
manage-pets.blade.php's Characteristics column order is now breed, size, fur type, colors, age (each its own <span>, blank when null — same convention as furType). ManagePets::pets() eager-loads 'size' alongside breed/furType/etc. No hide-when-species-has-no-sizes rule here (unlike PetForm/PetShow, see [[views-livewire-pets]]) — a blank span is acceptable in this compact list view.

## pet-show Breed row appends "(Pure)" gated by both species.has_pure_breed_field and pet.is_pure_breed
pet-show.blade.php's Breed row shows "(Pure)" (translation key "Pure") only when BOTH $pet->species->has_pure_breed_field AND $pet->is_pure_breed are true — checking only one is not enough, since is_pure_breed could be stale true data from before the species flag was toggled off. species is already eager-loaded in PetShow::mount(). Mirrors [[pets-models]] rule on PetForm's petIsPureBreed toggle (same two-flag gating), and the [[views-livewire-pets]] pattern of hiding a breed-adjacent field based on a species-level condition.

## pet-show's Adoption subsection was split: Cage/Checkin/Checkout moved to Accommodation
Superseding the earlier note in [[pets-views-livewire-pets]]: pet-show.blade.php's "Adoption" subsection now holds only Is Adoptable/Is Sponsorable/Status. Cage, Checkin Date and Checkout Date moved into their own "Accommodation" subsection (same `flux:heading size="lg"` + responsive label/value grid pattern), placed right after Adoption. The "Accommodation" translation key already existed in lang/en.json and lang/pt.json (used elsewhere) so nothing new was added to the lang files this time.

## adoption-form.blade.php now also pairs short fields into grid rows
adoption-form.blade.php already paired Email+Phone and Postal Code+City (see the field:class pattern in [[views-livewire]]); it was missing the Adoption Date/Return Date and Adoption Fee/Application Status pairings that [[pets-views-livewire-pets]]'s volunteer-style density convention expects. Both are now `grid grid-cols-2` rows in the second card, right before the full-width Notes textarea. Same visual density as pet-form.blade.php/volunteer-form.blade.php — no PHP/behavior changes.

## sponsorship-form.blade.php pairs its two switches into a grid row
sponsorship-form.blade.php's first card already matched the volunteer-style density convention (Name full, Email+Phone paired, Address full, Postal Code+City paired). The second card's Send Feedback/Send Newsletter switches are now paired in a `grid grid-cols-2` row too (mirrors pet-form.blade.php's Is Adoptable/Is Sponsorable pairing), with the Notes textarea staying full-width below. See [[pets-views-livewire-pets]] for the overall convention and [[views-livewire-pets]] for the sibling adoption-form pairing.
