{{-- Colour tokens and hover states for the chart components; place inside an element with the shelter-report class. --}}
<style>
    .shelter-report {
        --chart-series-1: #2a78d6;
        --chart-series-2: #eb6834;
        --chart-series-3: #1baf7a;
        --chart-series-4: #eda100;
        --chart-grid: #e5e5e5;
        --chart-axis: #d4d4d4;
        --chart-muted: #737373;
        --chart-ink: #171717;
        --chart-surface: #ffffff;
    }

    .dark .shelter-report {
        --chart-series-1: #3987e5;
        --chart-series-2: #d95926;
        --chart-series-3: #199e70;
        --chart-series-4: #c98500;
        --chart-grid: #2c2c2a;
        --chart-axis: #404040;
        --chart-muted: #a3a3a3;
        --chart-ink: #ffffff;
        --chart-surface: #171717;
    }

    .shelter-report {
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    .shelter-report .chart-hover { fill: transparent; }
    .shelter-report .chart-group:hover .chart-hover { fill: var(--chart-grid); fill-opacity: 0.5; }
    .shelter-report .chart-hover-dot { opacity: 0; }
    .shelter-report .chart-group:hover .chart-hover-dot { opacity: 1; }

    @media print {
        /* Printing the on-screen page in dark mode: use the light chart colours and white boxes to save ink. */
        .dark .shelter-report {
            --chart-series-1: #2a78d6;
            --chart-series-2: #eb6834;
            --chart-series-3: #1baf7a;
            --chart-series-4: #eda100;
            --chart-grid: #e5e5e5;
            --chart-axis: #d4d4d4;
            --chart-muted: #737373;
            --chart-ink: #171717;
            --chart-surface: #ffffff;
            color: #171717;
        }
        .dark .shelter-report [class*="dark:bg-"] { background-color: #ffffff !important; }
        .dark .shelter-report [class*="dark:border-"],
        .dark .shelter-report [class*="dark:divide-"] > * { border-color: #e5e5e5 !important; }
        .dark .shelter-report [class*="dark:text-"] { color: #525252 !important; }
        .dark .shelter-report [class*="dark:text-white"],
        .dark .shelter-report [data-flux-heading] { color: #171717 !important; }

        .shelter-report svg { min-width: 0 !important; }
        .shelter-report details { display: none; }

        /* Browsers only keep boxes whole (break-inside: avoid) reliably in block layout, not inside flex or grid containers. */
        .shelter-report.report-sections,
        .shelter-report .report-sections { display: block !important; }
        .shelter-report.report-sections > * + *,
        .shelter-report .report-sections > * + * { margin-top: 1.5rem; }
        .shelter-report .report-section {
            break-inside: avoid;
            page-break-inside: avoid;
        }
        .shelter-report [style*="break-after: page"] { page-break-after: always; }
        /* Summary tiles three per row on paper, so the first chart still fits on the first page. */
        .shelter-report .report-section.grid { grid-template-columns: repeat(auto-fill, minmax(13rem, 1fr)) !important; }
    }
</style>
