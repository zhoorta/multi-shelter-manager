---
paths:
  - resources/views/livewire/pets/pet-show.blade.php
---

# Resources Views Livewire Pets

## pet-show values use flux:text variant="strong" for brighter contrast
Every value `<flux:text>` in pet-show.blade.php's Identification/Characteristics/Health/Adoption/Accommodation/Description/Notes subsections now has `variant="strong"` (renders `text-zinc-800 dark:text-white` per vendor/livewire/flux/stubs/resources/views/flux/text.blade.php, vs the default muted `text-zinc-500 dark:text-white/70`). Labels keep the existing `class="text-neutral-500 dark:text-neutral-400"` muted styling — only values were brightened. Badge-rendered values (Is Neutered, sickness flags, Is Adoptable/Sponsorable, Status) were left as-is since badges already carry their own strong color. Apply the same `variant="strong"` treatment to value text if this pattern is extended to volunteer-show.blade.php or the adoption/sponsorship box partials.

## pet-show values use text-neutral-700/300, not flux:text variant=strong
Superseding the earlier note in [[resources-views-livewire-pets]]: `variant="strong"` (text-zinc-800/white) was judged too bright and replaced with an explicit `class="text-neutral-700 dark:text-neutral-300"` on the same 16 value `<flux:text>` elements in pet-show.blade.php — a middle contrast between the label's muted `text-neutral-500 dark:text-neutral-400` and Flux's `strong` variant. If asked to brighten/dim these values again, adjust this class (not variant) in pet-show.blade.php; labels stay untouched.

## pet-show subsection headings dropped to default size to match "Photos" label
The 7 subsection headings in pet-show.blade.php (Identification, Characteristics, Health, Adoption, Accommodation, Description, Notes) were changed from `<flux:heading size="lg">` (text-base, 16px) to bare `<flux:heading>` (default size, text-sm, 14px) so they visually match the "Photos" card title, which uses `<flux:label>` (also text-sm). The top-level page heading (`size="xl"`, the pet's name) is untouched. This was scoped to pet-show.blade.php only — volunteer-show.blade.php still uses `size="lg"` subsection headings; ask before changing it too if consistency is wanted there.
