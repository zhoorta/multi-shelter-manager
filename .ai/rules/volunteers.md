---
paths:
  - 'app/Livewire/Volunteers/**'
---

# Volunteers

## Volunteers module: list+show now, manager-only delete, staff view-only
Volunteer has a direct shelter_id column (like Pet/Facility/User) so it uses MultiShelterTrait + Blameable + SoftDeletes (see [[models]]). App\Livewire\Volunteers\ManageVolunteers (route volunteers.index) and VolunteerShow (route volunteers.show, implicit {volunteer} binding) both allow `manager` and `staff` via `abort_unless(in_array(role, ['manager','staff']))`, mirroring Facilities/ManageSpaces — but only list+show+delete exist so far, no create/edit form yet. deleteVolunteer() is gated by `abort_unless($this->isManager(), 403)` and the blade hides the delete trigger/modal for staff, matching the Facilities staff-view-only convention. gender/transport_mode/attendance_evaluation/performance_evaluation are plain DB enum columns (not PHP enums) stored/cast as strings, translated in the blade via `__($value)` against raw lang keys ('foot', 'very low', etc.) in lang/pt.json and lang/en.json. Sidebar link sits right before Facilities inside the same non-admin `@if` block in resources/views/layouts/app/sidebar.blade.php.

## VolunteerForm: manager-only full page, mount() gates the whole page not just actions
Create/edit now exist: App\Livewire\Volunteers\VolunteerForm (routes volunteers.create, volunteers.edit with implicit {volunteer} binding — MultiShelterTrait scoping 404s a cross-shelter edit). Unlike ManageSpaces (single page, gates each mutating action individually), VolunteerForm is a dedicated full page like ShelterForm/PetForm, so mount() gates the entire page with `abort_unless(Auth::user()->role === 'manager', 403)` — staff get 403 just visiting /volunteers/create, they never reach an action-level check. ManageVolunteers' Create button and each row's Edit button are both wrapped in `@if (auth()->user()->role === 'manager')`, same as the existing Delete gating. Photo upload follows ShelterForm's single-image pattern (WithFileUploads, `existingImagePath` + delete-old-then-store-new on replace into the `volunteers` disk path), not PetForm's multi-photo pattern. volunteerEndDate validates `after_or_equal:volunteerStartDate`.
