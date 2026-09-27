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

## About page is part of the public portal
Route 'about' carries EnsurePublicPortalEnabled (user decision 2026-09-26, replacing the earlier "About stays public"): with PUBLIC_PORTAL_ENABLED=false it redirects to login, and layouts/public.blade.php only shows the header/footer About links inside @if (config('app.public_portal_enabled')). Only privacy-policy (and robots.txt, login) stay public in backoffice-only mode, so tests that need a public page regardless of the portal flag (e.g. LocaleTest) use route('privacy-policy'), not route('about'). AboutTest enables the portal in beforeEach.

## Public portal paths are translated per installation (APP_LOCALE)
Each country runs its own installation, so the public portal URL words come from lang/{locale}/routes.php (shelters, animals, adopt, about) for config('app.locale'), read when routes load (user decision 2026-09-27). Route names never change, so always link with route(). Tests run in en (/shelters, /animals…); tests/Feature/PublicPortalUrlsTest reloads routes/web.php as a pt installation. Only the old English /about redirects 301 to the translated path (it was linked from the outreach e-mails; the other public links were never shared, user decision 2026-09-27). Backoffice routes, privacy-policy and the feed (shelters/{shelter}/feed, id-based, registered in social media tools) stay fixed. A new public route needs a key in all 10 routes.php files.
