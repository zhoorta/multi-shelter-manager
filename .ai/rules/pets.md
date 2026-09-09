---
paths:
  - 'app/Livewire/Pets/**'
---

# Pets

## pets.gender enum only allows male/female — 'unknown' is a latent bug
The pets.gender column is `enum('gender', ['male', 'female'])` — no 'unknown' value exists at the DB level. PetForm's validation ('petGender' => 'in:male,female,unknown') and the gender <flux:select> in pet-form.blade.php both still offer 'unknown', which will throw a DB constraint violation if selected. PetFactory was fixed to only generate male/female. Not fixed in the UI/validation yet — decide whether to widen the DB enum or drop 'unknown' from the form before shipping.

## Gender is male/female only — 'unknown' removed
The previously-documented latent bug is fixed: PetForm's validation ('petGender' => 'in:male,female') and the gender <flux:select> in pet-form.blade.php no longer offer 'unknown', matching the pets.gender DB enum (male/female only). Don't reintroduce an 'unknown' option without widening the DB enum first.

## pets.ref format is PET + zero-padded pet_id, set after insert
pets.ref is generated from the pet's own id (format PET00001, PET00002, ...), not a random string — so it's naturally unique with no DB check needed. Since the id doesn't exist until after INSERT, PetForm::savePet() creates the Pet with a temporary empty ref, then immediately updates it via generatePetRef($pet->id), all inside the same DB::transaction(). Only happens on create — editing never touches ref. PetFactory mirrors this with an afterCreating() hook (definition() can't know the id either). Do not go back to a random Str::random()-based ref.

## pets.description is a contenteditable rich-text field, sanitized server-side
PetForm has no wire:model on the description editor — pet-form.blade.php uses a contenteditable div driven by Alpine (bold/italic/underline/list buttons via document.execCommand), synced to $wire.set('petDescription', html, false) on input. wire:model was avoided deliberately: Livewire's DOM morphing on a bound contenteditable element fights the browser's cursor/selection on every keystroke.

Stored HTML is never trusted as-is: PetForm::sanitizeDescription() strips to an allowlist (<p><br><b><strong><i><em><u><ul><ol><li>) and strips all attributes from surviving tags (regex, since strip_tags() keeps attributes on allowed tags) before saving to pets.description. Returns null when only empty markup remains (e.g. contenteditable's stray "<p><br></p>"). Any future code that renders pets.description must still treat it as HTML needing `{!! !!}`, not `{{ }}` — sanitization happens on write, not on read.
