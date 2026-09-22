---
paths:
  - 'app/Livewire/Admin/ManageUsers.php,resources/views/livewire/admin/manage-users.blade.php,resources/views/layouts/app/sidebar.blade.php'
---

# Layouts App

## ManageUsers is shared by admin and any shelter manager, not admin-only
App\Livewire\Admin\ManageUsers (route admin.users.index) is accessible to admins and to any user who manages at least one shelter — `mount()` checks `$viewer->is_admin || $viewer->isManagerOfAnyShelter()`. A manager can only invite/edit staff or manager memberships (never the global admin flag) within shelters they manage.

Enforcement is explicit, not automatic: since `User` no longer uses `MultiShelterTrait` (users has no shelter_id column — see [[traits]] and [[models]]), `users()`/`editUser()`/`deleteUser()` each apply their own `whereHas('shelters', fn ($q) => $q->whereIn('shelters.id', $viewer->managedShelterIds()))` constraint for a non-admin viewer, so cross-shelter edit/delete attempts throw `ModelNotFoundException` (see tests/Feature/ManageUsersTest.php). See [[livewire-admin]] for the full per-membership rework (`$userMemberships`, `userIsAdmin`, `workingShelterId`).

Note: to assert a row still exists after a blocked cross-shelter action inside a test where the acting user is a scoped manager, use `$model->fresh()` (bypasses global scopes, like `->trashed()` checks) — a scoped `User::query()->find($id)` call will incorrectly return null due to an explicit shelter constraint even when the row was never touched.
