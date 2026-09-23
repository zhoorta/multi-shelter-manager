---
paths:
  - 'app/Livewire/**'
  - app/Livewire/Welcome.php
---

# Livewire

## Full-page Livewire components render via Route::livewire(), no manual layout wrapper
This app uses Livewire 4 class-based ("traditional split") components: app/Livewire/{Name}.php + resources/views/livewire/{name}.blade.php, wired up in routes with `Route::livewire('path', Name::class)->name(...)` (see routes/settings.php, routes/web.php).

Livewire's default `component_layout` config ('layouts::app') automatically wraps the component's rendered view in resources/views/layouts/app.blade.php (sidebar + header chrome) — so the Blade view for a full-page component must NOT wrap itself in `<x-layouts::app>`; just return the inner content (see resources/views/livewire/settings/*.blade.php and livewire/dashboard.blade.php for the pattern). Set the browser tab title via `#[Title('...')]` on the component class, not a layout prop.

When computing shelter-scoped stats that involve Cage: Cage has no direct shelter_id column and neither does its parent Wing (see [[models]] rule on MultiShelterTrait, and [[facilities]]) so scope it two hops transitively via `Cage::whereHas('wing.facility', fn ($q) => $q->where('shelter_id', $shelterId))`.

## Public welcome page bypasses the shelter scope, gated by publish_to_portal
Route 'home' (/) is App\Livewire\Welcome, public for guests AND logged-in users (layouts::public). It lists pets across all shelters, so Welcome::publicPetsQuery() calls withoutGlobalScope('shelter') on purpose (otherwise a logged-in staff member would only see their own shelter). Every public query must go through publicPetsQuery(), which only exposes pets with publish_to_portal=true, is_adoptable=true, status='available', no date_of_death and a non-deleted shelter. publish_to_portal defaults to false and is set via the "Publish to Portal" switch in PetForm. Never expose internal_notes/clinical_notes/notes/chip/cage on this page.

## Public portal pet visibility lives in Pet::publishedToPortal() scope
The portal visibility rules (publish_to_portal, is_adoptable, status='available', no date_of_death, non-deleted shelter) are the #[Scope] Pet::publishedToPortal(). Welcome::publicPetsQuery() and PartnerShelters (route 'shelters', the public list of partner shelters with their available_pets_count) both use it. The scope does NOT remove the 'shelter' global scope: public pages must chain withoutGlobalScope('shelter') themselves, including inside withCount closures, or logged-in staff see counts for their own shelter only. Public portal routes ('home', 'shelters') carry EnsurePublicPortalEnabled middleware.
