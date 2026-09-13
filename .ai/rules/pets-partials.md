---
paths:
  - 'resources/views/livewire/volunteers/volunteer-show.blade.php,resources/views/livewire/pets/partials/adoption-box.blade.php,resources/views/livewire/pets/partials/sponsorship-box.blade.php'
---

# Pets Partials

## Value brightening (text-neutral-700/300) extended to all show pages
The `class="text-neutral-700 dark:text-neutral-300"` value-brightening from pet-show.blade.php (see [[resources-views-livewire-pets]]) was extended to every other label/value show page: volunteer-show.blade.php, and the shared partials/adoption-box.blade.php + partials/sponsorship-box.blade.php (which cover adoption-show.blade.php and sponsorship-show.blade.php since they just @include these partials). Labels keep `text-neutral-500 dark:text-neutral-400`; badge-rendered values (Application Status, Send Feedback/Newsletter in sponsorship-box) are untouched since badges already carry their own color. This is now the standard label/value show-page convention across Pets and Volunteers — apply the same two classes to any new show-page field.
