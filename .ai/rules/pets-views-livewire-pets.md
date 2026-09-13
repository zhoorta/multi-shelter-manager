---
paths:
  - 'app/Livewire/Pets/PetForm.php,resources/views/livewire/pets/pet-form.blade.php,app/Livewire/Pets/PetShow.php,resources/views/livewire/pets/pet-show.blade.php'
---

# Pets Views Livewire Pets

## Pet form/show now mirror Volunteer form/show layout conventions
pet-form.blade.php was refactored to pair short related fields into `grid grid-cols-2` rows (Chip+Gender, Primary/Secondary Color, Fur Type+Size, Birth/Death Date, Is Adoptable/Is Sponsorable, Status+Cage) instead of stacking one field per row, matching volunteer-form.blade.php's density. Longer fields (Name, Breed) stay full-width.

pet-show.blade.php was refactored from many small per-field-group cards (`grid-cols-[max-content_1fr]` label:value rows) into a single card with `flux:heading size="lg"` subsections (Identification, Characteristics, Health, Adoption, Description, Notes), each holding a responsive `grid-cols-1 sm:grid-cols-2 lg:grid-cols-3` grid of label-above-value `<div>` pairs (no colon suffix), matching volunteer-show.blade.php. Notes section is hidden entirely when empty (mirrors volunteer). Description keeps its own heading but is not a grid item since it's a rich-text block, not a scalar field.

The Photos gallery card (lightbox, multi-photo) and the adoption/sponsorship partials are pet-specific and were left untouched — they have no volunteer equivalent. New "Health" translation key was added to lang/en.json and lang/pt.json for the new subsection heading.

When adding new pet fields, follow this pattern: pair short fields in a 2-col grid in the form, and add a label/value div to the matching subsection in the show page.
