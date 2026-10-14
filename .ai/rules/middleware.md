---
paths:
  - app/Http/Middleware/ReadOnlyWhileImpersonating.php
  - app/Http/Middleware/EnsureEmailIsVerifiedUnlessImpersonating.php
---

# Middleware

## Admin impersonation is read-only, enforced at the database
Admins view the app as a manager/staff user from Admin > Users (POST impersonation.start; never admins or self; session key impersonator_id; stop restores the admin; start/stop are logged). ReadOnlyWhileImpersonating (web group) refuses non-safe non-Livewire requests (except stop/logout), blocks profile.edit and security.edit, and registers DB::beforeExecuting that aborts 403 on any insert/update/delete/DDL except the sessions/cache tables, so Eloquent and mass writes are covered alike. Harmless preference writes must be wrapped in ReadOnlyWhileImpersonating::allowWrites() (ShelterSwitcher, the admin's own login on stop). UpdateLastLogin skips while impersonating. A new write that must work while viewing needs allowWrites; Blameable is irrelevant because nothing is saved.

## The `verified` alias is overridden to let impersonation through
bootstrap/app.php aliases 'verified' to EnsureEmailIsVerifiedUnlessImpersonating (a subclass of Laravel's): an admin viewing as an invited user who has not set a password yet (email_verified_at null until the invitation link is used) passes, read-only, so new shelters can be checked before the manager arrives. Nothing is verified or logged by doing so. Don't re-alias 'verified' to the stock middleware.
