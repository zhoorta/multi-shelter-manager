<div class="shelter-report report-sections flex h-full w-full flex-1 flex-col gap-6">
    <x-charts.styles />

    @php
        [$periodStart, $periodEnd] = $this->dateRange();
    @endphp

    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div class="flex flex-col gap-1">
            <flux:heading size="xl">{{ __('Reports') }}</flux:heading>
            <flux:text>{{ $periodStart->format('d/m/Y') }} – {{ $periodEnd->format('d/m/Y') }}</flux:text>
        </div>

        <div class="flex flex-col gap-3 sm:flex-row sm:items-end print:hidden">
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

            {{-- Opens in a new tab with the tab and period on screen, so the printout shows the same section and figures. --}}
            <flux:button
                :href="route('reports.print', array_filter(['tab' => $this->activeTab() === 'animals' ? '' : $this->activeTab(), 'period' => $period, 'from' => $period === 'custom' ? $from : '', 'to' => $period === 'custom' ? $to : '']))"
                icon="printer"
                target="_blank"
                :aria-label="__('Print')"
            />
        </div>
    </div>


    <flux:radio.group wire:model.live="tab" variant="segmented" class="w-full sm:w-auto sm:self-start print:hidden">
        @foreach ($this->reportTabs() as $tabKey => $tabLabel)
            <flux:radio value="{{ $tabKey }}" :label="$tabLabel" />
        @endforeach
    </flux:radio.group>

    @include('livewire.reports.partials.'.$this->activeTab())
</div>
