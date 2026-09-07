---
paths:
  - app/Traits/MultiShelterTrait.php
---

# Traits

## Never call Auth::user() unguarded in a global scope applied to the User model
MultiShelterTrait's global scope and `creating` hook call Auth::user() to resolve the acting user's shelter_id. Because this trait is also applied to the User model itself, an unguarded Auth::user() call there causes infinite recursion: resolving the session user triggers a User::find() query, which re-invokes the scope, which calls Auth::user() again before the guard has cached anything — crashing the process with a memory-exhaustion fatal (no exception, blank response).

Fix/guard: always check `Auth::hasUser()` (a pure in-memory state check, no query) before calling `Auth::user()` inside this trait. Do not remove that guard. Regression test: tests/Feature/Traits/MultiShelterTraitTest.php ("resolves the logged-in user from the session without recursing...") — it forces a fresh guard via `Auth::forgetGuards()` after a real login, since `actingAs()` bypasses the bug entirely (it sets the guard's user directly, skipping retrieveById).
