{{-- Finances section: income by payment date, sponsorships and members. --}}
@php
    $finance = $this->financeReport;
    $totals = $finance['totals'];
    $money = fn (int|float $value): string => number_format((float) $value, 2, ',', '.').' €';
    $groupedByYear = $this->isGroupedByYear();
    $incomeSeries = [
        ['name' => __('Sponsorships'), 'color' => 'var(--chart-series-1)', 'values' => array_column($finance['incomeBuckets'], 'sponsorships')],
        ['name' => __('Membership fees'), 'color' => 'var(--chart-series-2)', 'values' => array_column($finance['incomeBuckets'], 'membershipFees')],
        ['name' => __('Joining fees'), 'color' => 'var(--chart-series-3)', 'values' => array_column($finance['incomeBuckets'], 'joiningFees')],
    ];

    if ($totals['adoptionFees'] > 0) {
        $incomeSeries[] = ['name' => __('Adoption fees'), 'color' => 'var(--chart-series-4)', 'values' => array_column($finance['incomeBuckets'], 'adoptionFees')];
    }
@endphp

<div class="report-sections flex flex-col gap-6">
    <div class="report-section grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ([
            ['label' => __('Total income'), 'value' => $money($totals['income'])],
            ['label' => __('Sponsorships'), 'value' => $money($totals['sponsorships'])],
            ['label' => __('Membership fees'), 'value' => $money($totals['membershipFees'])],
            ['label' => __('Joining fees'), 'value' => $money($totals['joiningFees'])],
            ['label' => __('Adoption fees'), 'value' => $money($totals['adoptionFees'])],
            ['label' => __('Monthly value of active sponsorships'), 'value' => $money($finance['sponsorships']['monthlyValue'])],
        ] as $tile)
            <div wire:key="finance-tile-{{ $loop->index }}" class="report-section flex flex-col gap-2 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
                <span class="text-sm font-medium text-neutral-500 dark:text-neutral-400">{{ $tile['label'] }}</span>
                <span class="text-3xl font-semibold text-neutral-900 dark:text-white">{{ $tile['value'] }}</span>
            </div>
        @endforeach
    </div>

    <div class="report-section flex flex-col gap-4 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900" style="break-after: page;">
        <div class="flex flex-col gap-1">
            <h2 class="text-base font-semibold text-neutral-900 dark:text-white">{{ __('Income') }}</h2>
            <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ ($groupedByYear ? __('Per year') : __('Per month')).' · '.__('By payment date') }}</p>
        </div>
        <x-charts.columns :title="__('Income')" :labels="$this->periodLabels" :series="$incomeSeries" money />
    </div>

    <div class="report-section flex flex-col gap-4 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
        <div class="flex flex-col gap-1">
            <h2 class="text-base font-semibold text-neutral-900 dark:text-white">{{ __('Active sponsorships') }}</h2>
            <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ $groupedByYear ? __('At the end of each year') : __('At the end of each month') }}</p>
        </div>
        <x-charts.line :name="__('Active sponsorships')" :labels="$this->periodLabels" :values="$finance['activeSponsorshipsBuckets']" />
    </div>

    <div class="report-sections grid gap-6 lg:grid-cols-2">
        <div class="report-section flex flex-col gap-4 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <h2 class="text-base font-semibold text-neutral-900 dark:text-white">{{ __('Sponsorships') }}</h2>
            <dl class="grid grid-cols-2 gap-4 text-sm">
                <div class="flex flex-col gap-1">
                    <dt class="text-neutral-500 dark:text-neutral-400">{{ __('Active sponsorships') }}</dt>
                    <dd class="text-2xl font-semibold text-neutral-900 dark:text-white">{{ $finance['sponsorships']['active'] }}</dd>
                </div>
                <div class="flex flex-col gap-1">
                    <dt class="text-neutral-500 dark:text-neutral-400">{{ __('Sponsored animals') }}</dt>
                    <dd class="text-2xl font-semibold text-neutral-900 dark:text-white">{{ $finance['sponsorships']['sponsoredPets'] }}</dd>
                </div>
            </dl>
        </div>

        <div class="report-section flex flex-col gap-4 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <h2 class="text-base font-semibold text-neutral-900 dark:text-white">{{ __('Members') }}</h2>
            <dl class="grid grid-cols-3 gap-4 text-sm">
                <div class="flex flex-col gap-1">
                    <dt class="text-neutral-500 dark:text-neutral-400">{{ __('Active members') }}</dt>
                    <dd class="text-2xl font-semibold text-neutral-900 dark:text-white">{{ $finance['members']['active'] }}</dd>
                </div>
                <div class="flex flex-col gap-1">
                    <dt class="text-neutral-500 dark:text-neutral-400">{{ __('New members') }}</dt>
                    <dd class="text-2xl font-semibold text-neutral-900 dark:text-white">{{ $finance['members']['joined'] }}</dd>
                </div>
                <div class="flex flex-col gap-1">
                    <dt class="text-neutral-500 dark:text-neutral-400">{{ __('Fees overdue') }}</dt>
                    <dd class="text-2xl font-semibold text-neutral-900 dark:text-white">{{ $finance['members']['inArrears'] }}</dd>
                </div>
            </dl>
            <x-charts.bars :items="[
                ['label' => __('Membership fees expected'), 'value' => $finance['members']['feesExpected'], 'display' => $money($finance['members']['feesExpected'])],
                ['label' => __('Membership fees collected'), 'value' => $finance['members']['feesCollected'], 'display' => $money($finance['members']['feesCollected'])],
            ]" />
        </div>
    </div>

    <div class="report-section flex flex-col gap-4 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900" style="break-after: page;">
        <div class="flex flex-col gap-1">
            <h2 class="text-base font-semibold text-neutral-900 dark:text-white">{{ __('Member payments by method') }}</h2>
            <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Membership and joining fees received in the period') }}</p>
        </div>
        <x-charts.bars :items="array_map(fn (array $item): array => $item + ['display' => $money($item['value'])], $finance['paymentsByMethod'])" />
    </div>

    <div class="report-section overflow-hidden rounded-xl border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-neutral-900">
        <div class="flex flex-col gap-1 border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
            <h2 class="text-base font-semibold text-neutral-900 dark:text-white">{{ __('Sponsorships ending soon') }}</h2>
            <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Paid period ends in the next :days days and there is no later payment', ['days' => 30]) }}</p>
        </div>

        @if ($finance['sponsorshipsEnding']->isEmpty())
            <p class="p-6 text-center text-sm text-neutral-500 dark:text-neutral-400">{{ __('No sponsorships ending soon') }}</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-neutral-50 text-xs uppercase text-neutral-500 dark:bg-neutral-800 dark:text-neutral-400">
                        <tr>
                            <th scope="col" class="px-6 py-3 font-medium">{{ __('Pet') }}</th>
                            <th scope="col" class="px-6 py-3 font-medium">{{ __('Sponsor') }}</th>
                            <th scope="col" class="px-6 py-3 font-medium">{{ __('Paid until') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                        @foreach ($finance['sponsorshipsEnding'] as $payment)
                            <tr wire:key="sponsorship-ending-{{ $payment->sponsorship_id }}">
                                <td class="px-6 py-3">
                                    <span class="font-medium text-neutral-900 dark:text-white">{{ $payment->sponsorship->pet?->name }}</span>
                                    <span class="block text-xs text-neutral-500 dark:text-neutral-400">{{ $payment->sponsorship->pet?->species?->name }} - {{ $payment->sponsorship->pet?->ref }}</span>
                                </td>
                                <td class="px-6 py-3">
                                    @if ($payment->sponsorship->pet)
                                        <a href="{{ route('pets.sponsor.show', [$payment->sponsorship->pet, $payment->sponsorship]) }}" wire:navigate class="text-neutral-900 hover:underline dark:text-white">{{ $payment->sponsorship->name }}</a>
                                    @else
                                        {{ $payment->sponsorship->name }}
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-neutral-600 dark:text-neutral-300">{{ $payment->end_date->format('d/m/Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
