@props([
    'labels' => [],
    'values' => [],
    'name' => '',
    'color' => 'var(--chart-series-1)',
    'unit' => '',
])

{{--
    Single-series line chart with an area wash, drawn as SVG.
    labels: list of ['label' => string, 'fullLabel' => ?string, 'sublabel' => ?string]
    values: list<?int> (null leaves the point out, e.g. months still to come)
--}}

@php
    $width = 880;
    $height = 220;
    $paddingLeft = 40;
    $paddingRight = 40;
    $paddingTop = 16;
    $paddingBottom = 40;
    $plotWidth = $width - $paddingLeft - $paddingRight;
    $plotHeight = $height - $paddingTop - $paddingBottom;
    $baseline = $paddingTop + $plotHeight;

    $maxValue = max(1, ...array_map('intval', array_filter($values, fn (?int $value): bool => $value !== null) ?: [0]));
    $magnitude = 10 ** floor(log10($maxValue / 4));
    $tickStep = collect([1, 2, 5, 10])->map(fn (int $multiplier): float => $multiplier * $magnitude)->first(fn (float $step): bool => $step * 4 >= $maxValue);
    $tickStep = max(1, (int) $tickStep);
    $topValue = (int) (ceil($maxValue / $tickStep) * $tickStep);
    $ticks = range(0, $topValue, $tickStep);

    $bucketCount = max(1, count($labels));
    $groupWidth = $plotWidth / $bucketCount;
    $labelEvery = $groupWidth >= 30 ? 1 : (int) ceil(30 / $groupWidth);

    $toX = fn (int $index): float => $paddingLeft + ($index + 0.5) * $groupWidth;
    $toY = fn (int $value): float => $baseline - ($value / $topValue) * $plotHeight;

    $points = collect($values)
        ->map(fn (?int $value, int $index): ?array => $value === null ? null : ['x' => $toX($index), 'y' => $toY($value), 'value' => $value])
        ->filter()
        ->values();

    $linePath = $points->map(fn (array $point, int $index): string => ($index === 0 ? 'M' : 'L').round($point['x'], 1).','.round($point['y'], 1))->implode(' ');
    $areaPath = $points->isEmpty()
        ? ''
        : $linePath.' L'.round($points->last()['x'], 1).','.$baseline.' L'.round($points->first()['x'], 1).','.$baseline.' Z';
    $lastPoint = $points->last();
@endphp

<div {{ $attributes->class('flex flex-col gap-3') }}>
    <div class="overflow-x-auto">
        <svg viewBox="0 0 {{ $width }} {{ $height }}" role="img" aria-label="{{ $name }}" style="width: 100%; min-width: 720px; height: auto;">
            @foreach ($ticks as $tick)
                <line x1="{{ $paddingLeft }}" x2="{{ $width - $paddingRight }}" y1="{{ $toY($tick) }}" y2="{{ $toY($tick) }}" stroke="{{ $tick === 0 ? 'var(--chart-axis)' : 'var(--chart-grid)' }}" stroke-width="1" />
                <text x="{{ $paddingLeft - 8 }}" y="{{ $toY($tick) + 4 }}" text-anchor="end" font-size="12" fill="var(--chart-muted)" style="font-variant-numeric: tabular-nums">{{ number_format($tick, 0, ',', '.') }}{{ $unit }}</text>
            @endforeach

            @if ($points->isNotEmpty())
                <path d="{{ $areaPath }}" fill="{{ $color }}" fill-opacity="0.1" />
                <path d="{{ $linePath }}" fill="none" stroke="{{ $color }}" stroke-width="2" stroke-linejoin="round" stroke-linecap="round" />
                <circle cx="{{ $lastPoint['x'] }}" cy="{{ $lastPoint['y'] }}" r="4" fill="{{ $color }}" stroke="var(--chart-surface)" stroke-width="2" />
                <text x="{{ $lastPoint['x'] + 8 }}" y="{{ $lastPoint['y'] + 4 }}" font-size="12" font-weight="600" fill="var(--chart-ink)">{{ $lastPoint['value'] }}{{ $unit }}</text>
            @endif

            @foreach ($labels as $index => $label)
                <g class="chart-group">
                    <title>{{ $label['fullLabel'] ?? $label['label'] }}&#10;{{ $name }}: {{ ($values[$index] ?? null) === null ? '—' : $values[$index].$unit }}</title>
                    <rect class="chart-hover" x="{{ $paddingLeft + $index * $groupWidth }}" y="{{ $paddingTop }}" width="{{ $groupWidth }}" height="{{ $plotHeight }}" rx="4" />
                    @if (($values[$index] ?? null) !== null)
                        <circle class="chart-hover-dot" cx="{{ $toX($index) }}" cy="{{ $toY($values[$index]) }}" r="4" fill="{{ $color }}" stroke="var(--chart-surface)" stroke-width="2" />
                    @endif
                    @if ($index % $labelEvery === 0)
                        <text x="{{ $toX($index) }}" y="{{ $baseline + 16 }}" text-anchor="middle" font-size="12" fill="var(--chart-muted)">{{ $label['label'] }}</text>
                    @endif
                    @if ($label['sublabel'] ?? null)
                        <text x="{{ $toX($index) }}" y="{{ $baseline + 31 }}" text-anchor="middle" font-size="12" font-weight="600" fill="var(--chart-muted)">{{ $label['sublabel'] }}</text>
                    @endif
                </g>
            @endforeach
        </svg>
    </div>

    <details class="text-sm">
        <summary class="cursor-pointer text-xs text-neutral-500 dark:text-neutral-400">{{ __('Show table') }}</summary>
        <div class="mt-2 overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="text-neutral-500 dark:text-neutral-400">
                    <tr>
                        <th scope="col" class="py-1 pe-4 font-medium">{{ __('Period') }}</th>
                        <th scope="col" class="py-1 pe-4 text-right font-medium">{{ $name }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200 text-neutral-700 dark:divide-neutral-700 dark:text-neutral-300" style="font-variant-numeric: tabular-nums">
                    @foreach ($labels as $index => $label)
                        @if (($values[$index] ?? null) !== null)
                            <tr>
                                <td class="py-1 pe-4">{{ $label['fullLabel'] ?? $label['label'] }}</td>
                                <td class="py-1 pe-4 text-right">{{ $values[$index] }}{{ $unit }}</td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </details>
</div>
