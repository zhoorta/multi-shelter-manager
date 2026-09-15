---
paths:
  - 'app/Livewire/Pets/**'
  - app/Livewire/Pets/SponsorshipForm.php
  - app/Livewire/Pets/PetShow.php
  - app/Livewire/Pets/AdoptionForm.php
  - app/Livewire/Pets/ManagePets.php
---

# Pets

## pets.gender enum only allows male/female — 'unknown' is a latent bug
The pets.gender column is `enum('gender', ['male', 'female'])` — no 'unknown' value exists at the DB level. PetForm's validation ('petGender' => 'in:male,female,unknown') and the gender <flux:select> in pet-form.blade.php both still offer 'unknown', which will throw a DB constraint violation if selected. PetFactory was fixed to only generate male/female. Not fixed in the UI/validation yet — decide whether to widen the DB enum or drop 'unknown' from the form before shipping.

## Gender is male/female only — 'unknown' removed
The previously-documented latent bug is fixed: PetForm's validation ('petGender' => 'in:male,female') and the gender <flux:select> in pet-form.blade.php no longer offer 'unknown', matching the pets.gender DB enum (male/female only). Don't reintroduce an 'unknown' option without widening the DB enum first.

## pets.ref format is PET + zero-padded pet_id, set after insert
pets.ref is generated from the pet's own id (format PET00001, PET00002, ...), not a random string — so it's naturally unique with no DB check needed. Since the id doesn't exist until after INSERT, PetForm::savePet() creates the Pet with a temporary empty ref, then immediately updates it via generatePetRef($pet->id), all inside the same DB::transaction(). Only happens on create — editing never touches ref. PetFactory mirrors this with an afterCreating() hook (definition() can't know the id either). Do not go back to a random Str::random()-based ref.

## pets.description is a contenteditable rich-text field, sanitized server-side
PetForm has no wire:model on the description editor — pet-form.blade.php uses a contenteditable div driven by Alpine (bold/italic/underline/list buttons via document.execCommand), synced to $wire.set('petDescription', html, false) on input. wire:model was avoided deliberately: Livewire's DOM morphing on a bound contenteditable element fights the browser's cursor/selection on every keystroke.

Stored HTML is never trusted as-is: PetForm::sanitizeDescription() strips to an allowlist (<p><br><b><strong><i><em><u><ul><ol><li>) and strips all attributes from surviving tags (regex, since strip_tags() keeps attributes on allowed tags) before saving to pets.description. Returns null when only empty markup remains (e.g. contenteditable's stray "<p><br></p>"). Any future code that renders pets.description must still treat it as HTML needing `{!! !!}`, not `{{ }}` — sanitization happens on write, not on read.

## SponsorshipForm supports editing; pet-show lists every sponsorship
SponsorshipForm handles both create (route pets.sponsor, mount(Pet $pet)) and edit (route pets.sponsor.edit "pets/{pet}/sponsor/{sponsorship}/edit", mount(Pet $pet, ?Sponsorship $sponsorship)) via the same component, mirroring PetForm's create/edit pattern. mount() aborts 404 if the given sponsorship's pet_id doesn't match the route pet (Sponsorship has no shelter_id, so route model binding alone can't scope it — see the existing note on transitive scoping through pet). saveSponsorship() branches on $this->sponsorship !== null to update() vs create().

pet-show.blade.php renders one card per sponsorship (@foreach over $pet->sponsorships, eager-loaded ->latest() in PetShow::mount(), so most recent first), each with its own Edit button linking to pets.sponsor.edit for that specific sponsorship — it is NOT limited to the latest one. Still does not touch pet.status.

## SponsorshipPayment CRUD lives in PetShow, modal-based like ManageBreeds
Unlike Sponsorship itself (a separate route/page via SponsorshipForm), SponsorshipPayment create/edit/delete is handled directly by PetShow (app/Livewire/Pets/PetShow.php) via a single shared `sponsorship-payment-form` flux:modal plus a per-payment `confirm-payment-deletion-{id}` modal in pet-show.blade.php — the same modal-in-page pattern ManageBreeds uses for Breed, not a dedicated form page/route.

