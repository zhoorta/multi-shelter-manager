<div class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex items-center justify-between">
        <flux:heading size="xl">{{ __('Wings') }} &amp; {{ __('Cages') }}</flux:heading>

        <flux:modal.trigger name="wing-form">
            <flux:button variant="primary" icon="plus" wire:click="createWing">
                {{ __('Create') }}
            </flux:button>
        </flux:modal.trigger>
    </div>

    @if ($this->wings->isEmpty())
        <div class="flex min-h-48 items-center justify-center rounded-xl border border-neutral-200 bg-white p-6 text-center text-neutral-500 shadow-sm dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-400">
            {{ __('No wings registered') }}
        </div>
    @else
        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
            @foreach ($this->wings as $wing)
                <div wire:key="wing-{{ $wing->id }}" class="flex flex-col gap-4 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex flex-col gap-1">
                            <flux:heading size="lg">{{ $wing->name }}</flux:heading>

                            @if ($wing->description)
                                <flux:text class="text-neutral-500 dark:text-neutral-400">{{ $wing->description }}</flux:text>
                            @endif
                        </div>

                        <div class="flex shrink-0 items-center gap-2">
                            <flux:modal.trigger name="wing-form">
                                <flux:button
                                    size="sm"
                                    variant="subtle"
                                    icon="pencil"
                                    wire:click="editWing({{ $wing->id }})"
                                    :aria-label="__('Edit')"
                                />
                            </flux:modal.trigger>

                            <flux:modal.trigger name="confirm-wing-deletion-{{ $wing->id }}">
                                <flux:button
                                    size="sm"
                                    variant="subtle"
                                    icon="trash"
                                    :aria-label="__('Delete')"
                                />
                            </flux:modal.trigger>

                            <flux:modal name="confirm-wing-deletion-{{ $wing->id }}" class="max-w-lg">
                                <div class="space-y-6">
                                    <div>
                                        <flux:heading size="lg">{{ __('Are you sure you want to delete this record?') }}</flux:heading>
                                        <flux:subheading>{{ __('This record can be restored later by an administrator') }}</flux:subheading>
                                    </div>

                                    <div class="flex justify-end space-x-2 rtl:space-x-reverse">
                                        <flux:modal.close>
                                            <flux:button variant="filled">{{ __('Cancel') }}</flux:button>
                                        </flux:modal.close>

                                        <flux:button variant="danger" wire:click="deleteWing({{ $wing->id }})">
                                            {{ __('Delete') }}
                                        </flux:button>
                                    </div>
                                </div>
                            </flux:modal>
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <flux:subheading>{{ __('Cages') }}</flux:subheading>

                        <flux:modal.trigger name="cage-form">
                            <flux:button
                                size="sm"
                                variant="subtle"
                                icon="plus"
                                wire:click="createCage({{ $wing->id }})"
                            >
                                {{ __('Add Cage') }}
                            </flux:button>
                        </flux:modal.trigger>
                    </div>

                    <ul class="divide-y divide-neutral-200 overflow-hidden rounded-lg border border-neutral-200 dark:divide-neutral-700 dark:border-neutral-700">
                        @forelse ($wing->cages as $cage)
                            <li wire:key="cage-{{ $cage->id }}" class="flex items-center justify-between gap-2 px-4 py-2 text-sm">
                                <span class="font-medium text-neutral-900 dark:text-white">{{ $cage->code }}</span>

                                <div class="flex items-center gap-2">
                                    <flux:badge size="sm">{{ __('Capacity') }}: {{ $cage->capacity }}</flux:badge>

                                    <flux:modal.trigger name="cage-form">
                                        <flux:button
                                            size="sm"
                                            variant="subtle"
                                            icon="pencil"
                                            wire:click="editCage({{ $cage->id }})"
                                            :aria-label="__('Edit')"
                                        />
                                    </flux:modal.trigger>

                                    <flux:modal.trigger name="confirm-cage-deletion-{{ $cage->id }}">
                                        <flux:button
                                            size="sm"
                                            variant="subtle"
                                            icon="trash"
                                            :aria-label="__('Delete')"
                                        />
                                    </flux:modal.trigger>

                                    <flux:modal name="confirm-cage-deletion-{{ $cage->id }}" class="max-w-lg">
                                        <div class="space-y-6">
                                            <div>
                                                <flux:heading size="lg">{{ __('Are you sure you want to delete this record?') }}</flux:heading>
                                                <flux:subheading>{{ __('This record can be restored later by an administrator') }}</flux:subheading>
                                            </div>

                                            <div class="flex justify-end space-x-2 rtl:space-x-reverse">
                                                <flux:modal.close>
                                                    <flux:button variant="filled">{{ __('Cancel') }}</flux:button>
                                                </flux:modal.close>

                                                <flux:button variant="danger" wire:click="deleteCage({{ $cage->id }})">
                                                    {{ __('Delete') }}
                                                </flux:button>
                                            </div>
                                        </div>
                                    </flux:modal>
                                </div>
                            </li>
                        @empty
                            <li class="px-4 py-6 text-center text-sm text-neutral-500 dark:text-neutral-400">
                                {{ __('No cages registered for this wing') }}
                            </li>
                        @endforelse
                    </ul>
                </div>
            @endforeach
        </div>
    @endif

    <flux:modal name="wing-form" class="max-w-lg">
        <form wire:submit="saveWing" class="flex flex-col gap-6">
            <flux:heading size="lg">
                {{ $editingWingId ? __('Edit') : __('Create') }} &mdash; {{ __('Wing') }}
            </flux:heading>

            <flux:input wire:model="wingName" :label="__('Name')" />

            <flux:textarea wire:model="wingDescription" :label="__('Notes')" rows="3" />

            <div class="flex justify-end gap-2">
                <flux:modal.close>
                    <flux:button type="button" variant="filled">{{ __('Cancel') }}</flux:button>
                </flux:modal.close>

                <flux:button type="submit" variant="primary">
                    {{ $editingWingId ? __('Save') : __('Create') }}
                </flux:button>
            </div>
        </form>
    </flux:modal>

    <flux:modal name="cage-form" class="max-w-lg">
        <form wire:submit="saveCage" class="flex flex-col gap-6">
            <div class="flex flex-col gap-1">
                <flux:heading size="lg">
                    {{ $editingCageId ? __('Edit') : __('Create') }} &mdash; {{ __('Cage') }}
                </flux:heading>

                <flux:text class="text-neutral-500 dark:text-neutral-400">
                    {{ __('Wing') }}: <span class="font-medium text-neutral-900 dark:text-white">{{ $this->wings->firstWhere('id', $cageWingId)?->name }}</span>
                </flux:text>
            </div>

            <flux:input wire:model="cageCode" :label="__('Code')" />

            <flux:input type="number" min="1" wire:model="cageCapacity" :label="__('Capacity')" />

            <div class="flex justify-end gap-2">
                <flux:modal.close>
                    <flux:button type="button" variant="filled">{{ __('Cancel') }}</flux:button>
                </flux:modal.close>

                <flux:button type="submit" variant="primary">
                    {{ $editingCageId ? __('Save') : __('Create') }}
                </flux:button>
            </div>
        </form>
    </flux:modal>
</div>
