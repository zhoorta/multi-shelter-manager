---
paths:
  - 'app/Models/Pet.php,app/Livewire/Pets/PetForm.php,app/Livewire/Pets/AdoptionForm.php,app/Livewire/Pets/ManageAdoptions.php'
  - 'app/Models/PetSickness.php,app/Livewire/Pets/DiagnosisForm.php,app/Livewire/Pets/PetShow.php,app/Livewire/Pets/PetForm.php'
---

# Livewire Pets Livewire Pets

## pets.status is a derived value, computed by Pet::determineStatus()
pets.status enum is now ['available', 'not_available', 'adopted', 'deceased'] (quarantine/medical removed). PetForm no longer exposes an editable status field/property at all — status is never user-set directly.

Pet::determineStatus() is the single source of truth: 'deceased' if date_of_death is filled (this wins even over an open adoption); else 'adopted' if the pet has an Adoption row with return_date null; else 'available' if is_adoptable, else 'not_available'.

Call sites that must call $pet->determineStatus() (after the relevant attributes/adoption rows are persisted, inside the same DB transaction) and write the result to pets.status:
- PetForm::savePet() — both create and update, since is_adoptable/date_of_death are editable there.
- AdoptionForm::saveAdoption() — after creating/updating the adoption record (only when there isn't another still-open adoption for the pet, per the existing $hasAnotherOpenAdoption guard).
- ManageAdoptions::deleteAdoption() — after soft-deleting an open adoption.

Any new code path that changes is_adoptable, date_of_death, or an Adoption's return_date must also recompute status via determineStatus(), not hardcode a status string.

## Diagnoses are records on the pet page, not switches in PetForm
PetForm no longer touches pet_sicknesses (the old toggles faked diagnosed_at=today, status=active, and detached on untoggle, deleting history). Diagnoses are created/edited via App\Livewire\Pets\DiagnosisForm (routes pets.diagnose / pets.diagnose.edit, mirrors VaccinationForm) and listed/soft-deleted in PetShow's Diagnoses table (deleteDiagnosis, scoped by pet_id). pet_sicknesses gained resolved_at (only kept when status=treated; form suggests today when switching to treated) and SoftDeletes; Pet::sicknesses()/Sickness::pets() withPivot includes 'id' and 'resolved_at' and use wherePivotNull('deleted_at'). A pet can have several diagnoses of the same sickness. Status list/labels/badge colors live in PetSickness::STATUSES/statusLabel()/statusColor().
