---
paths:
  - 'app/Models/PetTreatment.php,app/Models/Treatment.php,app/Traits/TracksDueDates.php,app/Livewire/Pets/TreatmentForm.php,app/Livewire/Pets/GroupTreatmentForm.php,app/Livewire/Pets/ManagePetTreatments.php,app/Console/Commands/SendTreatmentDueNotifications.php'
---

# Console Commands

## Preventive treatments are separate from vaccines; neutering stays on pets
Decided 2026-09-28 (AFAMA deworming request): deworming/antiparasitics live in `treatments` (global admin catalogue, species pivot, frequency_months) + `pet_treatments` (plain Model, hasMany from Pet, has `product`), never as Vaccine rows, so vaccine reports/lists/reminders stay vaccine-only. The pending rule and "a dose fulfils the open scheduled row" logic are shared via App\Traits\TracksDueDates (pending() scope + scheduledRecordFulfilledBy(); model declares static dueDateKindColumn()), and the next-date auto-fill via App\Livewire\Pets\Concerns\FillsDueDateFromFrequency, used by VaccinationForm too — change them once. GroupTreatmentForm records one round for many Pet::resident() pets (status not adopted/deceased), re-querying ticked ids through its candidate query. SendTreatmentDueNotifications mails one row per treatment+due date and reuses shelter_users.vaccination_notifications (label "Vaccination and Treatment Notifications"). Neutering was deliberately NOT moved here (Faial has 891 neutered pets, 2 with dates).
