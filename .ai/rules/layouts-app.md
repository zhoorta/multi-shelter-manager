---
paths:
  - 'app/Livewire/Admin/ManageUsers.php,resources/views/livewire/admin/manage-users.blade.php,resources/views/layouts/app/sidebar.blade.php'
---

# Layouts App

## ManageUsers is shared by admin and manager, not admin-only
App\Livewire\Admin\ManageUsers (route admin.users.index) is now accessible to both `admin` and `manager` roles — mount() checks `in_array(role, ['admin','manager'])`. A manager can only invite/edit staff or manager users (never admin) into their own shelter.

Enforcement relies on User's existing MultiShelterTrait global scope, not extra manual checks: when a manager is authenticated, every `User::query()` (including inside `users()`, `editUser()`, `deleteUser()`) is already filtered to `shelter_id = manager's shelter_id`, so cross-shelter edit/delete attempts throw ModelNotFoundException automatically (see tests/Feature/ManageUsersTest.php "manager cannot edit/delete a user from another shelter"). Don't add a redundant `where shelter_id` check in this component — the trait already does it.

`ManageUsers::assignableRoles()` returns `['staff','manager']` for a manager vs `['staff','manager','admin']` for an admin, used both in the `userRole` validation Rule::in() and the blade role `<flux:select>` options. `saveUser()` forcibly overwrites `$this->userShelterId = Auth::user()->shelter_id` when the acting user is a manager (before validation), so a manager can never assign a user to another shelter even via a tampered request. The blade hides the shelter filter, the Shelter table column, and the shelter `<flux:select>` in the invite/edit modal behind `@if (auth()->user()->role === 'admin')` since a manager's shelter is implicit.

Note: to assert a row still exists after a blocked cross-shelter action inside a test where the acting user is a scoped manager, use `$model->fresh()` (bypasses global scopes, like `->trashed()` checks) — `User::query()->find($id)` will incorrectly return null due to the shelter scope even when the row was never touched.
