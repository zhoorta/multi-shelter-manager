---
paths:
  - 'app/Livewire/Pets/PetShow.php,resources/views/livewire/pets/pet-show.blade.php'
  - 'app/Livewire/Pets/ManagePets.php,resources/views/livewire/pets/manage-pets.blade.php'
  - 'app/Livewire/Pets/AdoptionForm.php,resources/views/livewire/pets/adoption-form.blade.php'
  - 'app/Livewire/Pets/SponsorshipForm.php,resources/views/livewire/pets/sponsorship-form.blade.php'
  - 'app/Livewire/Pets/PetForm.php,resources/views/livewire/pets/pet-form.blade.php'
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

## Cage dropdown shows available space + color, via emoji (native select, no Flux badges possible)
PetForm::cages() withCount()s each cage's 'active_pets_count' (status != adopted AND date_of_death IS NULL, excluding the pet currently being edited via whereKeyNot so its own slot doesn't count against itself), then annotates each Cage with runtime-only available_space (capacity - active_pets_count, floored at 0) and availability_color: red if available_space <= 0, yellow if active_pets_count is over 80% of capacity (strictly greater — exactly 80% usage stays green), else green.

pet-form.blade.php's Cage <flux:select> is a native <select> (this Flux UI free-edition version, v2.18.0, has no listbox/combobox variant — confirmed by reading vendor/livewire/flux/stubs), so <option> can only hold plain text — flux:badge cannot be nested inside it. The color is conveyed with a 🔴/🟡/🟢 emoji prefix instead, plus the ":available of :capacity free" translated text (lang/en.json + lang/pt.json). If a future Flux upgrade adds a listbox/combobox, prefer switching to real color badges then.

## ManagePets: search covers name/ref/chip/internal_notes, plus a location filter dropdown
ManagePets::pets()'s `search` LIKE-matches name, ref, chip, and internal_notes (not the separate `notes` field). A new `locationFilter` public string filters by exact `cage_id`, alongside the existing `statusFilter`/`speciesFilter` (each with its own `updating*() => resetPage()` hook).

A new `cages()` #[Computed] lists cages scoped transitively via `whereHas('wing.facility', ... shelter_id)` (Cage has no direct shelter_id — see [[facilities]]/[[models]]), mirroring PetForm::cages()/scopedCageQuery() but without the availability annotations (not needed for a filter). manage-pets.blade.php renders it as a single grouped `<flux:select>` (group label "Facility · Wing", option = cage code), same grouping pattern as PetForm's cage select — one dropdown represents the full Facility->Wing->Cage hierarchy, not three cascading selects.

## ManagePets location filter: Facility, Wing, or Cage each directly selectable
Superseding the earlier note here: `locationFilter` is no longer cage-only. It's a single string encoding both the level and id, e.g. "facility:3", "wing:7", "cage:12" (or "" for all). ManagePets::pets() parses it (`explode(':', ..., 2)`) and branches with `match`: 'facility' -> `whereHas('cage.wing', fn q => q->where('facility_id', $id))`, 'wing' -> `whereHas('cage', fn q => q->where('wing_id', $id))`, 'cage' -> `where('cage_id', $id)`. Selecting a Facility or Wing matches every pet in any cage underneath it, not just a leaf cage.

The `cages()` computed was replaced by `facilities()`, which eager-loads `wings` and `wings.cages` (ordered by name/code) off `Facility::query()` — scoped automatically via MultiShelterTrait on Facility, no manual whereHas needed (see [[facilities]]/[[models]]). manage-pets.blade.php renders one native `<flux:select.option>` per Facility, Wing, and Cage (all three levels selectable, not just leaves), indented with a "— "/"—— " text prefix since native `<option>` elements can't reliably be styled with padding/classes across browsers — don't reach for `flux:select.group` here, group labels aren't selectable and we need the group-level rows (Facility/Wing) to be clickable filters themselves.

## ManagePets filters: no :placeholder — Flux auto-adds a disabled duplicate
locationFilter/statusFilter's `<flux:select>` no longer carry `:placeholder`. Flux's placeholder implementation (vendor/livewire/flux/stubs/.../select/variants/*.blade.php) renders its own `<option value="" disabled selected class="placeholder">` ahead of the slot — combined with the manual selectable `<flux:select.option value="">{{ __('All') }}</flux:select.option>` already used here (and in manage-breeds/manage-users/manage-volunteers, see [[views-livewire-volunteers]]), that produced two visually-identical "All"/"All Locations" entries, one an inert disabled duplicate. Don't pair `:placeholder` with a manual empty-value option on these selects — pick one pattern (this codebase's convention is the manual selectable option, optionally with `:label`, not `:placeholder`).

The location filter's tree indentation (Facility/Wing/Cage) also no longer uses an em-dash prefix — it's `str_repeat("\u{00A0}", 4)`/`str_repeat("\u{00A0}", 8)` (non-breaking spaces) before the wing/cage name, since native `<option>` text can't be padded via CSS.

## ManagePets location filter cages show available space, mirroring PetForm
ManagePets::facilities() now withCount()s each cage's 'active_pets_count' (status != adopted AND date_of_death IS NULL) via the 'wings.cages' eager-load constraint, then annotates every cage with available_space (capacity - active_pets_count, floored at 0) and availability_color (red if <=0, yellow if active_pets_count > 80% of capacity, else green) — same formula as PetForm::cages() (see [[views-livewire-pets]]), but with no "exclude the pet being edited" step since this is a list filter, not a single pet's form. manage-pets.blade.php's cage `<flux:select.option>` shows the same 🔴/🟡/🟢 emoji + ":available of :capacity free" text as pet-form.blade.php's cage select (existing lang key, no new translations needed) after the leading indentation spaces. Facility/Wing options have no such annotation, only leaf Cage options do.

## ManagePets: missingDataFilter combo for pets missing age/photo/checkin date
A new `missingDataFilter` public string (own `updatingMissingDataFilter() => resetPage()` hook, same pattern as statusFilter/speciesFilter) adds a fourth `<flux:select>` combo to manage-pets.blade.php's filter row, alongside search/location/status. Values: '' (All), 'no_age' -> `whereNull('birth_date')` (mirrors the list's age_in_words, which is derived from birth_date, not the unused `age` column), 'no_photo' -> `whereDoesntHave('images')`, 'no_checkin_date' -> `whereNull('checkin_date')`, applied via the same `match()`-in-`when()` style as locationFilter. No :placeholder (see the existing note on Flux's disabled-duplicate placeholder bug) — plain selectable empty option instead.

## ManagePets: missingDataFilter combo for pets missing age/photo/checkin/location
Superseding the earlier note here: `missingDataFilter` now has a fourth value, 'no_location' -> `whereNull('cage_id')` (a pet with no cage assigned, independent of `locationFilter` which filters by a specific facility/wing/cage). Full value set: '' (All), 'no_age' -> whereNull('birth_date'), 'no_photo' -> whereDoesntHave('images'), 'no_checkin_date' -> whereNull('checkin_date'), 'no_location' -> whereNull('cage_id'), all via the same match()-in-when() style as locationFilter.
