---
paths:
  - 'resources/views/livewire/pets/manage-pets.blade.php,resources/views/livewire/volunteers/manage-volunteers.blade.php'
---

# Pets Views Livewire Volunteers

## List views: pagination lives outside the table's bordered wrapper
Both manage-pets.blade.php and manage-volunteers.blade.php wrap the table in an outer `rounded-xl bg-white shadow-sm dark:bg-neutral-900` div containing only the `overflow-hidden rounded-xl border ... overflow-x-auto table` structure. `<flux:pagination>` sits in its own `px-6 py-3` div as a SIBLING after that wrapper closes (not nested inside it), with `class="!border-t-0 !pt-0"` to strip Flux's default top border/padding since the pagination no longer sits inside a bordered box. manage-pets.blade.php was updated to match manage-volunteers.blade.php's already-established pattern (2026-09-14) — previously pagination was nested inside the bordered div with no border override.
