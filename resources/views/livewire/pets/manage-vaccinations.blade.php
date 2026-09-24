@php
    $canEdit = auth()->user()->canEditCurrentShelter();
@endphp

<div class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex items-center justify-between">
        <flux:heading size="xl">{{ __('Vaccinations') }}</flux:heading>
    </div>

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
        <flux:input
            wire:model.live.debounce.300ms="search"
            icon="magnifying-glass"
            :placeholder="__('Search by vaccine, pet, lot number, veterinarian or notes')"
            class="sm:max-w-xs"
        />

        <flux:select wire:model.live="nextDueFilter" class="sm:max-w-xs">
            <flux:select.option value="">{{ __('All') }}</flux:select.option>
            <flux:select.option value="within_week">{{ __('Due date within a week') }}</flux:select.option>
            <flux:select.option value="within_two_weeks">{{ __('Due date within two weeks') }}</flux:select.option>
            <flux:select.option value="within_month">{{ __('Due date within a month') }}</flux:select.option>
            <flux:select.option value="overdue">{{ __('Vaccine overdue') }}</flux:select.option>
        </flux:select>
    </div>

    <div class="rounded-xl bg-white shadow-sm dark:bg-neutral-900">
        <div class="overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-neutral-50 text-xs uppercase text-neutral-500 dark:bg-neutral-800 dark:text-neutral-400">
                        <tr>
                            <th scope="col" class="px-6 py-3 font-medium">{{ __('Vaccine') }}</th>
                            <th scope="col" class="px-6 py-3 font-medium">{{ __('Administered Date') }}</th>
                            <th scope="col" class="px-6 py-3 font-medium">{{ __('Due Date') }}</th>
                            <th scope="col" class="px-6 py-3 text-center font-medium">{{ __('Pet') }}</th>
                            <th scope="col" class="px-6 py-3 font-medium">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                        @forelse ($this->vaccinations as $vaccination)
                            @php
                                $mainImage = $vaccination->pet->images->firstWhere('is_main', true) ?? $vaccination->pet->images->first();
                                $isScheduled = $vaccination->administered_date === null;
                                $isOverdue = $isScheduled && $vaccination->due_date !== null && $vaccination->due_date->lt(now()->startOfDay());
                                $isDueSoon = $isScheduled && $vaccination->due_date !== null && $vaccination->due_date->lte(now()->addWeek());
                                $nextDueDateClass = match (true) {
                                    $isOverdue => 'bg-red-50 text-red-800 dark:bg-red-950/40 dark:text-red-200',
                                    $isDueSoon => 'bg-amber-50 text-amber-800 dark:bg-amber-950/40 dark:text-amber-200',
                                    default => '',
                                };
                            @endphp
                            <tr wire:key="vaccination-{{ $vaccination->id }}">
                                <td class="px-6 py-3">{{ $vaccination->vaccine->name }}</td>
                                <td class="px-6 py-3">{{ $vaccination->administered_date?->format('d/m/Y') ?? '—' }}</td>
                                <td class="px-6 py-3 {{ $nextDueDateClass }}">{{ $vaccination->due_date?->format('d/m/Y') ?? '—' }}</td>
                                <td class="px-6 py-3 text-center">
                                    <div class="flex flex-col items-center gap-1">
                                        <span class="text-xs text-neutral-500 dark:text-neutral-400">
                                            {{ $vaccination->pet->species->name }} - {{ $vaccination->pet->ref }}
                                        </span>

                                        @if ($mainImage)
                                            <flux:avatar
                                                size="lg"
                                                class="size-16"
                                                :src="\Illuminate\Support\Facades\Storage::url($mainImage->image_path)"
                                                :name="$vaccination->pet->name"
                                            />
                                        @endif

                                        <a href="{{ route('pets.show', $vaccination->pet) }}" wire:navigate class="font-medium text-neutral-900 hover:underline dark:text-white">
                                            {{ $vaccination->pet->name }}
                                        </a>
                                    </div>
                                </td>
                                <td class="px-6 py-3">
                                    <div class="flex items-center gap-2">
                                        <flux:modal.trigger name="vaccination-show-{{ $vaccination->id }}">
                                            <flux:button size="sm" variant="subtle" icon="eye" :aria-label="__('Show')" />
                                        </flux:modal.trigger>

                                        @if ($canEdit)
                                            <flux:button
                                                :href="route('pets.vaccinate.edit', [$vaccination->pet, $vaccination])"
                                                size="sm"
                                                variant="subtle"
                                                icon="pencil"
                                                :aria-label="__('Edit')"
                                                wire:navigate
                                            />

                                            <flux:modal.trigger name="confirm-vaccination-deletion-{{ $vaccination->id }}">
                                                <flux:button size="sm" variant="subtle" icon="trash" :aria-label="__('Delete')" />
                                            </flux:modal.trigger>
                                        @endif

                                        <flux:modal name="vaccination-show-{{ $vaccination->id }}" class="max-w-lg">
                                            <div class="space-y-6">
                                                <flux:heading size="lg">{{ $vaccination->vaccine->name }}</flux:heading>

                                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                                    <div>
                                                        <flux:text class="text-neutral-500 dark:text-neutral-400">{{ __('Administered Date') }}</flux:text>
                                                        <flux:text class="text-neutral-700 dark:text-neutral-300">{{ $vaccination->administered_date?->format('d/m/Y') ?? '—' }}</flux:text>
                                                    </div>
                                                    <div>
                                                        <flux:text class="text-neutral-500 dark:text-neutral-400">{{ __('Due Date') }}</flux:text>
                                                        <flux:text class="text-neutral-700 dark:text-neutral-300">{{ $vaccination->due_date?->format('d/m/Y') ?? '—' }}</flux:text>
                                                    </div>
                                                    <div>
                                                        <flux:text class="text-neutral-500 dark:text-neutral-400">{{ __('Lot Number') }}</flux:text>
                                                        <flux:text class="text-neutral-700 dark:text-neutral-300">{{ $vaccination->lot_number ?? '—' }}</flux:text>
                                                    </div>
                                                    <div>
                                                        <flux:text class="text-neutral-500 dark:text-neutral-400">{{ __('Veterinarian') }}</flux:text>
                                                        <flux:text class="text-neutral-700 dark:text-neutral-300">{{ $vaccination->veterinarian_name ?? '—' }}</flux:text>
                                                    </div>
                                                </div>

                                                <div>
                                                    <flux:text class="text-neutral-500 dark:text-neutral-400">{{ __('Notes') }}</flux:text>
                                                    <flux:text class="text-neutral-700 dark:text-neutral-300 whitespace-pre-line">{{ $vaccination->notes ?? '—' }}</flux:text>
                                                </div>

                                                <div class="flex justify-end">
                                                    <flux:modal.close>
                                                        <flux:button variant="filled">{{ __('Close') }}</flux:button>
                                                    </flux:modal.close>
                                                </div>
                                            </div>
                                        </flux:modal>

                                        @if ($canEdit)
                                            <flux:modal name="confirm-vaccination-deletion-{{ $vaccination->id }}" class="max-w-lg">
                                                <div class="space-y-6">
                                                    <div>
                                                        <flux:heading size="lg">{{ __('Are you sure you want to delete this record?') }}</flux:heading>
                                                        <flux:subheading>{{ __('This record can be restored later by an administrator') }}</flux:subheading>
                                                    </div>

                                                    <div class="flex justify-end space-x-2 rtl:space-x-reverse">
                                                        <flux:modal.close>
                                                            <flux:button variant="filled">{{ __('Cancel') }}</flux:button>
                                                        </flux:modal.close>

                                                        <flux:button variant="danger" wire:click="deleteVaccination({{ $vaccination->id }})">
                                                            {{ __('Delete') }}
                                                        </flux:button>
                                                    </div>
                                                </div>
                                            </flux:modal>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-6 text-center text-neutral-500 dark:text-neutral-400">
                                    {{ __('No vaccinations registered') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="px-6 py-3">
        <flux:pagination :paginator="$this->vaccinations" class="!border-t-0 !pt-0" />
    </div>
</div>
