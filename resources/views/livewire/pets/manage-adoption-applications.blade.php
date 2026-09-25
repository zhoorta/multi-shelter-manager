@php
    $canEdit = auth()->user()->canEditCurrentShelter();
    $statusColors = ['pending' => 'amber', 'approved' => 'lime', 'rejected' => 'zinc'];
@endphp

<div class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex items-center justify-between">
        <flux:heading size="xl">{{ __('Adoption Applications') }}</flux:heading>
    </div>

    <div class="flex flex-col gap-4 sm:flex-row sm:flex-wrap sm:items-end">
        <flux:input
            wire:model.live.debounce.300ms="search"
            icon="magnifying-glass"
            :label="__('Search')"
            :placeholder="__('Search by applicant, contact, city or pet')"
            class="sm:max-w-xs"
        />

        <flux:select wire:model.live="statusFilter" :label="__('Status')" class="sm:max-w-xs">
            <flux:select.option value="">{{ __('All') }}</flux:select.option>
            <flux:select.option value="pending">{{ __('Pending') }}</flux:select.option>
            <flux:select.option value="approved">{{ __('Approved') }}</flux:select.option>
            <flux:select.option value="rejected">{{ __('Rejected') }}</flux:select.option>
        </flux:select>
    </div>

    @if ($canEdit && $this->staleApplicationsCount > 0)
        <flux:callout icon="information-circle" color="amber">
            <flux:callout.text>
                {{ trans_choice(':count pending application is for an animal that is no longer available.|:count pending applications are for animals that are no longer available.', $this->staleApplicationsCount) }}
            </flux:callout.text>
            <x-slot name="actions">
                <flux:button size="sm" wire:click="rejectStaleApplications">{{ __('Reject them') }}</flux:button>
            </x-slot>
        </flux:callout>
    @endif

    <div class="rounded-xl bg-white shadow-sm dark:bg-neutral-900">
        <div class="overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-neutral-50 text-xs uppercase text-neutral-500 dark:bg-neutral-800 dark:text-neutral-400">
                        <tr>
                            <th scope="col" class="px-6 py-3 font-medium">{{ __('Applicant') }}</th>
                            <th scope="col" class="px-6 py-3 font-medium">{{ __('Answers') }}</th>
                            <th scope="col" class="px-6 py-3 text-center font-medium">{{ __('Pet') }}</th>
                            <th scope="col" class="px-6 py-3 font-medium">{{ __('Options') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                        @forelse ($this->applications as $application)
                            @php
                                $mainImage = $application->pet->images->firstWhere('is_main', true) ?? $application->pet->images->first();
                            @endphp
                            <tr wire:key="adoption-application-{{ $application->id }}" class="align-top">
                                <td class="px-6 py-3">
                                    <div class="flex flex-col gap-1">
                                        <span class="font-medium text-neutral-900 dark:text-white">{{ $application->name }}</span>
                                        <a href="mailto:{{ $application->email }}" class="text-neutral-500 hover:underline dark:text-neutral-400">{{ $application->email }}</a>
                                        <a href="tel:{{ $application->phone }}" class="text-neutral-500 hover:underline dark:text-neutral-400">{{ $application->phone }}</a>
                                        <span class="text-neutral-500 dark:text-neutral-400">{{ collect([$application->postal_code, $application->city])->filter()->implode(' ') }}</span>
                                        <span class="text-neutral-500 dark:text-neutral-400">
                                            {!! __('Sent at :date', ['date' => '<strong>'.$application->created_at->format('d/m/Y H:i').'</strong>']) !!}
                                        </span>
                                        <div>
                                            <flux:badge size="sm" :color="$statusColors[$application->status]">{{ __(Str::ucfirst($application->status)) }}</flux:badge>
                                        </div>
                                        @if ($application->reviewed_at)
                                            <span class="text-xs text-neutral-500 dark:text-neutral-400">
                                                {{ $application->reviewer?->name }} · {{ $application->reviewed_at->format('d/m/Y') }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-3">
                                    <dl class="flex max-w-md flex-col gap-1 text-neutral-600 dark:text-neutral-300">
                                        <div><dt class="inline font-medium">{{ __('Housing Type') }}:</dt> <dd class="inline">{{ __(Str::ucfirst($application->housing_type)) }}</dd></div>
                                        <div><dt class="inline font-medium">{{ __('Garden or yard') }}:</dt> <dd class="inline">{{ $application->has_garden ? __('Yes') : __('No') }}</dd></div>
                                        <div><dt class="inline font-medium">{{ __('Children at home') }}:</dt> <dd class="inline">{{ $application->has_children ? __('Yes') : __('No') }}</dd></div>
                                        @if ($application->other_animals)
                                            <div><dt class="inline font-medium">{{ __('Other animals at home') }}:</dt> <dd class="inline">{{ $application->other_animals }}</dd></div>
                                        @endif
                                        <dd class="mt-2 whitespace-pre-line">{{ $application->message }}</dd>
                                    </dl>
                                </td>
                                <td class="px-6 py-3 text-center">
                                    <div class="flex flex-col items-center gap-1">
                                        <span class="text-xs text-neutral-500 dark:text-neutral-400">
                                            {{ $application->pet->species->name }} - {{ $application->pet->ref }}
                                        </span>

                                        @if ($mainImage)
                                            <flux:avatar
                                                size="lg"
                                                class="size-16"
                                                :src="\Illuminate\Support\Facades\Storage::url($mainImage->image_path)"
                                                :name="$application->pet->name"
                                            />
                                        @endif

                                        <a href="{{ route('pets.show', $application->pet) }}" wire:navigate class="font-medium text-neutral-900 hover:underline dark:text-white">
                                            {{ $application->pet->name }}
                                        </a>
                                    </div>
                                </td>
                                <td class="px-6 py-3">
                                    <div class="flex items-center gap-2">
                                        @if ($canEdit && $application->status === 'pending')
                                            @if ($application->pet->status !== 'adopted')
                                                <flux:button
                                                    :href="route('pets.adopt', [$application->pet, 'application' => $application->id])"
                                                    size="sm"
                                                    variant="subtle"
                                                    icon="check"
                                                    :aria-label="__('Approve')"
                                                    :title="__('Approve')"
                                                    wire:navigate
                                                />
                                            @endif

                                            <flux:button
                                                size="sm"
                                                variant="subtle"
                                                icon="x-mark"
                                                :aria-label="__('Reject')"
                                                :title="__('Reject')"
                                                wire:click="rejectApplication({{ $application->id }})"
                                            />
                                        @endif

                                        @if ($application->adoption_id)
                                            <flux:button
                                                :href="route('pets.adopt.show', [$application->pet, $application->adoption_id])"
                                                size="sm"
                                                variant="subtle"
                                                icon="eye"
                                                :aria-label="__('View')"
                                                wire:navigate
                                            />
                                        @endif

                                        @if ($canEdit)
                                            <flux:modal.trigger name="confirm-application-deletion-{{ $application->id }}">
                                                <flux:button
                                                    size="sm"
                                                    variant="subtle"
                                                    icon="trash"
                                                    :aria-label="__('Delete')"
                                                />
                                            </flux:modal.trigger>

                                            <flux:modal name="confirm-application-deletion-{{ $application->id }}" class="max-w-lg">
                                                <div class="space-y-6">
                                                    <div>
                                                        <flux:heading size="lg">{{ __('Are you sure you want to delete this record?') }}</flux:heading>
                                                        <flux:subheading>{{ __('This record can be restored later by an administrator') }}</flux:subheading>
                                                    </div>

                                                    <div class="flex justify-end space-x-2 rtl:space-x-reverse">
                                                        <flux:modal.close>
                                                            <flux:button variant="filled">{{ __('Cancel') }}</flux:button>
                                                        </flux:modal.close>

                                                        <flux:button variant="danger" wire:click="deleteApplication({{ $application->id }})">
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
                                <td colspan="4" class="px-6 py-6 text-center text-neutral-500 dark:text-neutral-400">
                                    {{ __('No adoption applications') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="px-6 py-3">
        <flux:pagination :paginator="$this->applications" class="!border-t-0 !pt-0" />
    </div>
</div>
