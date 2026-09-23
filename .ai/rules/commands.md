---
paths:
  - app/Console/Commands/ImportPortugalZoofilo.php
---

# Commands

## Portugal Zoófilo import: PZ refs, notes markers, never follow portal redirects
app:import-portugal-zoofilo --animal=cao|gato imports PZ exports (animals/adoptions/sponsorships CSVs, ';'-separated, "-1" = empty). Species come from the ANIMALS map (species name + portal page path); species with no sizes (cats) skip size. Imported pets use ref "PZ{animal_id}" (not PET00000) and are matched on shelter_id+ref so re-runs update. Adoptions/sponsorships are matched on "[PZ adopc N]" / "[PZ apad N]" markers kept in notes — don't strip them. Status always comes from Pet::determineStatus() after adoptions load. Portal HTTP calls must use withoutRedirecting(): unpublished animals' pages 302-redirect to an unrelated animal's page, which would import the wrong biography. Descriptions go through Pet::sanitizeDescription() (shared with PetForm).
