{{-- Colour tokens and hover states for the chart components; place inside an element with the shelter-report class. --}}
<style>
    .shelter-report {
        --chart-series-1: #2a78d6;
        --chart-series-2: #eb6834;
        --chart-series-3: #1baf7a;
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
        .shelter-report svg { min-width: 0 !important; }
        .shelter-report details { display: none; }
        .shelter-report .report-section { break-inside: avoid; }
    }
</style>
