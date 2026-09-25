<div class="shelter-report flex h-full w-full flex-1 flex-col gap-6">
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

        .shelter-report .chart-hover { fill: transparent; }
        .shelter-report .chart-group:hover .chart-hover { fill: var(--chart-grid); fill-opacity: 0.5; }
        .shelter-report .chart-hover-dot { opacity: 0; }
        .shelter-report .chart-group:hover .chart-hover-dot { opacity: 1; }
    </style>

    @php
        $report = $this->report;
        $totals = $report['totals'];
        $periodLabels = collect($report['buckets'])->map(fn (array $bucket): array => [
            'label' => $bucket['label'],
            'fullLabel' => $bucket['fullLabel'],
            'sublabel' => $bucket['sublabel'],
        ])->all();
    @endphp

    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div class="flex flex-col gap-1">
            <flux:heading size="xl">{{ __('Reports') }}</flux:heading>
            <flux:text>{{ $report['start']->format('d/m/Y') }} – {{ $report['end']->format('d/m/Y') }}</flux:text>
        </div>

        <div class="flex flex-col gap-3 sm:flex-row sm:items-end">
            <flux:select wire:model.live="period" :label="__('Period')" class="sm:w-48">
                <flux:select.option value="last_12_months">{{ __('Last 12 months') }}</flux:select.option>
                @foreach ($this->availableYears as $year)
                    <flux:select.option value="{{ $year }}">{{ $year }}</flux:select.option>
                @endforeach
                <flux:select.option value="all">{{ __('All time') }}</flux:select.option>
                <flux:select.option value="custom">{{ __('Custom') }}</flux:select.option>
            </flux:select>

            @if ($period === 'custom')
                <flux:input type="date" wire:model.live.blur="from" :label="__('From')" />
                <flux:input type="date" wire:model.live.blur="to" :label="__('To')" />
            @endif
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ([
            ['label' => __('Intakes'), 'value' => $totals['intakes']],
            ['label' => __('Adoptions'), 'value' => $totals['adoptions']],
            ['label' => __('Returns'), 'value' => $totals['returns']],
            ['label' => __('Deaths'), 'value' => $totals['deaths']],
            ['label' => __('Median days until adoption'), 'value' => $totals['medianDaysToAdoption'] ?? '—'],
            ['label' => __('Pets in shelter at the end of the period'), 'value' => $totals['population']],
        ] as $tile)
            <div wire:key="report-tile-{{ $loop->index }}" class="flex flex-col gap-2 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
                <span class="text-sm font-medium text-neutral-500 dark:text-neutral-400">{{ $tile['label'] }}</span>
                <span class="text-3xl font-semibold text-neutral-900 dark:text-white">{{ $tile['value'] }}</span>
            </div>
        @endforeach
    </div>

    <div class="flex flex-col gap-4 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
        <div class="flex flex-col gap-1">
            <h2 class="text-base font-semibold text-neutral-900 dark:text-white">{{ __('Intakes and exits') }}</h2>
            <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ $report['groupedByYear'] ? __('Per year') : __('Per month') }}</p>
        </div>
        <x-charts.columns
            :title="__('Intakes and exits')"
            :labels="$periodLabels"
            :series="[
                ['name' => __('Intakes'), 'color' => 'var(--chart-series-1)', 'values' => array_column($report['buckets'], 'intakes')],
                ['name' => __('Adoptions'), 'color' => 'var(--chart-series-2)', 'values' => array_column($report['buckets'], 'adoptions')],
                ['name' => __('Deaths'), 'color' => 'var(--chart-series-3)', 'values' => array_column($report['buckets'], 'deaths')],
            ]"
        />
    </div>

    <div class="flex flex-col gap-4 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
        <div class="flex flex-col gap-1">
            <h2 class="text-base font-semibold text-neutral-900 dark:text-white">{{ __('Pets in shelter') }}</h2>
            <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ $report['groupedByYear'] ? __('At the end of each year') : __('At the end of each month') }}</p>
        </div>
        <x-charts.line
            :name="__('Pets in shelter')"
            :labels="$periodLabels"
            :values="array_column($report['buckets'], 'population')"
        />
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="flex flex-col gap-4 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <h2 class="text-base font-semibold text-neutral-900 dark:text-white">{{ __('Adoptions by species') }}</h2>
            <x-charts.bars :items="$report['adoptionsBySpecies']" />
        </div>

        <div class="flex flex-col gap-4 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <h2 class="text-base font-semibold text-neutral-900 dark:text-white">{{ __('Adoptions by age at adoption') }}</h2>
            <x-charts.bars :items="$report['adoptionsByAge']" />
        </div>
    </div>

    <div class="flex flex-col gap-4 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
        <div class="flex flex-col gap-1">
            <h2 class="text-base font-semibold text-neutral-900 dark:text-white">{{ __('Median days until adoption by age') }}</h2>
            <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Days between intake and adoption, for the adoptions in the period') }}</p>
        </div>
        <x-charts.bars :items="$report['medianDaysByAge']" :unit="__('days')" />
    </div>

    <div class="overflow-hidden rounded-xl border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-neutral-900">
        <div class="flex flex-col gap-1 border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
            <h2 class="text-base font-semibold text-neutral-900 dark:text-white">{{ __('Waiting longest for adoption') }}</h2>
            <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Available pets that have been in the shelter the longest') }}</p>
        </div>

        @if ($report['longestWaiting']->isEmpty())
            <p class="p-6 text-center text-sm text-neutral-500 dark:text-neutral-400">{{ __('No pets available for adoption') }}</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-neutral-50 text-xs uppercase text-neutral-500 dark:bg-neutral-800 dark:text-neutral-400">
                        <tr>
                            <th scope="col" class="px-6 py-3 font-medium">{{ __('Pet') }}</th>
                            <th scope="col" class="px-6 py-3 font-medium">{{ __('Age') }}</th>
                            <th scope="col" class="px-6 py-3 font-medium">{{ __('Checkin Date') }}</th>
                            <th scope="col" class="px-6 py-3 font-medium">{{ __('In captivity') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                        @foreach ($report['longestWaiting'] as $pet)
                            <tr wire:key="longest-waiting-{{ $pet->id }}">
                                <td class="px-6 py-3">
                                    <a href="{{ route('pets.show', $pet) }}" wire:navigate class="font-medium text-neutral-900 hover:underline dark:text-white">{{ $pet->name }}</a>
                                    <span class="block text-xs text-neutral-500 dark:text-neutral-400">{{ $pet->species?->name }} - {{ $pet->ref }}</span>
                                </td>
                                <td class="px-6 py-3 text-neutral-600 dark:text-neutral-300">{{ $pet->age_in_words ?? '—' }}</td>
                                <td class="px-6 py-3 text-neutral-600 dark:text-neutral-300">{{ $pet->checkin_date->format('d/m/Y') }}</td>
                                <td class="px-6 py-3 text-neutral-600 dark:text-neutral-300">{{ $pet->time_in_captivity }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
