---
paths:
  - routes/web.php
---

# Routes

## Static pets/* routes must be declared before pets/{pet}
Route::livewire('pets/{pet}', PetShow::class) is a 2-segment wildcard, so any new static 2-segment route like pets/sponsorships must be registered BEFORE it in routes/web.php, or {pet} will swallow the static segment (e.g. "sponsorships") and fail/404 via route-model-binding instead of matching the intended route. Same applies to any future pets/{word} addition — check registration order, not just the pattern.

Introduced by App\Livewire\Pets\ManageSponsorships (route pets.sponsorships.index) — see also .ai/rules/pets.md and .ai/rules/livewire-pets.md for Sponsorship's transitive shelter scoping via whereHas('pet').

## Public portal is toggled per installation via config('app.public_portal_enabled')
Env PUBLIC_PORTAL_ENABLED (default false = backoffice only). Route 'home' (/) always stays registered (auth layouts and logout link to it) but carries App\Http\Middleware\EnsurePublicPortalEnabled, which redirects to login when disabled. Use middleware, not `if (config(...))` around route registration: route:cache would freeze the choice and tests couldn't toggle it. privacy-policy stays public in both modes (backoffice still stores personal data). Portal-only UI (PetForm "Publish to Portal"/"Is Featured" switches, public layout "Adopt" link) is wrapped in @if (config('app.public_portal_enabled')). Tests hitting route('home') must set config(['app.public_portal_enabled' => true]) (WelcomeTest does it in beforeEach).
