---
paths:
  - 'app/Models/Region.php,app/Models/Shelter.php,app/Livewire/Admin/ShelterForm.php'
---

# Models Livewire Admin

## Shelter "distrito" is modelled as a generic Region
Portuguese "distritos" live in a country-neutral `regions` lookup table (name only, unique — user explicitly does not want a country_code column) referenced by shelters.region_id (nullable FK, RESTRICT). The name is deliberately generic (not "district"/"location") so other countries' states/provinces fit later; the UI label is __('Region'), translated "Distrito" in pt.json. Seeded in DatabaseSeeder with the 18 mainland districts plus "Açores - Faial" and "Açores - Santa Maria" (user's choice — no Madeira entry). Region is optional on shelters (ShelterForm: nullable|exists:regions,id). Region uses Blameable + SoftDeletes. Migration order has a cycle (shelters.region_id -> regions -> users (blameable) -> shelters (current_shelter_id)), broken by placing create_regions_table at 000009 (after users) and having it add the shelters.region_id FOREIGN KEY constraint via Schema::table; the region_id column itself stays in create_shelters_table without ->constrained(). ManageRegions::deleteRegion() nulls shelters.region_id (incl. trashed shelters) in the same transaction as the soft delete — soft deletes don't fire the FK, and a shelter left pointing at a trashed region could not be unassigned in the form. ShelterForm validates with Rule::exists('regions','id')->withoutTrashed(). Shelter::region() keeps ->withTrashed() as a safety net only. Admin CRUD is App\Livewire\Admin\ManageRegions (route admin.regions.index, sidebar right after Shelters), a copy of the ManageFurTypes pattern.
