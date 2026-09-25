@props([
    'items' => [],
    'color' => 'var(--chart-series-1)',
    'unit' => null,
    'empty' => null,
])

{{--
    Horizontal bar list, one series, value at the bar tip.
    items: list of ['label' => string, 'value' => int]
    unit: optional suffix shown after each value (e.g. "days")
--}}

@php
    $maxValue = max(1, ...array_column($items, 'value') ?: [0]);
@endphp

<div {{ $attributes->class('flex flex-col gap-3') }}>
    @forelse ($items as $item)
        <div class="flex flex-col gap-1 text-sm" title="{{ $item['label'] }}: {{ $item['value'] }}{{ $unit ? ' '.$unit : '' }}">
            <span class="truncate text-neutral-600 dark:text-neutral-300">{{ $item['label'] }}</span>
            <div class="flex items-center gap-2">
                <div class="shrink-0" style="height: 14px; width: {{ max(0.5, $item['value'] / $maxValue * 85) }}%; background: {{ $color }}; border-radius: 0 4px 4px 0;"></div>
                <span class="shrink-0 text-xs font-medium" style="color: var(--chart-ink); font-variant-numeric: tabular-nums">{{ $item['value'] }}{{ $unit ? ' '.$unit : '' }}</span>
            </div>
        </div>
    @empty
        <p class="text-center text-sm text-neutral-500 dark:text-neutral-400">{{ $empty ?? __('No data for this period') }}</p>
    @endforelse
</div>
