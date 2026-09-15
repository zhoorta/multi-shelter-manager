---
paths:
  - 'app/Livewire/Dashboard.php,resources/views/livewire/dashboard.blade.php,tests/Feature/DashboardTest.php'
---

# Feature

## Dashboard has five overlapping pet-card sections — test isolation matters
Dashboard::mount() builds five independent Pet/Adoption/Sponsorship collections rendered as card grids, all sharing the same species-ref/photo/name format, in this on-page order: unknownLocationPets, recentAdoptions, recentPassings, recentSponsorships, recentIntakes.
- recentIntakes: whereNotNull('checkin_date'), orderByDesc(checkin_date, created_at), take 5
- recentAdoptions: Adoption whereHas pet.status='adopted', orderByDesc(adoption_date, created_at), take 5
- unknownLocationPets: status != 'adopted', date_of_death null, cage_id null, orderByDesc(created_at), take 5
- recentPassings: date_of_death not null, orderByDesc(date_of_death, created_at), take 5
- recentSponsorships: Sponsorship whereHas pet.shelter_id, orderByDesc(created_at), take 5 (no status filter — Sponsorship has no lifecycle field like Adoption.return_date; "valid"/"expired" is derived elsewhere from payments.end_date and intentionally not applied here)

A Pet::factory() default (no cage_id, status 'available', no checkin_date, no date_of_death) satisfies unknownLocationPets' criteria even when a test only cares about intakes, adoptions, passings, or sponsorships. Any Feature test asserting assertDontSee('Some Pet Name') on the full dashboard response must also give that pet a cage_id (or otherwise make it fail one of the sections' criteria — e.g. set date_of_death or status='adopted'), or it will leak into whichever section it does match and break the assertion. Prefer creating a Facility/Wing/Cage and assigning cage_id in incidental test pets that aren't the subject of the assertion. When a single test legitimately needs pets that satisfy unknownLocationPets AND must assert another pet is absent from just that section, assert against the component's collection directly (`Livewire::test(Dashboard::class)->get('unknownLocationPets')->pluck('id')`) instead of assertDontSee on the whole page.

Also: "Recent Adoptions"/"No Recent Adoptions"/"Pets with Unknown Location"/"No Pets with Unknown Location"/"Recent Passings"/"No Recent Passings"/"Recent Sponsorships"/"No Recent Sponsorships" keys live in lang/en.json + lang/pt.json — any new dashboard section heading needs both keys added to both files (PT-facing text rule).
