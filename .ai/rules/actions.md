---
paths:
  - app/Actions/ExportShelterData.php
---

# Actions

## Shelter data export: explicit scoping, built in-request, no queue
Settings > Export data (manager only) builds a ZIP of CSVs (';' separator, BOM) and streams it, deleting the temp file after send; decided against a queued job + emailed link because production is shared hosting with no queue worker (user decision 2026-09-30). Every dataset scopes by shelter_id explicitly with withoutGlobalScope('shelter'), never by the logged-in user's current shelter (ExportShelterDataTest logs in as another shelter's manager). All table columns are exported except audit/technical ones (EXCLUDED_COLUMNS), so new columns appear automatically; a NEW table needs a new entry in datasets(). Cells starting with = + @ or -letter get a leading apostrophe against spreadsheet formula injection.
