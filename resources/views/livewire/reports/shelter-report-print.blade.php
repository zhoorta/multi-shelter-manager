<div class="shelter-report mx-auto max-w-5xl p-10 print:p-0">
    <x-charts.styles />

    <div class="mb-6 flex justify-end print:hidden">
        <button
            type="button"
            onclick="window.print()"
            class="inline-flex items-center gap-2 rounded-md bg-neutral-900 px-4 py-2 text-sm font-medium text-white hover:bg-neutral-700"
        >
            {{ __('Print') }}
        </button>
    </div>

    <div class="flex items-start justify-between gap-6 border-b border-neutral-300 pb-6">
        <div class="flex flex-col gap-1">
            <h1 class="text-2xl font-semibold">{{ $this->reportTitle() }}</h1>
            <p class="text-sm text-neutral-600">{{ $this->dateRange()[0]->format('d/m/Y') }} – {{ $this->dateRange()[1]->format('d/m/Y') }}</p>
            <div class="mt-3 text-sm leading-relaxed text-neutral-800">
                <p class="font-semibold">{{ $shelter->name }}</p>
                @if ($shelter->address)
                    <p>{{ $shelter->address }}</p>
                @endif
                <p>{{ trim($shelter->postal_code.' '.$shelter->city) }}</p>
                @foreach (array_filter([$shelter->phone, $shelter->email, $shelter->website]) as $contact)
                    <p>{{ $contact }}</p>
                @endforeach
            </div>
        </div>

        @if ($shelter->logo_path)
            <img
                src="{{ \Illuminate\Support\Facades\Storage::url($shelter->logo_path) }}"
                alt="{{ $shelter->name }}"
                class="h-20 w-20 shrink-0 object-contain"
            >
        @endif
    </div>

    @if ($this->activeTab() === 'animals')
        @php
            $report = $this->report;
            $totals = $report['totals'];
            $periodLabels = collect($report['buckets'])->map(fn (array $bucket): array => [
                'label' => $bucket['label'],
                'fullLabel' => $bucket['fullLabel'],
                'sublabel' => $bucket['sublabel'],
            ])->all();
            $adoptionsBySpecies = collect($report['adoptionsBySpecies'])->pluck('value', 'label');
            $speciesRows = collect($report['intakesBySpecies'])->pluck('value', 'label')
                ->union($adoptionsBySpecies->map(fn (): int => 0))
                ->map(fn (int $intakes, string $speciesName): array => [
                    'label' => $speciesName,
                    'intakes' => $intakes,
                    'adoptions' => $adoptionsBySpecies[$speciesName] ?? 0,
                ])
                ->values();
            $medianDaysByAge = collect($report['medianDaysByAge'])->pluck('value', 'label');
        @endphp

        <div class="report-section mt-8 grid grid-cols-3 gap-4">
            @foreach ([
                ['label' => __('Intakes'), 'value' => $totals['intakes']],
                ['label' => __('Adoptions'), 'value' => $totals['adoptions']],
                ['label' => __('Returns'), 'value' => $totals['returns']],
                ['label' => __('Deaths'), 'value' => $totals['deaths']],
                ['label' => __('Median days until adoption'), 'value' => $totals['medianDaysToAdoption'] ?? '—'],
                ['label' => __('Pets in shelter at the end of the period'), 'value' => $totals['population']],
            ] as $tile)
                <div class="flex flex-col gap-1 rounded-lg border border-neutral-300 p-4">
                    <span class="text-xs font-medium text-neutral-600">{{ $tile['label'] }}</span>
                    <span class="text-2xl font-semibold">{{ $tile['value'] }}</span>
                </div>
            @endforeach
        </div>

        <div class="report-section mt-8 flex flex-col gap-3">
            <h2 class="text-base font-semibold">{{ __('Intakes and exits') }}</h2>
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

        <div class="report-section mt-8 flex flex-col gap-3">
            <h2 class="text-base font-semibold">{{ __('Pets in shelter') }}</h2>
            <x-charts.line
                :name="__('Pets in shelter')"
                :labels="$periodLabels"
                :values="array_column($report['buckets'], 'population')"
            />
        </div>

        <div class="report-section mt-8 flex flex-col gap-3">
            <h2 class="text-base font-semibold">{{ $report['groupedByYear'] ? __('Per year') : __('Per month') }}</h2>
            <table class="w-full text-left text-sm" style="font-variant-numeric: tabular-nums">
                <thead class="border-b border-neutral-300 text-xs uppercase text-neutral-500">
                    <tr>
                        <th scope="col" class="py-2 pr-4 font-medium">{{ __('Period') }}</th>
                        <th scope="col" class="py-2 pr-4 text-right font-medium">{{ __('Intakes') }}</th>
                        <th scope="col" class="py-2 pr-4 text-right font-medium">{{ __('Adoptions') }}</th>
                        <th scope="col" class="py-2 pr-4 text-right font-medium">{{ __('Deaths') }}</th>
                        <th scope="col" class="py-2 text-right font-medium">{{ __('Pets in shelter') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200">
                    @foreach ($report['buckets'] as $bucket)
                        <tr>
                            <td class="py-1.5 pr-4">{{ $bucket['fullLabel'] }}</td>
                            <td class="py-1.5 pr-4 text-right">{{ $bucket['intakes'] }}</td>
                            <td class="py-1.5 pr-4 text-right">{{ $bucket['adoptions'] }}</td>
                            <td class="py-1.5 pr-4 text-right">{{ $bucket['deaths'] }}</td>
                            <td class="py-1.5 text-right">{{ $bucket['population'] ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="border-t border-neutral-300 font-semibold">
                    <tr>
                        <td class="py-2 pr-4">{{ __('Total') }}</td>
                        <td class="py-2 pr-4 text-right">{{ $totals['intakes'] }}</td>
                        <td class="py-2 pr-4 text-right">{{ $totals['adoptions'] }}</td>
                        <td class="py-2 pr-4 text-right">{{ $totals['deaths'] }}</td>
                        <td class="py-2 text-right">{{ $totals['population'] }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="report-section mt-8 grid grid-cols-2 gap-8">
            <div class="flex flex-col gap-3">
                <h2 class="text-base font-semibold">{{ __('By species') }}</h2>
                <table class="w-full text-left text-sm" style="font-variant-numeric: tabular-nums">
                    <thead class="border-b border-neutral-300 text-xs uppercase text-neutral-500">
                        <tr>
                            <th scope="col" class="py-2 pr-4 font-medium">{{ __('Species') }}</th>
                            <th scope="col" class="py-2 pr-4 text-right font-medium">{{ __('Intakes') }}</th>
                            <th scope="col" class="py-2 text-right font-medium">{{ __('Adoptions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-200">
                        @forelse ($speciesRows as $row)
                            <tr>
                                <td class="py-1.5 pr-4">{{ $row['label'] }}</td>
                                <td class="py-1.5 pr-4 text-right">{{ $row['intakes'] }}</td>
                                <td class="py-1.5 text-right">{{ $row['adoptions'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-2 text-center text-neutral-500">{{ __('No data for this period') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="flex flex-col gap-3">
                <h2 class="text-base font-semibold">{{ __('Adoptions by age at adoption') }}</h2>
                <table class="w-full text-left text-sm" style="font-variant-numeric: tabular-nums">
                    <thead class="border-b border-neutral-300 text-xs uppercase text-neutral-500">
                        <tr>
                            <th scope="col" class="py-2 pr-4 font-medium">{{ __('Age') }}</th>
                            <th scope="col" class="py-2 pr-4 text-right font-medium">{{ __('Adoptions') }}</th>
                            <th scope="col" class="py-2 text-right font-medium">{{ __('Median days until adoption') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-200">
                        @forelse ($report['adoptionsByAge'] as $row)
                            <tr>
                                <td class="py-1.5 pr-4">{{ $row['label'] }}</td>
                                <td class="py-1.5 pr-4 text-right">{{ $row['value'] }}</td>
                                <td class="py-1.5 text-right">{{ $medianDaysByAge[$row['label']] ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-2 text-center text-neutral-500">{{ __('No data for this period') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="mt-8">
            @include('livewire.reports.partials.'.$this->activeTab())
        </div>
    @endif

    <p class="mt-10 border-t border-neutral-300 pt-4 text-xs text-neutral-500">
        {{ __('Generated on :date by :app', ['date' => now()->format('d/m/Y'), 'app' => config('app.name')]) }}
    </p>
</div>
