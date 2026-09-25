@props([
    'labels' => [],
    'series' => [],
    'title' => '',
    'money' => false,
])

{{--
    Grouped column chart drawn as SVG.
    labels: list of ['label' => string, 'fullLabel' => ?string, 'sublabel' => ?string]
    series: list of ['name' => string, 'color' => css color, 'values' => list<int|float>]
    money: format values as euros in tooltips and the table
--}}

@php
    $width = 880;
    $height = 240;
    $paddingLeft = 40;
    $paddingRight = 8;
    $paddingTop = 12;
    $paddingBottom = 40;
    $plotWidth = $width - $paddingLeft - $paddingRight;
    $plotHeight = $height - $paddingTop - $paddingBottom;
    $baseline = $paddingTop + $plotHeight;

    $maxValue = max(1, ...array_map(fn (array $serie): float => (float) max([0, ...$serie['values']]), $series ?: [['values' => [0]]]));
    $formatValue = fn (int|float $value): string => $money ? number_format((float) $value, 2, ',', '.').' €' : (string) $value;
    $magnitude = 10 ** floor(log10($maxValue / 4));
    $tickStep = collect([1, 2, 5, 10])->map(fn (int $multiplier): float => $multiplier * $magnitude)->first(fn (float $step): bool => $step * 4 >= $maxValue);
    $tickStep = max(1, (int) $tickStep);
    $topValue = (int) (ceil($maxValue / $tickStep) * $tickStep);
    $ticks = range(0, $topValue, $tickStep);

    $bucketCount = max(1, count($labels));
    $groupWidth = $plotWidth / $bucketCount;
    $seriesCount = max(1, count($series));
    $barGap = 2;
    $barWidth = max(1, min(24, ($groupWidth * 0.8 - ($seriesCount - 1) * $barGap) / $seriesCount));
    $groupInnerWidth = $seriesCount * $barWidth + ($seriesCount - 1) * $barGap;
    $labelEvery = $groupWidth >= 30 ? 1 : (int) ceil(30 / $groupWidth);

    $toY = fn (int|float $value): float => $baseline - ($value / $topValue) * $plotHeight;
    $barPath = function (float $x, float $y, float $barWidth, float $barHeight): string {
        $radius = min(4, $barWidth / 2, $barHeight);

        return sprintf(
            'M%1$.1f,%2$.1f L%1$.1f,%3$.1f Q%1$.1f,%4$.1f %5$.1f,%4$.1f L%6$.1f,%4$.1f Q%7$.1f,%4$.1f %7$.1f,%3$.1f L%7$.1f,%2$.1f Z',
            $x, $y + $barHeight, $y + $radius, $y, $x + $radius, $x + $barWidth - $radius, $x + $barWidth,
        );
    };
@endphp

<div {{ $attributes->class('flex flex-col gap-3') }}>
    <div class="flex flex-wrap gap-x-4 gap-y-1 text-xs text-neutral-600 dark:text-neutral-300">
        @foreach ($series as $serie)
            <span class="inline-flex items-center gap-1.5">
                <span class="inline-block rounded-sm" style="width: 10px; height: 10px; background: {{ $serie['color'] }}"></span>
                {{ $serie['name'] }}
            </span>
        @endforeach
    </div>

    <div class="overflow-x-auto">
        <svg viewBox="0 0 {{ $width }} {{ $height }}" role="img" aria-label="{{ $title }}" style="width: 100%; min-width: 720px; height: auto;">
            @foreach ($ticks as $tick)
                <line x1="{{ $paddingLeft }}" x2="{{ $width - $paddingRight }}" y1="{{ $toY($tick) }}" y2="{{ $toY($tick) }}" stroke="{{ $tick === 0 ? 'var(--chart-axis)' : 'var(--chart-grid)' }}" stroke-width="1" />
                <text x="{{ $paddingLeft - 8 }}" y="{{ $toY($tick) + 4 }}" text-anchor="end" font-size="12" fill="var(--chart-muted)" style="font-variant-numeric: tabular-nums">{{ number_format($tick, 0, ',', '.') }}</text>
            @endforeach

            @foreach ($labels as $index => $label)
                @php
                    $groupX = $paddingLeft + $index * $groupWidth;
                    $firstBarX = $groupX + ($groupWidth - $groupInnerWidth) / 2;
                @endphp
                <g class="chart-group">
                    <title>{{ $label['fullLabel'] ?? $label['label'] }}&#10;@foreach ($series as $serie){{ $serie['name'] }}: {{ $formatValue($serie['values'][$index] ?? 0) }}&#10;@endforeach</title>
                    <rect class="chart-hover" x="{{ $groupX }}" y="{{ $paddingTop }}" width="{{ $groupWidth }}" height="{{ $plotHeight }}" rx="4" />
                    @foreach ($series as $serieIndex => $serie)
                        @php
                            $value = $serie['values'][$index] ?? 0;
                        @endphp
                        @if ($value > 0)
                            <path d="{{ $barPath($firstBarX + $serieIndex * ($barWidth + $barGap), $toY($value), $barWidth, $baseline - $toY($value)) }}" fill="{{ $serie['color'] }}" />
                        @endif
                    @endforeach
                    @if ($index % $labelEvery === 0)
                        <text x="{{ $groupX + $groupWidth / 2 }}" y="{{ $baseline + 16 }}" text-anchor="middle" font-size="12" fill="var(--chart-muted)">{{ $label['label'] }}</text>
                    @endif
                    @if ($label['sublabel'] ?? null)
                        <text x="{{ $groupX + $groupWidth / 2 }}" y="{{ $baseline + 31 }}" text-anchor="middle" font-size="12" font-weight="600" fill="var(--chart-muted)">{{ $label['sublabel'] }}</text>
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
                        @foreach ($series as $serie)
                            <th scope="col" class="py-1 pe-4 text-right font-medium">{{ $serie['name'] }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200 text-neutral-700 dark:divide-neutral-700 dark:text-neutral-300" style="font-variant-numeric: tabular-nums">
                    @foreach ($labels as $index => $label)
                        <tr>
                            <td class="py-1 pe-4">{{ $label['fullLabel'] ?? $label['label'] }}</td>
                            @foreach ($series as $serie)
                                <td class="py-1 pe-4 text-right">{{ $formatValue($serie['values'][$index] ?? 0) }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </details>
</div>
