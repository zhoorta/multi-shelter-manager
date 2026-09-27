---
paths:
  - 'app/Models/Pet.php,app/Livewire/Pets/PetForm.php,app/Console/Commands/ImportPortugalZoofilo.php,app/Livewire/Reports/Concerns/BuildsHealthReport.php'
---

# Reports Concerns

## Neutering details: is_neutered stays the source of truth, details may be unknown
pets.is_neutered (bool) is still what the portal, importer and "% sterilised" use. Added 2026-09-27: neutered_at + neutered_by_shelter (nullable = unknown; old/PZ data has neither, never guess them) for neutered pets; neutering_status (pending/scheduled/not_recommended, Pet::NEUTERING_STATUSES) + neutering_scheduled_at + neutering_notes for pets that aren't. PetForm::neuteringAttributes() clears the side that doesn't match the switch. Switching an existing non-neutered pet on suggests the scheduled date (or today) and "the shelter"; new pets get no suggestion. New pets and PZ-imported pets in the shelter that aren't neutered start as pending (the importer keeps a status set since). The "Sterilisations performed" report tile only counts neutered_by_shelter=true with neutered_at in the period.
