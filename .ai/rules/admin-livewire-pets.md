---
paths:
  - 'app/Models/Vaccine.php,app/Livewire/Admin/ManageVaccines.php,app/Livewire/Pets/VaccinationForm.php'
---

# Admin Livewire Pets

## Vaccine frequency_months auto-fills the next due date, never overwriting a typed one
vaccines.frequency_months (nullable, 1-120, set by admins in ManageVaccines; e.g. rabies = 36 by law) drives VaccinationForm::fillDueDateFromFrequency(), run on updatedVaccineId/updatedAdministeredDate (both fields are wire:model.live). It uses addMonthsNoOverflow (29 Feb + 36 months = 28 Feb). The #[Locked] $autoFilledDueDate tracks the last auto value: a dueDate that differs from it was typed by the user (or loaded when editing) and is never touched; an auto-filled one is recalculated, or cleared when the vaccine has no frequency. Null frequency = vet protocol decides, dates are entered by hand.
