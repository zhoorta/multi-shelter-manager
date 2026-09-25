---
paths:
  - app/Console/Commands/ImportPortugalZoofilo.php
---

# Commands

## Portugal Zoófilo import: PZ refs, notes markers, never follow portal redirects
app:import-portugal-zoofilo --animal=cao|gato imports PZ exports (animals/adoptions/sponsorships CSVs, ';'-separated, "-1" = empty). Species come from the ANIMALS map (species name + portal page path); species with no sizes (cats) skip size. Imported pets use ref "PZ{animal_id}" (not PET00000) and are matched on shelter_id+ref so re-runs update. Adoptions/sponsorships are matched on "[PZ adopc N]" / "[PZ apad N]" markers kept in notes — don't strip them. Status always comes from Pet::determineStatus() after adoptions load. Portal HTTP calls must use withoutRedirecting(): unpublished animals' pages 302-redirect to an unrelated animal's page, which would import the wrong biography. Descriptions go through Pet::sanitizeDescription() (shared with PetForm).

## Portugal Zoófilo members import: --members, no guessed data
--members=socios.csv (PZ "socios_todos" export, header starts with pessoa_id) imports members on its own; --animal/--animals are only required when importing animals. Members are matched on shelter + "[PZ socio {pessoa_id}]" in notes. socio_referencia is usually free text: it becomes member_number only when numeric and free (trashed included), otherwise the number is automatic and the reference goes to the notes. Cancellation date → status 'left'. Joia isn't exported, so it's 0. Fee frequency is assumed yearly. Checked against PZ's demo export: socio_quota_paga is the last year paid or empty, and gets a 0-value placeholder payment for that year (any other value goes to notes + warning); socio_quota_definida is a plain amount. pessoa_bi (ID card) and auth_code are deliberately not imported.
