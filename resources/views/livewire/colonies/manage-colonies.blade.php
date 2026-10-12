@php
    $canEdit = auth()->user()->canEditArea('pets');
    $canSeeCaretaker = ! auth()->user()->isViewerOfCurrentShelter();
@endphp
<div class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex items-center justify-between">
        <flux:heading size="xl">{{ __('Colonies') }}</flux:heading>

        @if ($canEdit)
            <flux:modal.trigger name="colony-form">
                <flux:button variant="primary" icon="plus" wire:click="createColony">
                    {{ __('Create') }}
                </flux:button>
            </flux:modal.trigger>
        @endif
    </div>

    <div class="flex flex-col gap-4 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
        <div class="overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-neutral-50 text-xs uppercase text-neutral-500 dark:bg-neutral-800 dark:text-neutral-400">
                        <tr>
                            <th scope="col" class="px-6 py-3 font-medium">{{ __('Name') }}</th>
                            <th scope="col" class="px-6 py-3 font-medium">{{ __('Species') }}</th>
                            <th scope="col" class="px-6 py-3 font-medium">{{ __('Colony info') }}</th>
                            <th scope="col" class="px-6 py-3 font-medium">{{ __('Animals') }}</th>
                            <th scope="col" class="px-6 py-3 font-medium">{{ __('Neutered') }}</th>
                            <th scope="col" class="px-6 py-3 font-medium">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                        @forelse ($this->colonies as $colony)
                            <tr wire:key="colony-{{ $colony->id }}">
                                <td class="px-6 py-3 font-medium text-neutral-900 dark:text-white">
                                    <a href="{{ route('colonies.show', $colony) }}" wire:navigate class="hover:underline">{{ $colony->name }}</a>
                                    @unless ($colony->is_active)
                                        <flux:badge size="sm" class="ms-2">{{ __('Inactive') }}</flux:badge>
                                    @endunless
                                </td>
                                <td class="px-6 py-3">{{ $colony->species->name }}</td>
                                <td class="px-6 py-3">
                                    <div class="flex flex-col gap-1">
                                        <span>{{ collect([$colony->location, $colony->city])->filter()->implode(', ') }}</span>
                                        {{-- Viewers never see people's data, so the caretaker's contact stays hidden from them. --}}
                                        @if ($canSeeCaretaker)
                                            @if ($colony->caretaker_name || $colony->caretaker_phone)
                                                <span class="text-neutral-500 dark:text-neutral-400">{{ collect([$colony->caretaker_name, $colony->caretaker_phone])->filter()->implode(' · ') }}</span>
                                            @endif
                                            @if ($colony->notes)
                                                <span class="line-clamp-2 whitespace-pre-line text-neutral-500 dark:text-neutral-400">{{ $colony->notes }}</span>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-3">{{ $colony->cats_count }}</td>
                                <td class="px-6 py-3">{{ $colony->neutered_count }} / {{ $colony->cats_count }}</td>
                                <td class="px-6 py-3">
                                    <div class="flex items-center gap-2">
                                        <flux:button size="sm" variant="subtle" icon="eye" :href="route('colonies.show', $colony)" wire:navigate :aria-label="__('View')" />

                                        @if ($canEdit)
                                            <flux:modal.trigger name="colony-form">
                                                <flux:button size="sm" variant="subtle" icon="pencil" wire:click="editColony({{ $colony->id }})" :aria-label="__('Edit')" />
                                            </flux:modal.trigger>

                                            <flux:modal.trigger name="confirm-colony-deletion-{{ $colony->id }}">
                                                <flux:button size="sm" variant="subtle" icon="trash" :aria-label="__('Delete')" />
                                            </flux:modal.trigger>

                                            <flux:modal name="confirm-colony-deletion-{{ $colony->id }}" class="max-w-lg">
                                                <div class="space-y-6">
                                                    <div>
                                                        <flux:heading size="lg">{{ __('Are you sure you want to delete this record?') }}</flux:heading>
                                                        <flux:subheading>{{ __('This record can be restored later by an administrator') }}</flux:subheading>
                                                        @if ($colony->cats_count > 0)
                                                            <flux:subheading class="mt-2">{{ __('Its animals stay registered without a colony and need a new location.') }}</flux:subheading>
                                                        @endif
                                                    </div>

                                                    <div class="flex justify-end space-x-2 rtl:space-x-reverse">
                                                        <flux:modal.close>
                                                            <flux:button variant="filled">{{ __('Cancel') }}</flux:button>
                                                        </flux:modal.close>

                                                        <flux:button variant="danger" wire:click="deleteColony({{ $colony->id }})">
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
                                <td colspan="6" class="px-6 py-6 text-center text-neutral-500 dark:text-neutral-400">
                                    {{ __('No colonies registered') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if ($canEdit)
        <flux:modal name="colony-form" class="max-w-lg">
            <form wire:submit="saveColony" class="flex flex-col gap-6">
                <flux:heading size="lg">
                    {{ $editingColonyId ? __('Edit') : __('Create') }} &mdash; {{ __('Colonies') }}
                </flux:heading>

                <flux:select wire:model="colonySpeciesId" :label="__('Species')" :placeholder="__('Species')">
                    @foreach ($this->colonySpecies as $item)
                        <flux:select.option value="{{ $item->id }}">{{ $item->name }}</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:input wire:model="colonyName" :label="__('Name')" />
                <flux:input wire:model="colonyLocation" :label="__('Location')" :description="__('Street, landmark or reference point')" />
                <flux:input wire:model="colonyCity" :label="__('City')" />

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <flux:input wire:model="colonyCaretakerName" :label="__('Caretaker')" />
                    <flux:input wire:model="colonyCaretakerPhone" :label="__('Phone')" />
                </div>

                <flux:textarea wire:model="colonyNotes" :label="__('Notes')" rows="3" />

                <flux:switch wire:model="colonyIsActive" :label="__('Active')" align="left" />

                <div class="flex justify-end gap-2">
                    <flux:modal.close>
                        <flux:button type="button" variant="filled">{{ __('Cancel') }}</flux:button>
                    </flux:modal.close>

                    <flux:button type="submit" variant="primary">
                        {{ $editingColonyId ? __('Save') : __('Create') }}
                    </flux:button>
                </div>
            </form>
        </flux:modal>
    @endif
</div>
