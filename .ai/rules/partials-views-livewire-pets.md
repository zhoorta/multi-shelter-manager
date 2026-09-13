---
paths:
  - 'resources/views/livewire/pets/partials/sponsorship-box.blade.php,resources/views/livewire/pets/sponsorship-show.blade.php'
  - 'resources/views/livewire/pets/partials/adoption-box.blade.php,resources/views/livewire/pets/adoption-show.blade.php'
---

# Partials Views Livewire Pets

## sponsorship-box partial also follows the volunteer-style label/value grid
partials/sponsorship-box.blade.php was restyled to match [[pets-views-livewire-pets]]'s pet-show/volunteer-show convention: the outer wrapper is now `flex flex-col gap-4` (was `grid grid-cols-[max-content_1fr]` with col-span-2 header tricks), and the Name/Email/Phone/Address/Postal Code/City/Send Feedback/Send Newsletter/Notes fields live in a `grid-cols-1 sm:grid-cols-2 lg:grid-cols-3` grid of label-above-value `<div>`s (no colon suffix), same as pet-show's subsections. The "Sponsorship" / "Sponsorship Payments" `flux:label` header bars and the payments table itself are unchanged. Since this partial is shared by both pet-show.blade.php and sponsorship-show.blade.php (see [[concerns]]), editing it updates both pages at once — don't duplicate the markup into either page.

adoption-box.blade.php was intentionally NOT touched (not requested) and still uses the old `grid-cols-[max-content_1fr]` colon-label style — restyle it the same way if asked to bring it in line.

## adoption-box now also follows the volunteer-style label/value grid
Superseding the earlier note in [[partials-views-livewire-pets]] that said adoption-box was left untouched: partials/adoption-box.blade.php was converted the same way as sponsorship-box.blade.php — outer wrapper is `flex flex-col gap-4` (was `grid grid-cols-[max-content_1fr]`), Name/Email/Phone/Address/Postal Code/City/Adoption Date/Return Date/Adoption Fee/Application Status live in a `grid-cols-1 sm:grid-cols-2 lg:grid-cols-3` grid of label-above-value `<div>`s (no colon), and Notes was pulled out into its own full-width block below with `whitespace-pre-line` since adoption notes can be long. This partial is shared by pet-show.blade.php (looped) and adoption-show.blade.php (single, with backToAdoptionsList=true) — edit the partial, not either page.
