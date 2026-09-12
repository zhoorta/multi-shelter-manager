---
paths:
  - 'app/Livewire/Admin/ShelterForm.php,app/Livewire/Admin/ManageShelters.php,app/Models/Shelter.php,resources/views/layouts/app/sidebar.blade.php'
---

# App

## Shelter create/edit moved to a full page; species enablement gates the sidebar
ManageShelters (admin/shelters) is list+delete only now. Create/edit lives in a separate full-page component App\Livewire\Admin\ShelterForm (routes admin.shelters.create / admin.shelters.edit), following the PetForm pattern — no more shelter-form modal.

Shelter::species() is BelongsToMany(Species::class, 'shelter_species')->withTimestamps() — a plain pivot with no extra boolean column; a species is "enabled" for a shelter simply by the pivot row existing (mirrors Vaccine::species()/Sickness::species()). ShelterForm toggles species in-memory (shelterSpeciesIds, like PetForm's petSicknessIds/toggleSickness) and syncs the pivot only on saveShelter(), so toggling works before the shelter is even created.

resources/views/layouts/app/sidebar.blade.php's Pets group now iterates `auth()->user()->shelter?->species()->orderBy('name')->get() ?? []` instead of `Species::query()->...->get()` — so the sidebar only shows species enabled for the logged-in manager/staff's own shelter. Don't revert to the global Species query; that would leak species across shelters and defeat the shelter_species setting.
