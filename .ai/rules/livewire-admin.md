---
paths:
  - 'app/Livewire/Admin/ManageUsers.php,resources/views/livewire/admin/manage-users.blade.php'
  - 'app/Livewire/Admin/UserForm.php,resources/views/livewire/admin/user-form.blade.php'
---

# Livewire Admin

## ManageUsers is now paginated (20/page), matches ManagePets/ManageVolunteers
ManageUsers now `use WithPagination`; `users()` returns `LengthAwarePaginator<int, User>` via `->paginate(20)` instead of a plain `->get()` Collection. `updatedFilterShelterId()` (which only did `unset($this->users)`) was replaced with `updatingFilterShelterId(): void { $this->resetPage(); }`, mirroring ManagePets/ManageVolunteers' `updating*` → `resetPage()` convention (see [[pets-views-livewire-volunteers]] and [[views-livewire-volunteers]]). Blade renders `<flux:pagination :paginator="$this->users" class="!border-t-0 !pt-0" />` in its own `px-6 py-3` div as a sibling AFTER the table's bordered wrapper closes, not nested inside it — same placement as manage-pets.blade.php/manage-volunteers.blade.php.

## ManageUsers edits shelter_users memberships, not a single role/shelter
A user row can now be "manager at Shelter A, staff at Shelter B". State moved from userRole/userShelterId/userVaccinationNotifications to a repeatable $userMemberships array (shelter_id/role/vaccination_notifications per row) plus addMembership()/removeMembership(). assignableRoles() is gone — the role select is always staff/manager regardless of viewer. userIsAdmin is admin-viewer-only and toggles a separate global-admin flag (no memberships when true). A manager who manages more than one shelter picks which roster to view via workingShelterId (hidden/fixed when they manage only one). Non-admin scoping (users()/editUser()/deleteUser()) is now explicit via whereHas('shelters', ...) on Auth::user()->managedShelterIds() — it is NOT automatic via MultiShelterTrait anymore, since User no longer uses that trait (no shelter_id column). New capability: inviting an email that already has an account attaches a new shelter_users row and sends App\Notifications\ShelterMembershipAdded instead of App\Notifications\UserInvitation (no password reset token — they already have credentials); updatedUserEmail() detects this live via $existingUserId.

## Managers cannot change their own name or shelters in UserForm
When a non-admin manager opens UserForm on their own account (computed isManagerEditingOwnAccount), the name, shelter and role fields are disabled and the Add shelter and remove buttons are hidden. addMembership() and removeMembership() do nothing in this case. On save, updateExistingUser() hands off to updateOwnNotificationPreferences(), which only applies vaccination_notifications to memberships the manager already has, so any tampered name, role or shelter value is ignored. This matches Settings > Profile, where name and email are also read-only. Admins editing any user, managers included, are not restricted.
