---
paths:
  - 'app/Livewire/Settings/ShelterProfile.php,app/Livewire/Concerns/EditsShelterProfile.php,app/Livewire/Admin/ShelterForm.php,resources/views/livewire/partials/shelter-profile-fields.blade.php'
---

# Views Livewire Partials

## Managers edit their shelter profile; name and species stay admin-only; email required
Settings > Shelter (App\Livewire\Settings\ShelterProfile, route shelter-profile.edit) lets the manager of the current shelter edit short name, contacts, address, region, description and logo. Only admins (Admin\ShelterForm) can change the shelter name or species (user decision 2026-09-26). Shelter email is required in both forms. Both components share the fields, rules, attributes and logo handling through the App\Livewire\Concerns\EditsShelterProfile trait and the livewire.partials.shelter-profile-fields view, so change the profile fields there and not in either form. ShelterProfile locks shelterId and checks isManagerOf() again on save. The settings nav link is shown only when isManagerOfCurrentShelter().
