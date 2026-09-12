---
paths:
  - 'app/Livewire/Pets/SponsorshipShow.php,app/Livewire/Pets/PetShow.php,app/Livewire/Pets/Concerns/ManagesSponsorshipPayments.php'
---

# Concerns

## Sponsorship box + payment CRUD shared between PetShow and SponsorshipShow
SponsorshipPayment CRUD (createPayment/editPayment/savePayment/deletePayment) now lives in App\Livewire\Pets\Concerns\ManagesSponsorshipPayments, a trait used by both PetShow (lists every sponsorship for a pet) and SponsorshipShow (route pets.sponsor.show, "pets/{pet}/sponsor/{sponsorship}", a single-sponsorship page). The trait declares abstract scopedSponsorshipQuery()/scopedSponsorshipPaymentQuery()/refreshSponsorships() that each host class implements with its own scope (PetShow: all of the pet's sponsorships; SponsorshipShow: just the one it was resolved for) — don't move payment logic back into either class directly.

The per-sponsorship box markup (details + payments table) is the Blade partial resources/views/livewire/pets/partials/sponsorship-box.blade.php (expects $pet, $sponsorship); the shared "sponsorship-payment-form" modal is partials/sponsorship-payment-modal.blade.php. Both pet-show.blade.php and sponsorship-show.blade.php @include these rather than duplicating markup — edit the partial, not each page, when changing the box/modal.

ManageSponsorships (route pets.sponsorships.index, sidebar entry under "Pets") lists sponsorships across the shelter with a View action (icon "eye") linking to pets.sponsor.show — it intentionally has no Edit action there; editing sponsor details still goes through SponsorshipForm's pets.sponsor.edit route, reached from inside the box.
