---
paths:
  - 'app/Livewire/Setup.php,app/Providers/FortifyServiceProvider.php,tests/Feature/Auth/**'
---

# Auth

## Login redirects to the setup wizard while no users exist
App\Livewire\Setup (guest route 'setup', layouts::auth) creates the first admin (is_admin, email verified) and logs them in. It is gated by Setup::isRequired() (no non-soft-deleted users). The Fortify loginView closure in FortifyServiceProvider redirects to 'setup' when that returns true. This means any test that GETs route('login') and expects 200 must create a user first; tests that only assert a redirect *to* login are unaffected. createAdmin() re-checks isRequired() and aborts 403, so the wizard can't be reused to add admins later.
