{{-- Health section: vaccinations, diagnoses and sterilisation. --}}
@php
    $health = $this->healthReport;
    $totals = $health['totals'];
    $groupedByYear = $this->isGroupedByYear();
@endphp

<div class="report-sections flex flex-col gap-6">
    <div class="report-section grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ([
            ['label' => __('Vaccinations given'), 'value' => $totals['vaccinations'], 'href' => null],
            ['label' => __('Overdue Vaccinations'), 'value' => $totals['overdueVaccinations'], 'href' => route('pets.vaccinations.index', ['nextDueFilter' => 'overdue'])],
            ['label' => __('Diagnoses'), 'value' => $totals['diagnoses'], 'href' => null],
            ['label' => __('Open cases (active or chronic)'), 'value' => $totals['openCases'], 'href' => null],
            ['label' => __('Sterilised animals in the shelter'), 'value' => $totals['neuteredRate'] === null ? '—' : $totals['neuteredRate'].'%', 'href' => null],
        ] as $tile)
            @if ($tile['href'])
                <a wire:key="health-tile-{{ $loop->index }}" href="{{ $tile['href'] }}" wire:navigate class="report-section flex flex-col gap-2 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm hover:bg-neutral-50 dark:border-neutral-700 dark:bg-neutral-900 dark:hover:bg-neutral-800">
                    <span class="text-sm font-medium text-neutral-500 dark:text-neutral-400">{{ $tile['label'] }}</span>
                    <span class="text-3xl font-semibold text-neutral-900 dark:text-white">{{ $tile['value'] }}</span>
                </a>
            @else
                <div wire:key="health-tile-{{ $loop->index }}" class="report-section flex flex-col gap-2 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
                    <span class="text-sm font-medium text-neutral-500 dark:text-neutral-400">{{ $tile['label'] }}</span>
                    <span class="text-3xl font-semibold text-neutral-900 dark:text-white">{{ $tile['value'] }}</span>
                </div>
            @endif
        @endforeach
    </div>

    <div class="report-section flex flex-col gap-4 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
        <div class="flex flex-col gap-1">
            <h2 class="text-base font-semibold text-neutral-900 dark:text-white">{{ __('Vaccinations given') }}</h2>
            <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ $groupedByYear ? __('Per year') : __('Per month') }}</p>
        </div>
        <x-charts.columns
            :title="__('Vaccinations given')"
            :labels="$this->periodLabels"
            :series="[['name' => __('Vaccinations given'), 'color' => 'var(--chart-series-1)', 'values' => $health['vaccinationBuckets']]]"
        />
    </div>

    <div class="report-sections grid gap-6 lg:grid-cols-2">
        <div class="report-section flex flex-col gap-4 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <h2 class="text-base font-semibold text-neutral-900 dark:text-white">{{ __('Vaccinations by vaccine') }}</h2>
            <x-charts.bars :items="$health['vaccinationsByVaccine']" />
        </div>

        <div class="report-section flex flex-col gap-4 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <h2 class="text-base font-semibold text-neutral-900 dark:text-white">{{ __('Diagnoses by sickness') }}</h2>
            <x-charts.bars :items="$health['diagnosesBySickness']" />
        </div>
    </div>
</div>