PetShow::mount() eager-loads `sponsorships.payments` (payments ordered ->orderByDesc('payment_date')); createPayment(int $sponsorshipId)/editPayment(int $paymentId)/deletePayment(int $paymentId) each scope through scopedSponsorshipQuery()/scopedSponsorshipPaymentQuery() (both filter by `pet_id` on $this->pet, since neither Sponsorship nor SponsorshipPayment has a shelter_id) and findOrFail, so a payment/sponsorship id from another pet 404s. After any create/update/delete, refreshSponsorships() reloads the relation so the box reflects the change without navigation.

The "Add Payment" button and the shared payment-form modal only render when the pet has at least one sponsorship (`@if ($pet->sponsorships->isNotEmpty())` around the modal) — with none, there's no sponsorship to attach a payment to. This also matters for tests: Livewire always serializes every public property (including paymentSponsorshipId) into the page's wire:snapshot HTML attribute regardless of what's visibly rendered, so a test asserting no sponsorship UI leaked must use `assertDontSeeText()` (strips tags/attributes) rather than `assertDontSee()`, which would false-fail on the word "Sponsorship" inside that attribute.

## SponsorshipPayment create form: default required date fields to today
PetShow::createPayment() sets paymentStartDate/paymentEndDate/paymentDate to today (Y-m-d) instead of leaving them empty. Reason: the app-wide Safari date-input workaround (type starts as "text", switches to "date" on focus — see pet-show.blade.php / pet-form.blade.php) only hides the WebKit bug where an empty type="date" input visually shows today's date. That's harmless for nullable date fields (petBirthDate etc.) but broke required ones here: users saw what looked like a filled date, submitted without touching it, and got "required" errors. Defaulting to a real, non-empty value sidesteps the WebKit quirk entirely instead of fighting it. Don't revert to empty defaults for these three fields without also solving the Safari empty-date rendering issue differently. Tests in tests/Feature/PetShowTest.php explicitly clear the date fields to still exercise the "required" validation path.

## SponsorshipPayment create form: default required date fields to today
PetShow::createPayment() sets paymentStartDate/paymentDate to today (Y-m-d) and paymentEndDate to one year from today, instead of leaving them empty. Reason: the app-wide Safari date-input workaround (type starts as "text", switches to "date" on focus — see pet-show.blade.php / pet-form.blade.php) only hides the WebKit bug where an empty type="date" input visually shows today's date. That's harmless for nullable date fields (petBirthDate etc.) but broke required ones here: users saw what looked like a filled date, submitted without touching it, and got "required" errors. Defaulting to real, non-empty values sidesteps the WebKit quirk entirely instead of fighting it; the one-year end date matches a typical annual sponsorship period. Don't revert to empty defaults for these three fields without also solving the Safari empty-date rendering issue differently. Tests in tests/Feature/PetShowTest.php explicitly clear the date fields to still exercise the "required" validation path.

## A non-null Adoption.return_date reverts the pet to 'available'
AdoptionForm::saveAdoption() branches on whether the submitted return_date is non-null: if so, pet.status is set to 'available' and pet.checkout_date is cleared to null (instead of 'adopted' + checkout_date = adoption_date). This re-enables the pets.adopt create route (AdoptionForm::mount() only 403s the create form when pet.status === 'adopted') and re-shows the "Adoption Registration" link in pet-show.blade.php, both driven purely by pet.status. A pet can accumulate multiple Adoption rows over time this way (adopted → returned → re-adopted); nothing deletes prior rows.

## Editing a non-active Adoption must not touch pet status/checkout_date
A pet can have multiple Adoption rows over time (adopted → returned → re-adopted). AdoptionForm::saveAdoption() computes $hasAnotherOpenAdoption (another adoption row for the pet with return_date null, excluding the one being edited). If true: (1) clearing this adoption's return_date is rejected with a validation error on returnDate (would imply two concurrent open adoptions for the pet), and (2) any other edit to this now-past adoption skips the $this->pet->update(['status'=>..., 'checkout_date'=>...]) call entirely, since the newer open adoption is what determines the pet's current status/checkout_date. Only the pet's single currently-open adoption should ever drive pet.status/checkout_date.

## no_location missingDataFilter excludes adopted/deceased pets
Superseding the earlier note here: 'no_location' is now `whereNull('cage_id')->whereNotIn('status', ['adopted', 'deceased'])`, not just whereNull('cage_id'). Reason: an adopted or deceased pet has legitimately left the shelter, so having no cage assigned isn't a data-quality issue worth surfacing in this filter.
