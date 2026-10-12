@php
    $canEdit = auth()->user()->canEditArea('pets');
@endphp
<div class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex flex-wrap items-center justify-between gap-2">
        <div>
            <flux:heading size="xl">{{ $colony->name }}</flux:heading>
            <flux:subheading>{{ collect([$colony->species->name, $colony->location, $colony->city])->filter()->implode(' · ') }}</flux:subheading>
        </div>

        <div class="flex flex-wrap gap-2">
            <flux:button icon="arrow-left" :href="route('colonies.index')" wire:navigate>{{ __('Back') }}</flux:button>
            <flux:button icon="arrow-down-tray" wire:click="exportCsv">{{ __('Export CSV') }}</flux:button>
            @if ($canEdit)
                <flux:button variant="primary" icon="plus" :href="route('pets.create', ['colony' => $colony->id, 'species' => $colony->species_id])" wire:navigate>
                    {{ __('Add :species', ['species' => $colony->species->name]) }}
                </flux:button>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4 lg:grid-cols-5">
        @foreach ([
            __('Animals') => $this->totals['total'],
            __('Neutered') => $this->totals['neutered'],
            __('Pending neutering') => $this->totals['pending'],
            ...($this->totals['unknown'] > 0 ? [__('No neutering info') => $this->totals['unknown']] : []),
            __('Ear-tipped') => $this->totals['earTipped'],
        ] as $label => $value)
            <div class="rounded-xl border border-neutral-200 bg-white p-4 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
                <div class="text-sm text-neutral-500 dark:text-neutral-400">{{ $label }}</div>
                <div class="text-2xl font-semibold text-neutral-900 dark:text-white">{{ $value }}</div>
            </div>
        @endforeach
    </div>

    <div class="flex flex-wrap items-end gap-4">
        <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" :placeholder="__('Search')" class="max-w-xs" />

        <flux:select wire:model.live="neuteringFilter" class="max-w-xs">
            <flux:select.option value="">{{ __('All') }}</flux:select.option>
            <flux:select.option value="neutered">{{ __('Neutered') }}</flux:select.option>
            <flux:select.option value="pending">{{ __('Pending neutering') }}</flux:select.option>
            <flux:select.option value="unknown">{{ __('No neutering info') }}</flux:select.option>
            <flux:select.option value="ear_tipped">{{ __('Ear-tipped') }}</flux:select.option>
        </flux:select>
    </div>

    <div class="overflow-hidden rounded-xl border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-neutral-900">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-neutral-50 text-xs uppercase text-neutral-500 dark:bg-neutral-800 dark:text-neutral-400">
                    <tr>
                        <th scope="col" class="px-6 py-3 font-medium">{{ __('Name') }}</th>
                        <th scope="col" class="px-6 py-3 font-medium">{{ __('Gender') }}</th>
                        <th scope="col" class="px-6 py-3 font-medium">{{ __('Microchip / Chip') }}</th>
                        <th scope="col" class="px-6 py-3 font-medium">{{ __('Captured on') }}</th>
                        <th scope="col" class="px-6 py-3 font-medium">{{ __('Neutered') }}</th>
                        <th scope="col" class="px-6 py-3 font-medium">{{ __('Ear-tipped') }}</th>
                        <th scope="col" class="px-6 py-3 font-medium">{{ __('Returned on') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                    @forelse ($this->cats as $cat)
                        <tr wire:key="cat-{{ $cat->id }}">
                            <td class="px-6 py-3 font-medium text-neutral-900 dark:text-white">
                                <a href="{{ route('pets.show', $cat) }}" wire:navigate class="hover:underline">{{ $cat->name }}</a>
                            </td>
                            <td class="px-6 py-3">{{ $cat->gender === 'male' ? __('Male') : __('Female') }}</td>
                            <td class="px-6 py-3">{{ $cat->chip }}</td>
                            <td class="px-6 py-3">{{ $cat->captured_at?->format('d/m/Y') }}</td>
                            <td class="px-6 py-3">
                                @if ($cat->is_neutered)
                                    {{ $cat->neutered_at?->format('d/m/Y') ?? __('Yes') }}
                                @else
                                    {{ __('No') }}
                                @endif
                            </td>
                            <td class="px-6 py-3">{{ $cat->is_ear_tipped ? __('Yes') : __('No') }}</td>
                            <td class="px-6 py-3">{{ $cat->returned_at?->format('d/m/Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-6 text-center text-neutral-500 dark:text-neutral-400">
                                {{ __('No animals in this colony') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
