@php
    $canEdit = auth()->user()->canEditCurrentShelter();
    $currentYear = today()->year;
    $currentMonth = today()->month;
    $planMonths = range(1, 12);
    $isOverdueCell = fn (int $planMonth): bool => $planMonth === 0
        ? $year <= $currentYear
        : $year < $currentYear || ($year === $currentYear && $planMonth < $currentMonth);
    $monthTotals = collect(range(0, 12))->mapWithKeys(fn (int $planMonth): array => [$planMonth => collect($this->counts)->sum(fn (array $counts): int => $counts[$planMonth] ?? 0)]);
    $selectedVaccine = $vaccine !== null ? $this->vaccines->firstWhere('id', $vaccine) : null;
@endphp

<div class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex flex-col gap-1">
            <flux:heading size="xl">{{ __('Vaccination Plan') }}</flux:heading>
            <flux:subheading>{{ __('Vaccinations due per month for the animals in the shelter. Choose a month to see the animals and print the list for the vet.') }}</flux:subheading>
        </div>

        <div class="flex items-center gap-2">
            <flux:select wire:model.live="year" class="w-28">
                @foreach ($this->yearOptions as $yearOption)
                    <flux:select.option value="{{ $yearOption }}">{{ $yearOption }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:button :href="route('pets.vaccinations.index')" variant="filled" icon="arrow-left" wire:navigate>
                {{ __('Vaccinations') }}
            </flux:button>
        </div>
    </div>

    <div class="rounded-xl bg-white shadow-sm dark:bg-neutral-900">
        <div class="overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-neutral-50 text-xs uppercase text-neutral-500 dark:bg-neutral-800 dark:text-neutral-400">
                        <tr>
                            <th scope="col" class="px-4 py-3 font-medium">{{ __('Vaccine') }}</th>
                            <th scope="col" class="px-2 py-3 text-center font-medium">
                                <button type="button" wire:click="selectMonth(0)" class="uppercase hover:underline">{{ __('Before :year', ['year' => $year]) }}</button>
                            </th>
                            @foreach ($planMonths as $planMonth)
                                <th scope="col" class="px-2 py-3 text-center font-medium">
                                    <button type="button" wire:click="selectMonth({{ $planMonth }})" class="uppercase hover:underline">
                                        {{ \Illuminate\Support\Carbon::create($year, $planMonth)->translatedFormat('M') }}
                                    </button>
                                </th>
                            @endforeach
                            <th scope="col" class="px-4 py-3 text-center font-medium">{{ __('Total') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                        @forelse ($this->vaccines as $planVaccine)
                            @php
                                $vaccineCounts = $this->counts[$planVaccine->id] ?? [];
                            @endphp
                            <tr wire:key="plan-vaccine-{{ $planVaccine->id }}">
                                <td class="px-4 py-3">
                                    <div class="font-medium text-neutral-900 dark:text-white">{{ $planVaccine->name }}</div>
                                    <div class="text-xs text-neutral-500 dark:text-neutral-400">{{ $planVaccine->species->pluck('name')->implode(', ') }}</div>
                                </td>
                                @foreach (range(0, 12) as $planMonth)
                                    @php
                                        $count = $vaccineCounts[$planMonth] ?? 0;
                                        $isSelected = $this->month === $planMonth && ($vaccine === null || $vaccine === $planVaccine->id);
                                    @endphp
                                    <td class="px-1 py-2 text-center">
                                        @if ($count > 0)
                                            <button
                                                type="button"
                                                wire:click="selectMonth({{ $planMonth }}, {{ $planVaccine->id }})"
                                                @class([
                                                    'min-w-9 rounded-md px-2 py-1 font-medium',
                                                    'ring-2 ring-neutral-900 dark:ring-white' => $isSelected,
                                                    'bg-red-50 text-red-800 hover:bg-red-100 dark:bg-red-950/40 dark:text-red-200' => $isOverdueCell($planMonth),
                                                    'bg-sky-50 text-sky-800 hover:bg-sky-100 dark:bg-sky-950/40 dark:text-sky-200' => ! $isOverdueCell($planMonth),
                                                ])
                                            >
                                                {{ $count }}
                                            </button>
                                        @else
                                            <span class="text-neutral-300 dark:text-neutral-600">·</span>
                                        @endif
                                    </td>
                                @endforeach
                                <td class="px-4 py-3 text-center font-medium">{{ array_sum($vaccineCounts) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="15" class="px-6 py-8 text-center text-neutral-500 dark:text-neutral-400">
                                    {{ __('No vaccinations due. Vaccinations with a next due date appear here.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if ($this->vaccines->isNotEmpty())
                        <tfoot class="border-t border-neutral-200 bg-neutral-50 font-medium dark:border-neutral-700 dark:bg-neutral-800">
                            <tr>
                                <td class="px-4 py-3">{{ __('Total') }}</td>
                                @foreach (range(0, 12) as $planMonth)
                                    <td class="px-1 py-3 text-center">
                                        @if ($monthTotals[$planMonth] > 0)
                                            <button type="button" wire:click="selectMonth({{ $planMonth }})" class="hover:underline">{{ $monthTotals[$planMonth] }}</button>
                                        @else
                                            <span class="text-neutral-300 dark:text-neutral-600">·</span>
                                        @endif
                                    </td>
                                @endforeach
                                <td class="px-4 py-3 text-center">{{ $monthTotals->sum() }}</td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>

    @if ($this->month !== null)
        <div class="flex flex-col gap-4 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex flex-col gap-1">
                    <flux:heading size="lg">
                        {{ $this->monthLabel() }}{{ $selectedVaccine ? ' — '.$selectedVaccine->name : '' }}
                    </flux:heading>
                    <flux:text>{{ trans_choice(':count animal|:count animals', $this->duePets->count(), ['count' => $this->duePets->count()]) }}</flux:text>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <flux:button
                        :href="route('pets.vaccinations.plan.print', ['year' => $year, 'month' => $this->month, 'vaccine' => $vaccine])"
                        target="_blank"
                        variant="filled"
                        icon="printer"
                    >
                        {{ __('Print list for the vet') }}
                    </flux:button>

                    @if ($canEdit && $this->duePets->isNotEmpty() && $this->groupDueMonth() !== null)
                        @php
                            $listedVaccines = $selectedVaccine
                                ? collect([$selectedVaccine])
                                : $this->duePets->flatMap(fn (array $row) => $row['vaccinations']->pluck('vaccine'))->unique('id')->sortBy('name');
                        @endphp
                        @foreach ($listedVaccines as $listedVaccine)
                            <flux:button
                                :href="route('pets.vaccinations.group', ['vaccine' => $listedVaccine->id, 'due' => $this->groupDueMonth()])"
                                variant="primary"
                                icon="plus"
                                wire:navigate
                            >
                                {{ $selectedVaccine ? __('Group Vaccination') : __('Group Vaccination').': '.$listedVaccine->name }}
                            </flux:button>
                        @endforeach
                    @endif

                    <flux:button variant="subtle" icon="x-mark" wire:click="clearSelection" :aria-label="__('Close')" />
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-neutral-200 text-xs uppercase text-neutral-500 dark:border-neutral-700 dark:text-neutral-400">
                        <tr>
                            <th scope="col" class="py-2 pr-4 font-medium">{{ __('Pet') }}</th>
                            <th scope="col" class="py-2 pr-4 font-medium">{{ __('Microchip / Chip') }}</th>
                            <th scope="col" class="py-2 pr-4 font-medium">{{ __('Accommodation') }}</th>
                            <th scope="col" class="py-2 font-medium">{{ __('Vaccines due') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                        @forelse ($this->duePets as $row)
                            <tr wire:key="due-pet-{{ $row['pet']->id }}">
                                <td class="py-2 pr-4">
                                    <a href="{{ route('pets.show', $row['pet']) }}" wire:navigate class="font-medium text-neutral-900 hover:underline dark:text-white">{{ $row['pet']->name }}</a>
                                    <div class="text-xs text-neutral-500 dark:text-neutral-400">{{ $row['pet']->species->name }} - {{ $row['pet']->ref }}</div>
                                </td>
                                <td class="py-2 pr-4">{{ $row['pet']->chip ?? '—' }}</td>
                                <td class="py-2 pr-4">{{ collect([$row['pet']->cage?->wing?->name, $row['pet']->cage?->code])->filter()->implode(' · ') ?: '—' }}</td>
                                <td class="py-2">
                                    @foreach ($row['vaccinations'] as $vaccination)
                                        <div>
                                            <span class="font-medium">{{ $vaccination->vaccine->name }}</span>
                                            — {{ $vaccination->due_date->format('d/m/Y') }}
                                            @if ($vaccination->administered_date)
                                                <span class="text-xs text-neutral-500 dark:text-neutral-400">({{ __('last dose :date', ['date' => $vaccination->administered_date->format('d/m/Y')]) }})</span>
                                            @endif
                                        </div>
                                    @endforeach
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-6 text-center text-neutral-500 dark:text-neutral-400">{{ __('No animals have vaccinations due in this month.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
