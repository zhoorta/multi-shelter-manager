{{-- Occupancy section: capacity is only a guideline, so occupancy is shown as a neutral percentage. --}}
@php
    $occupancy = $this->occupancyReport;
    $totals = $occupancy['totals'];
    $groupedByYear = $this->isGroupedByYear();
@endphp

<div class="report-sections flex flex-col gap-6">
    <div class="report-section grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ([
            ['label' => __('Occupancy today'), 'value' => $totals['rate'] === null ? '—' : $totals['rate'].'%'],
            ['label' => __('Total capacity'), 'value' => $totals['capacity']],
            ['label' => __('Pets in cages'), 'value' => $totals['housed']],
            ['label' => __('Pets with Unknown Location'), 'value' => $totals['withoutCage']],
            ['label' => __('Pets in foster families'), 'value' => $totals['inFosterFamilies']],
        ] as $tile)
            <div wire:key="occupancy-tile-{{ $loop->index }}" class="report-section flex flex-col gap-2 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
                <span class="text-sm font-medium text-neutral-500 dark:text-neutral-400">{{ $tile['label'] }}</span>
                <span class="text-3xl font-semibold text-neutral-900 dark:text-white">{{ $tile['value'] }}</span>
            </div>
        @endforeach
    </div>

    <div class="report-section flex flex-col gap-4 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
        <div class="flex flex-col gap-1">
            <h2 class="text-base font-semibold text-neutral-900 dark:text-white">{{ __('Occupancy over time') }}</h2>
            <p class="text-sm text-neutral-500 dark:text-neutral-400">
                {{ ($groupedByYear ? __('At the end of each year') : __('At the end of each month')).' · '.__('Pets in shelter compared with today\'s capacity') }}
            </p>
        </div>
        <x-charts.line :name="__('Occupancy')" :labels="$this->periodLabels" :values="$occupancy['rateBuckets']" unit="%" />
    </div>

    <div class="report-section flex flex-col gap-4 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
        <div class="flex flex-col gap-1">
            <h2 class="text-base font-semibold text-neutral-900 dark:text-white">{{ __('Occupancy by wing') }}</h2>
            <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Pets in cages compared with the capacity of each wing, today') }}</p>
        </div>
        <x-charts.bars :items="$occupancy['wings']" :empty="__('No facilities defined')" />
    </div>
</div>
