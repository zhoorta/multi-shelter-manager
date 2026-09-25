---
paths:
  - 'app/Livewire/Reports/**'
---

# Reports

## Reports: manager-only, PHP aggregation, server-side SVG charts
App\Livewire\Reports\ShelterReports (route reports.index) is manager-only: mount() does abort_unless(isManagerOfCurrentShelter()), and the sidebar link uses the same check (staff, viewers and admins get 403). Decided 2026-09-25, because finance reports will be added later. It loads the shelter's pets and approved adoptions once and aggregates them in PHP, so the numbers match on MySQL and SQLite (no YEAR()/strftime). The per-bucket population loop compares Y-m-d strings, because re-casting the date attributes took about 1s on the 1,347 Faial pets. Charts are Blade components in resources/views/components/charts (columns, line, bars) that draw SVG/HTML with inline styles and --chart-* CSS variables defined in the page view. They don't depend on a Tailwind rebuild and use no JS chart library (user's choice). The palette was validated for colour blindness against the white/neutral-900 cards. Each chart has a "Show table" view. Adoption applications are pruned after 6 months, so don't build historical funnels from them.
