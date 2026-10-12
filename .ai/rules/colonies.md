---
paths:
  - 'app/Livewire/Colonies/**'
---

# Colonies

## Colonies: one species per colony, colony animals are pets without a cage
Decided 2026-10-12 for Onde há Gato não há Rato (CED/CER cat colonies). A colony (colonies table, MultiShelterTrait + Blameable + SoftDeletes) has one required species_id, and only species flagged species.has_colonies can be picked (no backfill: tick Cat by hand after migrating). A colony animal is a normal Pet with pets.colony_id plus captured_at, returned_at and is_ear_tipped; PetForm hides the cage and saves cage_id null when a colony is set, requires the colony's species to match the pet's, and the species of a colony cannot change once it has animals. 'colonies' is a per-shelter module (missing key = ON). Colony animals are excluded from the 'no_location' list filter; the pets list, FiltersPetsList and both group forms filter by 'colony:{id}'. Colony info (caretaker, phone, notes) is people's data: hidden from viewers. The data export has a 'colonies' dataset.
