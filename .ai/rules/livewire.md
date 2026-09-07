---
paths:
  - 'app/Livewire/**'
---

# Livewire

## Full-page Livewire components render via Route::livewire(), no manual layout wrapper
This app uses Livewire 4 class-based ("traditional split") components: app/Livewire/{Name}.php + resources/views/livewire/{name}.blade.php, wired up in routes with `Route::livewire('path', Name::class)->name(...)` (see routes/settings.php, routes/web.php).

Livewire's default `component_layout` config ('layouts::app') automatically wraps the component's rendered view in resources/views/layouts/app.blade.php (sidebar + header chrome) — so the Blade view for a full-page component must NOT wrap itself in `<x-layouts::app>`; just return the inner content (see resources/views/livewire/settings/*.blade.php and livewire/dashboard.blade.php for the pattern). Set the browser tab title via `#[Title('...')]` on the component class, not a layout prop.

When computing shelter-scoped stats that involve Cage: Cage has no direct shelter_id column (see [[models]] rule on MultiShelterTrait) so scope it via `Cage::whereHas('wing', fn ($q) => $q->where('shelter_id', $shelterId))`.
