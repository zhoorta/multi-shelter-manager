<div class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex items-center justify-between">
        <flux:heading size="xl">{{ __('Volunteers') }}</flux:heading>

        @if (auth()->user()->role === 'manager')
            <flux:button variant="primary" icon="plus" :href="route('volunteers.create')" wire:navigate>
                {{ __('Create') }}
            </flux:button>
        @endif
    </div>

    <div class="flex flex-col gap-4 sm:flex-row sm:flex-wrap sm:items-center">
        <flux:input
            wire:model.live.debounce.300ms="search"
            icon="magnifying-glass"
            :label="__('Search')"
            :placeholder="__('Search by name, phone, email, TIN or notes')"
            class="sm:max-w-xs"
        />

        <flux:select wire:model.live="speciesFilter" :label="__('Sector')" class="sm:max-w-xs">
            <flux:select.option value="">{{ __('All') }}</flux:select.option>
            @foreach ($this->species as $species)
                <flux:select.option value="{{ $species->id }}">{{ $species->name_plural }}</flux:select.option>
            @endforeach
        </flux:select>

        <flux:select wire:model.live="dayFilter" :label="__('Day of the Week')" class="sm:max-w-xs">
            <flux:select.option value="">{{ __('All') }}</flux:select.option>
            @foreach (\App\Models\VolunteerAvailability::DAYS as $dayIndex => $day)
                <flux:select.option value="{{ $dayIndex }}">{{ __($day) }}</flux:select.option>
            @endforeach
        </flux:select>

        <flux:select wire:model.live="activityFilter" :label="__('Activities')" class="sm:max-w-xs">
            <flux:select.option value="">{{ __('All') }}</flux:select.option>
            @foreach ($this->activities as $activity)
                <flux:select.option value="{{ $activity->id }}">{{ $activity->name }}</flux:select.option>
            @endforeach
        </flux:select>
    </div>

    <div class="rounded-xl bg-white shadow-sm dark:bg-neutral-900">
        <div class="overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-neutral-50 text-xs uppercase text-neutral-500 dark:bg-neutral-800 dark:text-neutral-400">
                        <tr>
                            <th scope="col" class="px-6 py-3 font-medium">{{ __('Identification and Contacts') }}</th>
                            <th scope="col" class="px-6 py-3 font-medium">{{ __('Activity and Availability') }}</th>
                            <th scope="col" class="px-6 py-3 font-medium">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                        @forelse ($this->volunteers as $item)
                            @php
                                $availabilityLines = $item->availabilities->map(function ($availability) {
                                    $periods = collect([
                                        $availability->mornings ? lcfirst(__('Morning')) : null,
                                        $availability->afternoons ? lcfirst(__('Afternoon')) : null,
                                    ])->filter()->join(' '.__('and').' ');

                                    return __(\App\Models\VolunteerAvailability::DAYS[$availability->day_index]).' '.$periods.' '.lcfirst(__($availability->frequency));
                                });
                            @endphp
                            <tr wire:key="volunteer-{{ $item->id }}">
                                <td class="px-6 py-3">
                                    <div class="flex items-start gap-3">
                                        @if ($item->image_path)
                                            <flux:avatar
                                                size="xl"
                                                class="size-16 shrink-0"
                                                :src="\Illuminate\Support\Facades\Storage::url($item->image_path)"
                                                :name="$item->name"
                                                :href="route('volunteers.show', $item)"
                                                wire:navigate
                                            />
                                        @endif

                                        <div class="flex flex-col gap-1">
                                            <a href="{{ route('volunteers.show', $item) }}" wire:navigate class="font-medium text-neutral-900 hover:underline dark:text-white">{{ $item->name }}</a>
                                            <span class="text-neutral-500 dark:text-neutral-400">{{ $item->phone ?? '—' }}</span>
                                            <span class="text-neutral-500 dark:text-neutral-400">{{ $item->email ?? '—' }}</span>
                                            @if ($item->professional_activity)
                                                <span class="text-neutral-500 dark:text-neutral-400">{{ $item->professional_activity }}</span>
                                            @endif
                                            <span class="text-neutral-500 dark:text-neutral-400">{{ __('Started at') }} {{ $item->start_date?->format('d/m/Y') ?? '—' }}</span>
                                            @if ($item->end_date)
                                                <span class="text-neutral-500 dark:text-neutral-400">{{ __('Ended at') }} {{ $item->end_date->format('d/m/Y') }}</span>
                                            @endif
                                            @if ($item->notes)
                                                <span class="mt-1 line-clamp-2 max-w-xs text-neutral-500 dark:text-neutral-400">{{ $item->notes }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-3">
                                    <div class="flex flex-col gap-1 text-neutral-500 dark:text-neutral-400">
                                        <span class="whitespace-pre-line">{{ $item->activities->isNotEmpty() ? $item->activities->pluck('name')->join("\n") : '—' }}</span>
                                        <span class="mt-3">{{ __('Sector') }}: {{ $item->species->isNotEmpty() ? $item->species->pluck('name_plural')->join(', ') : '—' }}</span>
                                        <span class="mt-3 whitespace-pre-line">{{ $availabilityLines->isNotEmpty() ? $availabilityLines->join("\n") : '—' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-3">
                                    <div class="flex items-center gap-2">
                                        <flux:button
                                            size="sm"
                                            variant="subtle"
                                            icon="eye"
                                            :href="route('volunteers.show', $item)"
                                            :aria-label="__('View')"
                                            wire:navigate
                                        />

                                        @if (auth()->user()->role === 'manager')
                                            <flux:modal.trigger name="confirm-volunteer-deletion-{{ $item->id }}">
                                                <flux:button
                                                    size="sm"
                                                    variant="subtle"
                                                    icon="trash"
                                                    :aria-label="__('Delete')"
                                                />
                                            </flux:modal.trigger>

                                            <flux:modal name="confirm-volunteer-deletion-{{ $item->id }}" class="max-w-lg">
                                                <div class="space-y-6">
                                                    <div>
                                                        <flux:heading size="lg">{{ __('Are you sure you want to delete this record?') }}</flux:heading>
                                                        <flux:subheading>{{ __('This record can be restored later by an administrator') }}</flux:subheading>
                                                    </div>

                                                    <div class="flex justify-end space-x-2 rtl:space-x-reverse">
                                                        <flux:modal.close>
                                                            <flux:button variant="filled">{{ __('Cancel') }}</flux:button>
                                                        </flux:modal.close>

                                                        <flux:button variant="danger" wire:click="deleteVolunteer({{ $item->id }})">
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
                                <td colspan="3" class="px-6 py-6 text-center text-neutral-500 dark:text-neutral-400">
                                    {{ __('No volunteers registered') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="px-6 py-3">
        <flux:pagination :paginator="$this->volunteers" class="!border-t-0 !pt-0" />
    </div>
</div>
