---
paths:
  - routes/web.php
---

# Routes

## Static pets/* routes must be declared before pets/{pet}
Route::livewire('pets/{pet}', PetShow::class) is a 2-segment wildcard, so any new static 2-segment route like pets/sponsorships must be registered BEFORE it in routes/web.php, or {pet} will swallow the static segment (e.g. "sponsorships") and fail/404 via route-model-binding instead of matching the intended route. Same applies to any future pets/{word} addition — check registration order, not just the pattern.

Introduced by App\Livewire\Pets\ManageSponsorships (route pets.sponsorships.index) — see also .ai/rules/pets.md and .ai/rules/livewire-pets.md for Sponsorship's transitive shelter scoping via whereHas('pet').
