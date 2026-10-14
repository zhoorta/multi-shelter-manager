---
paths:
  - app/Livewire/Concerns/EditsShelterProfile.php
---

# Livewire Concerns

## Managers can toggle species (supersedes "species admin-only", 2026-10-14)
Species switches (shelterSpeciesIds, toggleSpecies, species(), livewire.partials.shelter-species-fields) live in EditsShelterProfile and are used by both Admin\ShelterForm and Settings\ShelterProfile; the manager saves them with the profile (sync). Only name and public address stay admin-only. A species cannot be switched off while the shelter has pets, colonies or cages of it (checked at toggle time, ignoring other shelters); components implement editedShelter(). The Colonies switch follows the ticked species (offersColonies()).
