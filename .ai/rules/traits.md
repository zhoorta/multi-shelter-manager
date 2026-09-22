---
paths:
  - app/Traits/MultiShelterTrait.php
---

# Traits

## Never call Auth::user() unguarded in a global scope applied to the User model
MultiShelterTrait's global scope and `creating` hook call Auth::user() to resolve the acting user's shelter_id. Because this trait is also applied to the User model itself, an unguarded Auth::user() call there causes infinite recursion: resolving the session user triggers a User::find() query, which re-invokes the scope, which calls Auth::user() again before the guard has cached anything — crashing the process with a memory-exhaustion fatal (no exception, blank response).

Fix/guard: always check `Auth::hasUser()` (a pure in-memory state check, no query) before calling `Auth::user()` inside this trait. Do not remove that guard. Regression test: tests/Feature/Traits/MultiShelterTraitTest.php ("resolves the logged-in user from the session without recursing...") — it forces a fresh guard via `Auth::forgetGuards()` after a real login, since `actingAs()` bypasses the bug entirely (it sets the guard's user directly, skipping retrieveById).

## MultiShelterTrait scopes by current_shelter_id/is_admin, no longer applied to User
The trait's global scope/creating hook now read users.current_shelter_id and users.is_admin (not the old shelter_id/role columns, which no longer exist). Admin (is_admin=true) is exempt and sees everything. A non-admin with current_shelter_id === null now sees NOTHING (whereRaw('1 = 0')), not everything — a user can have several shelter memberships via shelter_users, so a missing shelter id no longer implies "unrestricted" the way a null shelter_id did under the old single-shelter schema. The Auth::hasUser() guard in both hooks must stay — removing it causes infinite recursion when resolving the session user (see tests/Feature/Traits/MultiShelterTraitTest.php). User no longer uses this trait at all (users table has no shelter_id column anymore, so it would be a permanent no-op) — only Pet and Facility use it now.
