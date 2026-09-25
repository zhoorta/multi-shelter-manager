---
paths:
  - resources/views/components/charts/styles.blade.php
---

# Charts

## Reports print white even from the dark-mode screen page
.shelter-report uses print-color-adjust: exact so chart colours print, but that also printed the dark-mode card backgrounds (black boxes) when someone pressed Cmd+P on the Reports page. The @media print block in <x-charts.styles /> swaps in the light chart tokens under .dark and forces [class*="dark:bg-"] to white, dark:border/divide to #e5e5e5 and dark:text to dark grey. The page controls (period, tabs, print button) are print:hidden. Give every report card the report-section class so it isn't split across pages.

## Keeping report boxes whole on paper needs block layout
break-inside: avoid is ignored for boxes inside flex/grid containers in Safari and Firefox (Chrome honours it), so report cards were cut across pages. In print, every container with the report-sections class (the screen page root, each partial root and the card grids) becomes display: block with a 1.5rem gap. Cards carry report-section (break-inside plus the legacy page-break-inside), and the tile grids also carry report-section and go 3 per row so the first chart fits on page 1. The Finances page breaks are inline style="break-after: page" (plus page-break-after) on the Income and Member payments by method cards. New sections: wrap them in report-sections and give each card report-section.
