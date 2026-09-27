---
paths:
  - 'app/Livewire/Pets/Concerns/FiltersPetsList.php,resources/views/livewire/pets/manage-pets.blade.php,app/Models/Pet.php'
---

# Livewire Pets Models

## Open health issues: Pet::openSicknesses() drives the list filter and heart icon
Pet::openSicknesses() = sicknesses() with pivot status in PetSickness::OPEN_STATUSES (active, chronic). FiltersPetsList eager-loads it and the missingDataFilter dropdown has 'open_health_issues' (first option after All), which excludes adopted/deceased pets like 'no_location'; the print list gets it for free via the shared trait. manage-pets.blade.php shows a micro heart icon next to the pet name (red if any active, amber if only chronic) with a tooltip naming the sicknesses, shown for any pet with open diagnoses. Uses already-compiled text-red-600/amber-600 classes, so no rebuild is needed.
