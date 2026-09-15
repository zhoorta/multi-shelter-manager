---
paths:
  - 'app/Livewire/Pets/SponsorshipShow.php,app/Livewire/Pets/PetShow.php,app/Livewire/Pets/Concerns/ManagesSponsorshipPayments.php'
  - 'app/Livewire/Pets/ManagePets.php,app/Livewire/Pets/PetPrintList.php,app/Livewire/Pets/Concerns/FiltersPetsList.php'
---

# Concerns

## Sponsorship box + payment CRUD shared between PetShow and SponsorshipShow
SponsorshipPayment CRUD (createPayment/editPayment/savePayment/deletePayment) now lives in App\Livewire\Pets\Concerns\ManagesSponsorshipPayments, a trait used by both PetShow (lists every sponsorship for a pet) and SponsorshipShow (route pets.sponsor.show, "pets/{pet}/sponsor/{sponsorship}", a single-sponsorship page). The trait declares abstract scopedSponsorshipQuery()/scopedSponsorshipPaymentQuery()/refreshSponsorships() that each host class implements with its own scope (PetShow: all of the pet's sponsorships; SponsorshipShow: just the one it was resolved for) — don't move payment logic back into either class directly.

The per-sponsorship box markup (details + payments table) is the Blade partial resources/views/livewire/pets/partials/sponsorship-box.blade.php (expects $pet, $sponsorship); the shared "sponsorship-payment-form" modal is partials/sponsorship-payment-modal.blade.php. Both pet-show.blade.php and sponsorship-show.blade.php @include these rather than duplicating markup — edit the partial, not each page, when changing the box/modal.

ManageSponsorships (route pets.sponsorships.index, sidebar entry under "Pets") lists sponsorships across the shelter with a View action (icon "eye") linking to pets.sponsor.show — it intentionally has no Edit action there; editing sponsor details still goes through SponsorshipForm's pets.sponsor.edit route, reached from inside the box.

## Pet list filtering lives in FiltersPetsList, shared by ManagePets and PetPrintList
ManagePets' search/status/location/species/missingData filter query-building was extracted into app/Livewire/Pets/Concerns/FiltersPetsList (filteredPetsQuery(): Builder<Pet>, PHPDoc-typed for Larastan), the same trait-for-shared-Livewire-logic pattern as ManagesSponsorshipPayments. PetPrintList (route pets/print, name pets.print.list — a printable version of the pets list, opened in a new tab like pets.print) uses the same trait so the two never drift. PetPrintList's 5 filter properties are declared with #[Url] (unlike ManagePets, which only has speciesFilter as #[Url]) so a GET request's query string hydrates them directly — manage-pets.blade.php's Print button builds that query string explicitly from the live component's current filter values via route('pets.print.list', array_filter([...])), so the printout matches whatever is on screen. Keep any future filter added to ManagePets' pets() query in the shared trait, not copy-pasted into PetPrintList.

Route ordering matters: 'pets/print' must be registered before 'pets/{pet}' (pets.show) in routes/web.php, same reasoning as pets/create — otherwise Laravel would match it as pets.show with pet param "print".
