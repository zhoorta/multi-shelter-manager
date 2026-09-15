---
paths:
  - resources/views/livewire/pets/pet-show.blade.php
  - resources/views/livewire/pets/manage-pets.blade.php
---

# Resources Views Livewire Pets

## pet-show values use flux:text variant="strong" for brighter contrast
Every value `<flux:text>` in pet-show.blade.php's Identification/Characteristics/Health/Adoption/Accommodation/Description/Notes subsections now has `variant="strong"` (renders `text-zinc-800 dark:text-white` per vendor/livewire/flux/stubs/resources/views/flux/text.blade.php, vs the default muted `text-zinc-500 dark:text-white/70`). Labels keep the existing `class="text-neutral-500 dark:text-neutral-400"` muted styling — only values were brightened. Badge-rendered values (Is Neutered, sickness flags, Is Adoptable/Sponsorable, Status) were left as-is since badges already carry their own strong color. Apply the same `variant="strong"` treatment to value text if this pattern is extended to volunteer-show.blade.php or the adoption/sponsorship box partials.

## pet-show values use text-neutral-700/300, not flux:text variant=strong
Superseding the earlier note in [[resources-views-livewire-pets]]: `variant="strong"` (text-zinc-800/white) was judged too bright and replaced with an explicit `class="text-neutral-700 dark:text-neutral-300"` on the same 16 value `<flux:text>` elements in pet-show.blade.php — a middle contrast between the label's muted `text-neutral-500 dark:text-neutral-400` and Flux's `strong` variant. If asked to brighten/dim these values again, adjust this class (not variant) in pet-show.blade.php; labels stay untouched.

## pet-show subsection headings dropped to default size to match "Photos" label
The 7 subsection headings in pet-show.blade.php (Identification, Characteristics, Health, Adoption, Accommodation, Description, Notes) were changed from `<flux:heading size="lg">` (text-base, 16px) to bare `<flux:heading>` (default size, text-sm, 14px) so they visually match the "Photos" card title, which uses `<flux:label>` (also text-sm). The top-level page heading (`size="xl"`, the pet's name) is untouched. This was scoped to pet-show.blade.php only — volunteer-show.blade.php still uses `size="lg"` subsection headings; ask before changing it too if consistency is wanted there.

## pet-show heart dropdown trigger has no custom color anymore
The dropdown trigger `<flux:button icon="heart">` in the header no longer carries the custom `class="bg-[#960532]! ..."` maroon color override — it's a plain default-styled icon button now. Don't reintroduce the custom background color without being asked.

## Accommodation column shows a plain "-" for pets with no cage, not the "No X Assigned" strings
Unlike pet-show.blade.php and dashboard.blade.php (which still fall back to __('No Facility Assigned')/__('No Wing Assigned')/__('No Cage Assigned') per-line), manage-pets.blade.php's compact list now renders a single "-" span when $pet->cage is null, and the three facility/wing/cage lines only when $pet->cage exists. Don't reintroduce the three "No X Assigned" fallback strings here — that's deliberate, to keep the list row compact. Those lang keys are still used elsewhere, so don't remove them from lang/en.json or lang/pt.json.

## pet-show header shows "(Adopted at date)"/"(Deceased at date)" next to the name
Mirrors manage-pets.blade.php's list-row convention (see [[pets-views-livewire-pets]]/[[views-livewire-pets]]): the page's `<flux:heading size="xl">` now appends, in parentheses after $pet->name, "(Deceased at d/m/Y)" when date_of_death is set, else "(Adopted at d/m/Y)" when status === 'adopted' and the pet has at least one adoption — using $pet->adoptions->first() (eager-loaded ->latest('adoption_date') in PetShow::mount(), so first() is the most recent), not the separate latestAdoption() relation which isn't loaded here. Deceased takes priority over adopted, same precedence as the list. Reuses existing __('Adopted')/__('Deceased')/__('at') translation keys — no new lang entries needed.

## pet-show header shows "(Adopted at date)"/"(Deceased at date)" next to the name
Superseding the earlier note here: the "(Deceased at d/m/Y)"/"(Adopted at d/m/Y)" text is plain text inside the same `<flux:heading size="xl">` as the name (no wrapping `<span>` with a smaller/muted style) — it renders in the same font size/weight as $pet->name, unlike manage-pets.blade.php's list row where the equivalent text is deliberately smaller/muted. Deceased takes priority over adopted; uses $pet->adoptions->first() (eager-loaded ->latest('adoption_date') in PetShow::mount()), not latestAdoption(). Reuses existing __('Adopted')/__('Deceased')/__('at') keys.

## pet-show header shows "(Adopted at date)"/"(Deceased at date)" next to the name
Superseding the earlier note here (the font-matching change was undone): the "(Deceased at d/m/Y)"/"(Adopted at d/m/Y)" text is wrapped in a `<span class="text-base font-normal text-neutral-500 dark:text-neutral-400">` inside the `<flux:heading size="xl">` — smaller/muted relative to the name, matching manage-pets.blade.php's list-row styling for the same info. Deceased takes priority over adopted; uses $pet->adoptions->first() (eager-loaded ->latest('adoption_date') in PetShow::mount()), not latestAdoption(). Reuses existing __('Adopted')/__('Deceased')/__('at') keys.
