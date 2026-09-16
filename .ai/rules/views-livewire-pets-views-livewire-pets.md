---
paths:
  - 'app/Models/PetVaccine.php,app/Livewire/Pets/VaccinationForm.php,app/Livewire/Pets/PetShow.php,app/Livewire/Pets/ManageVaccinations.php,resources/views/livewire/pets/pet-show.blade.php,resources/views/livewire/pets/manage-vaccinations.blade.php'
---

# Views Livewire Pets Views Livewire Pets

## pet_vaccines: administered_date/due_date are both nullable, status is derived, highlighting is scheduled-only
pet_vaccines was reworked (base migration, this app isn't deployed yet — see the existing note in models-livewire-pets-livewire-pets.md): administered_at/next_due_at renamed to administered_date/due_date and BOTH are now nullable, plus a new `status` enum column (`scheduled`, `administered`, `canceled` — `canceled` is not used by any app code yet) and an unused `notification_date` timestamp. A row now represents either a future reminder (due_date only, status=scheduled) or a logged dose (administered_date set, status=administered, optionally with its own due_date for the next booster).

VaccinationForm::saveVaccination() requires at least one of administeredDate/dueDate (manual addError on 'administeredDate' if both blank — not a Laravel validation rule, so assert with `assertHasErrors(['administeredDate'])`, not a rule-keyed array) and derives status itself: `'administered'` when administeredDate is filled, else `'scheduled'`. This is the ONLY place status is set — it is not a model event/boot hook (mirrors Pet::determineStatus() being called explicitly by callers, not automatically).

Highlighting (red = overdue, amber = due within a week) in both pet-show.blade.php's vaccinations table and manage-vaccinations.blade.php was simplified to a single-row check: only rows with administered_date === null (i.e. status scheduled) are ever colored, comparing due_date to today directly. This replaces the old cross-row "does another pivot row for the same pet+vaccine have a later administered_at" query entirely — an administered row is never colored regardless of its own due_date, since a pending follow-up is now expected to be its own separate scheduled row rather than inferred from this row. Don't reintroduce the cross-row check.

PetShow's and ManageVaccinations' vaccine ordering can no longer use `orderByPivot('administered_at', 'desc')` / `->latest('administered_at')` (administered_date can be null) — both use `orderByRaw('COALESCE(...administered_date, ...due_date) desc')` instead (PetShow qualifies columns as `pet_vaccines.administered_date`/`pet_vaccines.due_date` since it orders through the BelongsToMany pivot join; ManageVaccinations queries PetVaccine directly so uses unqualified column names).

Vaccine::pets() and Pet::vaccines() withPivot() now both include `'status'` alongside `administered_date`/`due_date` — add any new pet_vaccines column there too or it reads back null via ->pivot (see the existing Blameable/withPivot rule in models.md).
