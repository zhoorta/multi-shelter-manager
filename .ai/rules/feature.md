---
paths:
  - 'app/Livewire/Dashboard.php,resources/views/livewire/dashboard.blade.php,tests/Feature/DashboardTest.php'
---

# Feature

## Dashboard pet-card sections overlap — test isolation matters
Two groups of pet cards, all rendered through resources/views/livewire/partials/dashboard-pet-card.blade.php (species - ref, photo, name, then a `details` array of context lines; empty lines skipped).
"Needs attention" (heading and each section hidden when empty; "View all" links to pets.index?missingDataFilter=… — ManagePets::$missingDataFilter is #[Url] for this):
- unknownLocationPets (public prop): not adopted, no date_of_death, cage_id null, orderByDesc(created_at); detail "N days ago" from checkin_date ?? created_at
- petsWithOpenHealthIssues (#[Computed]): in care, whereHas openSicknesses, latest open diagnosed_at first; detail = diagnosis names
- sponsorshipsToRenew (#[Computed], only when showsActionCounters): pet in care, max payment end_date within ±30 days of today (SPONSORSHIP_RENEWAL_WINDOW_DAYS), soonest first; detail = sponsor name + "Valid until/Expired at"; links to pets.sponsor.show
- petsWithoutPhoto (#[Computed]): in care, no images, newest checkin first
- longestWaitingPets (#[Computed]): status 'available', checkin_date not null, oldest checkin first; detail = time_in_captivity
"Recent activity" (always shown, with "No …" placeholders): recentIntakes (detail: checkin date + "wing · cage" or "No location defined"), recentAdoptions (adoption date + adopter first name, hidden from viewers), recentPassings (date of death). "Recent Sponsorships" was replaced by sponsorshipsToRenew on purpose.
New lists are #[Computed], not public props: eager-loaded relation names are serialized into the wire:snapshot (e.g. "openSicknesses" would leak "Sicknesses" and break the admin-links assertDontSee test — see below).

A Pet::factory() default (no cage, status 'available', no images) lands in unknownLocationPets and petsWithoutPhoto, and with a checkin_date also in longestWaitingPets. So never assertDontSee a pet name on the whole dashboard to prove it's missing from one section: assert against that section's collection instead (`Livewire::test(Dashboard::class)->get('recentIntakes')` for public props, `->instance()->petsWithoutPhoto` for computed ones).

## speciesWithoutBreeds is a #[Computed] method, not a public property — avoid property names containing "breeds"/"species" substrings in Dashboard
Dashboard warns non-admin users when a species enabled for their shelter (Shelter::species()) has no Breed rows (whereDoesntHave('breeds')). This is exposed as #[Computed] public function speciesWithoutBreeds(): SupportCollection, accessed in the blade as $this->speciesWithoutBreeds — NOT a public property. Reason: Livewire serializes all public property names (not just values) into the wire:snapshot data attribute embedded in the page HTML, and Illuminate's assertDontSee() is case-insensitive by default. A public $speciesWithoutBreeds property leaked the literal substring "Breeds" into every render, breaking the existing "staff and managers do not see the administration navigation links" test (which asserts dontSee('Breeds')) even though the warning itself was never displayed. Any new Dashboard state whose name would collide (case-insensitively) with an admin-only nav label (Species, Breeds, Fur Types, Vaccines, Sicknesses, Shelters) must be a #[Computed] method, not a public property, for this reason. Message keys live in all ten lang/*.json files (see [[lang]]).

## Dashboard counters: state row for everyone, action row only for managers/staff
Six cards, lg:grid-cols-3. Row 1 (everyone): Pets in Shelter (activePetsCount), Available Capacity, Adoptions This Year (Adoption rows with application_status 'Approved', adoption_date in the current year and no return_date). Row 2 is gated by $showsActionCounters (not admin, not viewer), because those users get a 403 on the linked lists: Pending Applications -> pets.applications.index (pending by default), Overdue Vaccinations -> pets.vaccinations.index?nextDueFilter=overdue (same rule as the list filter: status scheduled, due_date < today; nextDueFilter is #[Url]), Fees overdue -> members.index?inArrearsOnly=1 (Member::inArrears()). The count turns amber above 0. Keep each count matching its list filter so the number agrees with what the link shows. The "Total Staff" counter was removed on purpose.
