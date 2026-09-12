---
paths:
  - 'app/Livewire/Pets/ManageAdoptions.php,app/Livewire/Pets/AdoptionShow.php'
---

# Pets Livewire Pets

## ManageAdoptions + AdoptionShow mirror the Sponsorship management pair
Adoptions management was built as a direct mirror of Sponsorships (see [[pets]] and [[livewire-pets]] notes on Sponsorship): ManageAdoptions (route pets.adoptions.index, "pets/adoptions", sidebar item under "Pets" right after "Sponsorships") lists every adoption across the shelter (scoped transitively via whereHas('pet'), since Adoption has no shelter_id) with search over name/phone/email/notes/pet name/pet ref, and a soft-delete action. AdoptionShow (route pets.adopt.show, "pets/{pet}/adopt/{adoption}", registered between pets.adopt and pets.adopt.edit) is a single-adoption page reusing the existing partials/adoption-box.blade.php partial (same one pet-show.blade.php loops over) — it has no payments sub-resource so, unlike SponsorshipShow, it does not use ManagesSponsorshipPayments.

## deleteAdoption() reverts pet status/checkout_date only when the deleted adoption was open
ManageAdoptions::deleteAdoption() checks the adoption's return_date before soft-deleting it (inside DB::transaction()): if return_date is null (the pet's currently-open adoption), the pet is reset to `status => 'available'` and `checkout_date => null`. If return_date is already set (a past, closed adoption), the pet's status/checkout_date is left untouched — deleting historical adoption records must never affect the pet's current state. This mirrors AdoptionForm::saveAdoption()'s return_date branching (see [[pets]]), but deleteAdoption() does not need the $hasAnotherOpenAdoption check that saveAdoption() has, since the business rule enforced there guarantees at most one open adoption per pet at a time.
