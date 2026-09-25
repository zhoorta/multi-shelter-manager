---
paths:
  - 'app/Livewire/Reports/**'
---

# Reports

## Reports: manager-only, PHP aggregation, server-side SVG charts
App\Livewire\Reports\ShelterReports (route reports.index) is manager-only: mount() does abort_unless(isManagerOfCurrentShelter()), and the sidebar link uses the same check (staff, viewers and admins get 403). Decided 2026-09-25, because finance reports will be added later. It loads the shelter's pets and approved adoptions once and aggregates them in PHP, so the numbers match on MySQL and SQLite (no YEAR()/strftime). The per-bucket population loop compares Y-m-d strings, because re-casting the date attributes took about 1s on the 1,347 Faial pets. Charts are Blade components in resources/views/components/charts (columns, line, bars) that draw SVG/HTML with inline styles and --chart-* CSS variables defined in the page view. They don't depend on a Tailwind rebuild and use no JS chart library (user's choice). The palette was validated for colour blindness against the white/neutral-900 cards. Each chart has a "Show table" view. Adoption applications are pruned after 6 months, so don't build historical funnels from them.

## Report figures live in BuildsShelterReport, shared by screen and print
App\Livewire\Reports\Concerns\BuildsShelterReport holds the #[Url] period/from/to properties, dateRange() and report(). It is used by ShelterReports (reports.index) and ShelterReportPrint (reports.print, #[Layout('layouts.print')], manager-only). The printer button passes the period on screen as query params, so both pages always show the same numbers: add new figures to the trait, never to one page. The chart colour variables and print rules (svg min-width reset, details hidden, .report-section break-inside: avoid, print-color-adjust: exact) live in <x-charts.styles />, which goes inside a .shelter-report root. In print, the charts' "Show table" details are hidden, so the print view renders its own full tables. A year period gets the title "Activity report :year" (the association's relatório de atividades), which becomes the PDF file name. There's no PDF library: window.print() is enough (see the layouts rule).
