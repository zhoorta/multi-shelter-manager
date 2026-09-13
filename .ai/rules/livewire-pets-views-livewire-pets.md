---
paths:
  - 'resources/views/livewire/pets/sponsorship-form.blade.php,resources/views/livewire/pets/adoption-form.blade.php'
---

# Livewire Pets Views Livewire Pets

## Notes textarea gets its own dedicated card in every pet-related form
sponsorship-form.blade.php and adoption-form.blade.php now put their Notes `flux:textarea` in its own trailing card (a bare `flex flex-col gap-4 rounded-xl border ...` box with nothing else in it), instead of bundled at the bottom of the preceding fields card. This matches pet-form.blade.php and volunteer-form.blade.php, which already isolated Notes this way. Any new pet/adoption/sponsorship form field should go in the fields card(s); Notes always stays alone in its own trailing card.
