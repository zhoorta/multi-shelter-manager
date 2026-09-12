<div class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex items-center justify-between">
        <flux:heading size="xl">{{ __('Species') }}</flux:heading>

        <flux:modal.trigger name="species-form">
            <flux:button variant="primary" icon="plus" wire:click="createSpecies">
                {{ __('Create') }}
            </flux:button>
        </flux:modal.trigger>
    </div>

    <div class="flex flex-col gap-4 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
        <div class="overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-neutral-50 text-xs uppercase text-neutral-500 dark:bg-neutral-800 dark:text-neutral-400">
                        <tr>
                            <th scope="col" class="px-6 py-3 font-medium">{{ __('Name') }}</th>
                            <th scope="col" class="px-6 py-3 font-medium">{{ __('Plural Name') }}</th>
                            <th scope="col" class="px-6 py-3 font-medium">{{ __('Breeds') }}</th>
                            <th scope="col" class="px-6 py-3 font-medium">{{ __('Pure breeds') }}</th>
                            <th scope="col" class="px-6 py-3 font-medium">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                        @forelse ($this->species as $item)
                            <tr wire:key="species-{{ $item->id }}">
                                <td class="px-6 py-3 font-medium text-neutral-900 dark:text-white">{{ $item->name }}</td>
                                <td class="px-6 py-3 text-neutral-500 dark:text-neutral-400">{{ $item->name_plural }}</td>
                                <td class="px-6 py-3">
                                    <flux:badge size="sm">{{ $item->breeds_count }}</flux:badge>
                                </td>
                                <td class="px-6 py-3">
                                    <flux:badge size="sm" :color="$item->has_pure_breed_field ? 'lime' : 'zinc'">
                                        {{ $item->has_pure_breed_field ? __('Yes') : __('No') }}
                                    </flux:badge>
                                </td>
                                <td class="px-6 py-3">
                                    <div class="flex items-center gap-2">
                                        <flux:modal.trigger name="species-form">
                                            <flux:button
                                                size="sm"
                                                variant="subtle"
                                                icon="pencil"
                                                wire:click="editSpecies({{ $item->id }})"
                                                :aria-label="__('Edit')"
                                            />
                                        </flux:modal.trigger>

                                        <flux:modal.trigger name="confirm-species-deletion-{{ $item->id }}">
                                            <flux:button
                                                size="sm"
                                                variant="subtle"
                                                icon="trash"
                                                :aria-label="__('Delete')"
                                            />
                                        </flux:modal.trigger>

                                        <flux:modal name="confirm-species-deletion-{{ $item->id }}" class="max-w-lg">
                                            <div class="space-y-6">
                                                <div>
                                                    <flux:heading size="lg">{{ __('Are you sure you want to delete this record?') }}</flux:heading>
                                                    <flux:subheading>{{ __('This record can be restored later by an administrator') }}</flux:subheading>
                                                </div>

                                                <div class="flex justify-end space-x-2 rtl:space-x-reverse">
                                                    <flux:modal.close>
                                                        <flux:button variant="filled">{{ __('Cancel') }}</flux:button>
                                                    </flux:modal.close>

                                                    <flux:button variant="danger" wire:click="deleteSpecies({{ $item->id }})">
                                                        {{ __('Delete') }}
                                                    </flux:button>
                                                </div>
                                            </div>
                                        </flux:modal>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-6 text-center text-neutral-500 dark:text-neutral-400">
                                    {{ __('No species registered') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <flux:modal name="species-form" class="max-w-lg">
        <form wire:submit="saveSpecies" class="flex flex-col gap-6">
            <flux:heading size="lg">
                {{ $editingSpeciesId ? __('Edit') : __('Create') }} &mdash; {{ __('Species') }}
            </flux:heading>

            <flux:input wire:model="speciesName" :label="__('Name')" />

            <flux:input wire:model="speciesNamePlural" :label="__('Plural Name')" />

            <flux:switch wire:model="speciesHasPureBreedField" :label="__('Pure breeds')" align="left" />

            <div class="flex justify-end gap-2">
                <flux:modal.close>
                    <flux:button type="button" variant="filled">{{ __('Cancel') }}</flux:button>
                </flux:modal.close>

                <flux:button type="submit" variant="primary">
                    {{ $editingSpeciesId ? __('Save') : __('Create') }}
                </flux:button>
            </div>
        </form>
    </flux:modal>
</div>
