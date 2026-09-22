---
paths:
  - 'app/Livewire/Admin/ManageUsers.php,app/Livewire/Admin/UserForm.php,resources/views/livewire/admin/manage-users.blade.php,resources/views/livewire/admin/user-form.blade.php'
---

# Views Livewire Admin

## User invite/edit moved to a full page (UserForm); no user-form modal
ManageUsers (admin/users) is list + filter + delete only. Invite/edit lives in App\Livewire\Admin\UserForm (routes admin.users.create / admin.users.edit {user}), following the ShelterForm pattern, and redirects to admin.users.index after saving. UserForm::mount() does the same access check as ManageUsers (admin or manager of any shelter), and for a manager it looks up the bound user again, limited to managedShelterIds(), so opening another shelter's user returns 404. On create, mount() adds one empty membership row. saveUser() clears userMemberships when userIsAdmin is set, so that row does not break the 'prohibited' rule. This replaces the editUser()/createUser()/$editingUserId API described in the livewire-admin and layouts-app rules. Tests are in tests/Feature/UserFormTest.php.

## Users list is scoped to the current shelter; UserForm memberships span every managed shelter
ManageUsers (list + delete) is scoped to the manager's current_shelter_id (switched via ShelterSwitcher): only users of the current shelter are listed, but each row's Shelters column (with a bell icon on memberships that have vaccination notifications on) eager-loads every membership within managedShelterIds(), never shelters the manager doesn't manage. There is no workingShelterId picker. Access to ManageUsers and UserForm requires isManagerOfCurrentShelter(), and the sidebar Users link uses the same check, so someone who manages shelter A is forbidden while acting in shelter B where they are staff. UserForm is different on purpose: a manager of several shelters can add, edit and remove a user's memberships in any shelter they manage (managedShelters() options, Rule::in(managedShelterIds())), and can open a user who belongs to any of those shelters. A new membership row defaults to the current shelter. Memberships in shelters the manager doesn't manage are never loaded, and they are kept when the form is saved.
