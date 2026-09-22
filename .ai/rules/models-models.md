---
paths:
  - 'app/Models/User.php,app/Models/Shelter.php'
---

# Models Models

## Users belong to shelters many-to-many via shelter_users; role/notifications are per-membership
users no longer has shelter_id/role/vaccination_notifications. It has boolean is_admin (global, no shelter) and nullable current_shelter_id (the shelter the user is currently acting within — read by MultiShelterTrait and every shelter-scoped screen). Actual shelter membership, role ('manager'/'staff'), and vaccination_notifications live on the shelter_users pivot (User::shelters()/Shelter::users(), both BelongsToMany with withPivot(['role','vaccination_notifications'])). Use User helpers roleForShelter(), isManagerOf(), isStaffOf(), belongsToShelter(), isManagerOfCurrentShelter(), managedShelterIds(), isManagerOfAnyShelter() instead of reading a role column. A user can belong to several shelters, possibly with a different role at each; current_shelter_id is switched via App\Livewire\ShelterSwitcher.
