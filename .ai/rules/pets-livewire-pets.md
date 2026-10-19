---
paths:
  - 'app/Livewire/Pets/ManageAdoptions.php,app/Livewire/Pets/AdoptionShow.php'
  - 'app/Livewire/Pets/AdoptionForm.php,app/Livewire/Pets/ManageAdoptions.php'
---

# Pets Livewire Pets

## ManageAdoptions + AdoptionShow mirror the Sponsorship management pair
Adoptions management was built as a direct mirror of Sponsorships (see [[pets]] and [[livewire-pets]] notes on Sponsorship): ManageAdoptions (route pets.adoptions.index, "pets/adoptions", sidebar item under "Pets" right after "Sponsorships") lists every adoption across the shelter (scoped transitively via whereHas('pet'), since Adoption has no shelter_id) with search over name/phone/email/notes/pet name/pet ref, and a soft-delete action. AdoptionShow (route pets.adopt.show, "pets/{pet}/adopt/{adoption}", registered between pets.adopt and pets.adopt.edit) is a single-adoption page reusing the existing partials/adoption-box.blade.php partial (same one pet-show.blade.php loops over) — it has no payments sub-resource so, unlike SponsorshipShow, it does not use ManagesSponsorshipPayments.

## deleteAdoption() reverts pet status/checkout_date only when the deleted adoption was open
ManageAdoptions::deleteAdoption() checks the adoption's return_date before soft-deleting it (inside DB::transaction()): if return_date is null (the pet's currently-open adoption), the pet is reset to `status => 'available'` and `checkout_date => null`. If return_date is already set (a past, closed adoption), the pet's status/checkout_date is left untouched — deleting historical adoption records must never affect the pet's current state. This mirrors AdoptionForm::saveAdoption()'s return_date branching (see [[pets]]), but deleteAdoption() does not need the $hasAnotherOpenAdoption check that saveAdoption() has, since the business rule enforced there guarantees at most one open adoption per pet at a time.

## Adoption frees the box and turns is_adoptable off; a return or deleting the open adoption turns it back on
Superseding the older "non-adoptable pet returns as not_available" behaviour (changed 2026-10-19 at AFAMA's request). AdoptionForm::saveAdoption(), when an adoption becomes the open one (new, or its return date cleared), sets cage_id = null and is_adoptable = false, then status via determineStatus(); editing an adoption that was already open leaves the pet alone. A return date sets is_adoptable = true (alive pets) so the pet is 'available', with no cage until staff assign one; ManageAdoptions::deleteAdoption() of an open adoption does the same. Do not go back to reading is_adoptable as it was before the adoption: it is turned off by the adoption itself.
