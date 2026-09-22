---
paths:
  - 'app/Models/Region.php,app/Models/Shelter.php,app/Livewire/Admin/ShelterForm.php'
---

# Models Livewire Admin

## Shelter "distrito" is modelled as a generic Region
Portuguese "distritos" live in a country-neutral `regions` lookup table (name only, unique — user explicitly does not want a country_code column) referenced by shelters.region_id (nullable FK, RESTRICT). The name is deliberately generic (not "district"/"location") so other countries' states/provinces fit later; the UI label is __('Region'), translated "Distrito" in pt.json. Seeded in DatabaseSeeder with the 18 mainland districts plus "Açores - Faial" and "Açores - Santa Maria" (user's choice — no Madeira entry). Region is optional on shelters (ShelterForm: nullable|exists:regions,id). Region uses SoftDeletes (no Blameable: the regions migration must sort before shelters, which is before users, so FKs to users are impossible there). Shelter::region() uses ->withTrashed() so a shelter keeps its region after an admin soft-deletes it. Admin CRUD is App\Livewire\Admin\ManageRegions (route admin.regions.index, sidebar right after Shelters), a copy of the ManageFurTypes pattern.
