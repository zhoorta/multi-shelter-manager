<div class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex items-center justify-between">
        <flux:heading size="xl">
            {{ $shelter ? __('Edit') : __('Create') }} &mdash; {{ __('Shelters') }}
        </flux:heading>

        <flux:button :href="route('admin.shelters.index')" variant="filled" icon="arrow-left" wire:navigate>
            {{ __('Shelters') }}
        </flux:button>
    </div>

    <form wire:submit="saveShelter" class="flex flex-col gap-6">
        <div class="flex flex-col gap-4 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <div class="grid grid-cols-3 gap-4">
                <flux:input wire:model="shelterName" :label="__('Name')" field:class="col-span-2" />
                <flux:input wire:model="shelterShortName" :label="__('Short Name')" />
            </div>

            <flux:field>
                <flux:label>{{ __('Public address') }}</flux:label>
                <flux:input.group>
                    <flux:input.group.prefix>{{ $publicPagePrefix }}</flux:input.group.prefix>
                    <flux:input wire:model="shelterSlug" :placeholder="__('Made from the name when left blank')" />
                </flux:input.group>
                <flux:description>{{ __('Changing it breaks links to the shelter page that were already shared.') }}</flux:description>
                <flux:error name="shelterSlug" />
            </flux:field>

            @include('livewire.partials.shelter-profile-fields')
        </div>

        <div class="flex flex-col gap-4 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <div class="flex flex-col gap-1">
                <flux:heading size="lg">{{ __('Species') }}</flux:heading>
                <flux:subheading>
                    {{ __('Controls which species appear on the sidebar for this shelter\'s manager and staff users.') }}
                </flux:subheading>
            </div>

            @foreach ($this->species as $item)
                <flux:switch
                    :checked="in_array($item->id, $shelterSpeciesIds, true)"
                    wire:click="toggleSpecies({{ $item->id }})"
                    :label="$item->name"
                    align="left"
                />
            @endforeach
        </div>

        <div class="flex flex-col gap-4 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            @include('livewire.partials.shelter-modules-fields')
        </div>

        <div class="flex justify-end gap-2">
            <flux:button :href="route('admin.shelters.index')" variant="filled" wire:navigate>
                {{ __('Cancel') }}
            </flux:button>

            <flux:button type="submit" variant="primary">
                {{ $shelter ? __('Save') : __('Create') }}
            </flux:button>
        </div>
    </form>
</div>
