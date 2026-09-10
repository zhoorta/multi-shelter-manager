<div class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex items-center justify-between">
        <flux:heading size="xl">{{ __('Sicknesses') }}</flux:heading>

        <flux:modal.trigger name="sickness-form">
            <flux:button variant="primary" icon="plus" wire:click="createSickness">
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
                            <th scope="col" class="px-6 py-3 font-medium">{{ __('Description') }}</th>
                            <th scope="col" class="px-6 py-3 font-medium">{{ __('Species') }}</th>
                            <th scope="col" class="px-6 py-3 font-medium">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                        @forelse ($this->sicknesses as $sickness)
                            <tr wire:key="sickness-{{ $sickness->id }}">
                                <td class="px-6 py-3 font-medium text-neutral-900 dark:text-white">{{ $sickness->name }}</td>
                                <td class="max-w-xs truncate px-6 py-3 text-neutral-500 dark:text-neutral-400">
                                    {{ $sickness->description ?: '—' }}
                                </td>
                                <td class="px-6 py-3">
                                    <div class="flex flex-wrap gap-1">
                                        @forelse ($sickness->species as $sicknessSpecies)
                                            <flux:badge size="sm">{{ $sicknessSpecies->name }}</flux:badge>
                                        @empty
                                            <span class="text-neutral-400">&mdash;</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="px-6 py-3">
                                    <div class="flex items-center gap-2">
                                        <flux:modal.trigger name="sickness-form">
                                            <flux:button
                                                size="sm"
                                                variant="subtle"
                                                icon="pencil"
                                                wire:click="editSickness({{ $sickness->id }})"
                                                :aria-label="__('Edit')"
                                            />
                                        </flux:modal.trigger>

                                        <flux:modal.trigger name="confirm-sickness-deletion-{{ $sickness->id }}">
                                            <flux:button
                                                size="sm"
                                                variant="subtle"
                                                icon="trash"
                                                :aria-label="__('Delete')"
                                            />
                                        </flux:modal.trigger>

                                        <flux:modal name="confirm-sickness-deletion-{{ $sickness->id }}" class="max-w-lg">
                                            <div class="space-y-6">
                                                <div>
                                                    <flux:heading size="lg">{{ __('Are you sure you want to delete this record?') }}</flux:heading>
                                                    <flux:subheading>{{ __('This record can be restored later by an administrator') }}</flux:subheading>
                                                </div>

                                                <div class="flex justify-end space-x-2 rtl:space-x-reverse">
                                                    <flux:modal.close>
                                                        <flux:button variant="filled">{{ __('Cancel') }}</flux:button>
                                                    </flux:modal.close>

                                                    <flux:button variant="danger" wire:click="deleteSickness({{ $sickness->id }})">
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
                                <td colspan="4" class="px-6 py-6 text-center text-neutral-500 dark:text-neutral-400">
                                    {{ __('No sicknesses registered') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <flux:modal name="sickness-form" class="max-w-lg">
        <form wire:submit="saveSickness" class="flex flex-col gap-6">
            <flux:heading size="lg">
                {{ $editingSicknessId ? __('Edit') : __('Create') }} &mdash; {{ __('Sicknesses') }}
            </flux:heading>

            <flux:input wire:model="sicknessName" :label="__('Name')" />

            <flux:textarea wire:model="sicknessDescription" :label="__('Description')" rows="3" />

            <flux:checkbox.group wire:model="sicknessSpeciesIds" :label="__('Species')">
                @foreach ($this->species as $item)
                    <flux:checkbox value="{{ $item->id }}" label="{{ $item->name }}" />
                @endforeach
            </flux:checkbox.group>

            <div class="flex justify-end gap-2">
                <flux:modal.close>
                    <flux:button type="button" variant="filled">{{ __('Cancel') }}</flux:button>
                </flux:modal.close>

                <flux:button type="submit" variant="primary">
                    {{ $editingSicknessId ? __('Save') : __('Create') }}
                </flux:button>
            </div>
        </form>
    </flux:modal>
</div>
