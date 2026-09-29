---
paths:
  - 'app/Livewire/Pets/VaccinationPlan.php,app/Livewire/Pets/VaccinationPlanPrint.php,app/Livewire/Pets/GroupVaccinationForm.php,app/Livewire/Pets/Concerns/ListsDueVaccinations.php'
---

# Pets Concerns

## Vaccination plan, vet list and group vaccination share one "due" query
Built 2026-09-29 for AFAMA (vaccinates dog groups in one month). ListsDueVaccinations::dueVaccinationsQuery() = PetVaccine::pending() of Pet::resident() pets (whereIn pet_id, keeps the shelter scope), bucketed by due month; month 0 = due before the plan's year. VaccinationPlan (pets/vaccinations/plan, viewers too) shows vaccine × month counts, a month's pets grouped per pet, links to VaccinationPlanPrint (layouts.print, blank "given" column) and to GroupVaccinationForm with ?vaccine=&due=Y-m. The group form (staff/manager) lists and pre-ticks residents with that vaccine pending IN the due month (the same pets as the plan's cell), "overdue" (due before today; the plan's "before" column links there) or "all" residents of the species; no due param defaults to the current month. Never make it cumulative ("due by"): from May 2028 it ticked 86 dogs instead of the cell's 38, sets lot/vet once, and each dose fulfils the pet's open scheduled row like VaccinationForm. Compare due_date with whereDate, not where/whereBetween: SQLite stores pivot dates as "Y-m-d 00:00:00" and drops the month's last day.
