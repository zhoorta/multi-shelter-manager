<div class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex items-center justify-between">
        <flux:heading size="xl">{{ __('Facilities') }}</flux:heading>

        @if (auth()->user()->isManagerOfCurrentShelter())
            <flux:modal.trigger name="facility-form">
                <flux:button variant="primary" icon="plus" wire:click="createFacility">
                    {{ __('Create') }}
                </flux:button>
            </flux:modal.trigger>
        @endif
    </div>

    @if ($this->facilities->isEmpty())
        <div class="flex min-h-48 items-center justify-center rounded-xl border border-neutral-200 bg-white p-6 text-center text-neutral-500 shadow-sm dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-400">
            {{ __('No facilities registered') }}
        </div>
    @else
        <div class="flex flex-col gap-6">
            @foreach ($this->facilities as $facility)
                <div wire:key="facility-{{ $facility->id }}" class="flex flex-col gap-4 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex flex-col gap-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <flux:heading size="lg">{{ $facility->name }}</flux:heading>

                                @php($facilityCages = $facility->wings->flatMap->cages)
                                @if ($facilityCages->isNotEmpty())
                                    <flux:badge size="sm">{{ __(':available of :capacity free', ['available' => $facilityCages->sum('available_space'), 'capacity' => $facilityCages->sum('capacity')]) }}</flux:badge>
                                @endif
                            </div>

                            @if ($facility->address || $facility->postal_code || $facility->city)
                                <flux:text class="text-neutral-500 dark:text-neutral-400">
                                    {{ collect([$facility->address, $facility->postal_code, $facility->city])->filter()->implode(', ') }}
                                </flux:text>
                            @endif

                            @if ($facility->notes)
                                <flux:text class="text-neutral-500 dark:text-neutral-400">{{ $facility->notes }}</flux:text>
                            @endif
                        </div>

                        @if (auth()->user()->isManagerOfCurrentShelter())
                            <div class="flex shrink-0 items-center gap-2">
                                <flux:modal.trigger name="facility-form">
                                    <flux:button
                                        size="sm"
                                        variant="subtle"
                                        icon="pencil"
                                        wire:click="editFacility({{ $facility->id }})"
                                        :aria-label="__('Edit')"
                                    />
                                </flux:modal.trigger>

                                <flux:modal.trigger name="confirm-facility-deletion-{{ $facility->id }}">
                                    <flux:button
                                        size="sm"
                                        variant="subtle"
                                        icon="trash"
                                        :aria-label="__('Delete')"
                                    />
                                </flux:modal.trigger>

                                <flux:modal name="confirm-facility-deletion-{{ $facility->id }}" class="max-w-lg">
                                    <div class="space-y-6">
                                        <div>
                                            <flux:heading size="lg">{{ __('Are you sure you want to delete this record?') }}</flux:heading>
                                            <flux:subheading>{{ __('This record can be restored later by an administrator') }}</flux:subheading>
                                        </div>

                                        <div class="flex justify-end space-x-2 rtl:space-x-reverse">
                                            <flux:modal.close>
                                                <flux:button variant="filled">{{ __('Cancel') }}</flux:button>
                                            </flux:modal.close>

                                            <flux:button variant="danger" wire:click="deleteFacility({{ $facility->id }})">
                                                {{ __('Delete') }}
                                            </flux:button>
                                        </div>
                                    </div>
                                </flux:modal>
                            </div>
                        @endif
                    </div>

                    <div class="flex items-center justify-between">
                        <flux:subheading>{{ __('Wings') }}</flux:subheading>

                        @if (auth()->user()->isManagerOfCurrentShelter())
                            <flux:modal.trigger name="wing-form">
                                <flux:button
                                    size="sm"
                                    variant="subtle"
                                    icon="plus"
                                    wire:click="createWing({{ $facility->id }})"
                                >
                                    {{ __('Add Wing') }}
                                </flux:button>
                            </flux:modal.trigger>
                        @endif
                    </div>

                    @if ($facility->wings->isEmpty())
                        <div class="flex min-h-24 items-center justify-center rounded-lg border border-neutral-200 p-4 text-center text-sm text-neutral-500 dark:border-neutral-700 dark:text-neutral-400">
                            {{ __('No wings registered') }}
                        </div>
                    @else
                        <div class="grid gap-4 lg:grid-cols-2">
                            @foreach ($facility->wings as $wing)
                                <div wire:key="wing-{{ $wing->id }}" class="flex flex-col gap-3 rounded-lg border border-neutral-200 p-4 dark:border-neutral-700">
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="flex flex-col gap-1">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <flux:heading size="sm">{{ $wing->name }}</flux:heading>

                                                @if ($wing->cages->isNotEmpty())
                                                    <flux:badge size="sm">{{ __(':available of :capacity free', ['available' => $wing->cages->sum('available_space'), 'capacity' => $wing->cages->sum('capacity')]) }}</flux:badge>
                                                @endif
                                            </div>

                                            @if ($wing->description)
                                                <flux:text class="text-neutral-500 dark:text-neutral-400">{{ $wing->description }}</flux:text>
                                            @endif
                                        </div>

                                        @if (auth()->user()->isManagerOfCurrentShelter())
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
                                        @endif
                                    </div>

                                    <div class="flex items-center justify-between">
                                        <flux:subheading>{{ __('Cages') }}</flux:subheading>

                                        @if (auth()->user()->isManagerOfCurrentShelter())
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
                                        @endif
                                    </div>

                                    <ul class="divide-y divide-neutral-200 overflow-hidden rounded-lg border border-neutral-200 dark:divide-neutral-700 dark:border-neutral-700">
                                        @forelse ($wing->cages as $cage)
                                            <li wire:key="cage-{{ $cage->id }}" class="flex items-center justify-between gap-2 px-4 py-2 text-sm">
                                                <span class="font-medium text-neutral-900 dark:text-white">{{ $cage->code }}</span>

                                                <div class="flex items-center gap-2">
                                                    <flux:badge size="sm" :color="$cage->availability_color">{{ __(':available of :capacity free', ['available' => $cage->available_space, 'capacity' => $cage->capacity]) }}</flux:badge>

                                                    <flux:modal.trigger name="cage-pets">
                                                        <flux:button
                                                            size="sm"
                                                            variant="subtle"
                                                            icon="eye"
                                                            wire:click="viewCagePets({{ $cage->id }})"
                                                            :aria-label="__('View pets')"
                                                        />
                                                    </flux:modal.trigger>

                                                    @if (auth()->user()->isManagerOfCurrentShelter())
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
                                                    @endif
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
                </div>
            @endforeach
        </div>
    @endif

    <flux:modal name="cage-pets" class="w-full max-w-lg">
        <div class="flex flex-col gap-6">
            <div class="flex flex-col gap-1">
                <flux:heading size="lg">{{ __('Cage') }} {{ $this->viewingCage?->code }}</flux:heading>

                @if ($this->viewingCage)
                    <flux:text class="text-neutral-500 dark:text-neutral-400">
                        {{ __('Wing') }}: <span class="font-medium text-neutral-900 dark:text-white">{{ $this->viewingCage->wing->name }}</span>
                        &middot;
                        {{ __(':available of :capacity free', ['available' => max(0, $this->viewingCage->capacity - $this->viewingCage->pets->count()), 'capacity' => $this->viewingCage->capacity]) }}
                    </flux:text>
                @endif
            </div>

            <ul class="divide-y divide-neutral-200 overflow-hidden rounded-lg border border-neutral-200 dark:divide-neutral-700 dark:border-neutral-700">
                @forelse ($this->viewingCage?->pets ?? [] as $pet)
                    <li wire:key="cage-pet-{{ $pet->id }}" class="flex items-center justify-between gap-2 px-4 py-2 text-sm">
                        <div class="flex flex-col">
                            <a href="{{ route('pets.show', $pet) }}" wire:navigate class="font-medium text-neutral-900 hover:underline dark:text-white">{{ $pet->name }}</a>
                            <span class="text-neutral-500 dark:text-neutral-400">{{ $pet->ref }} &middot; {{ $pet->species->name }}</span>
                        </div>

                        <flux:button size="sm" variant="subtle" icon="eye" :href="route('pets.show', $pet)" wire:navigate :aria-label="__('View')" />
                    </li>
                @empty
                    <li class="px-4 py-6 text-center text-sm text-neutral-500 dark:text-neutral-400">
                        {{ __('No pets in this cage') }}
                    </li>
                @endforelse
            </ul>

            <div class="flex justify-end">
                <flux:modal.close>
                    <flux:button variant="filled">{{ __('Close') }}</flux:button>
                </flux:modal.close>
            </div>
        </div>
    </flux:modal>

    @if (auth()->user()->isManagerOfCurrentShelter())
    <flux:modal name="facility-form" class="max-w-lg">
        <form wire:submit="saveFacility" class="flex flex-col gap-6">
            <flux:heading size="lg">
                {{ $editingFacilityId ? __('Edit') : __('Create') }} &mdash; {{ __('Facility') }}
            </flux:heading>

            <flux:input wire:model="facilityName" :label="__('Name')" />

            <flux:input wire:model="facilityAddress" :label="__('Address')" />

            <div class="grid grid-cols-2 gap-4">
                <flux:input wire:model="facilityPostalCode" :label="__('Postal Code')" />
                <flux:input wire:model="facilityCity" :label="__('City')" />
            </div>

            <flux:textarea wire:model="facilityNotes" :label="__('Notes')" rows="3" />

            <div class="flex justify-end gap-2">
                <flux:modal.close>
                    <flux:button type="button" variant="filled">{{ __('Cancel') }}</flux:button>
                </flux:modal.close>

                <flux:button type="submit" variant="primary">
                    {{ $editingFacilityId ? __('Save') : __('Create') }}
                </flux:button>
            </div>
        </form>
    </flux:modal>

    <flux:modal name="wing-form" class="max-w-lg">
        <form wire:submit="saveWing" class="flex flex-col gap-6">
            <div class="flex flex-col gap-1">
                <flux:heading size="lg">
                    {{ $editingWingId ? __('Edit') : __('Create') }} &mdash; {{ __('Wing') }}
                </flux:heading>

                <flux:text class="text-neutral-500 dark:text-neutral-400">
                    {{ __('Facility') }}: <span class="font-medium text-neutral-900 dark:text-white">{{ $this->facilities->firstWhere('id', $wingFacilityId)?->name }}</span>
                </flux:text>
            </div>

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
                    {{ __('Wing') }}: <span class="font-medium text-neutral-900 dark:text-white">{{ $this->facilities->flatMap->wings->firstWhere('id', $cageWingId)?->name }}</span>
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
    @endif
</div>
