---
paths:
  - 'app/Models/Sponsorship.php,app/Models/SponsorshipPayment.php,app/Livewire/Pets/SponsorshipForm.php'
---

# Livewire Pets

## Sponsorship models: no shelter_id, gated by pet.is_sponsorable
Sponsorship (Blameable + SoftDeletes) belongs to Pet and has many SponsorshipPayment (also Blameable + SoftDeletes); neither table has a shelter_id column, so they're scoped transitively through pet (no MultiShelterTrait — same pattern as Adoption).

SponsorshipForm handles both create (route pets.sponsor, "pets/{pet}/sponsor") and edit (route pets.sponsor.edit, "pets/{pet}/sponsor/{sponsorship}/edit", mirrors PetForm's create/edit pattern) of the Sponsorship record (name/email/phone/address/postal_code/city/send_feedback/send_newsletter/notes) — it never touches pet.status. mount() aborts (403) unless the pet has is_sponsorable === true, and (404) if an edited sponsorship's pet_id doesn't match the route pet; the pet-show dropdown link and the sponsorship box's Edit button are shown under the same is_sponsorable condition, independent of pet.status (so it can coexist with an already-adopted pet, unlike the adoption link which hides once status === 'adopted').

SponsorshipPayment CRUD (start_date/end_date/payment_date/payment_value/notes) lives directly in PetShow (app/Livewire/Pets/PetShow.php), not SponsorshipForm — see [[pets]] for the details (modal-based create/edit/delete inline in pet-show.blade.php's sponsorship box, mirroring ManageBreeds' modal pattern).
