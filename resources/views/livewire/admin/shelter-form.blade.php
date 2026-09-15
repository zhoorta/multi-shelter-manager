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

            <div class="grid grid-cols-2 gap-4">
                <flux:input wire:model="shelterEmail" type="email" :label="__('Email')" />
                <flux:input wire:model="shelterPhone" :label="__('Phone')" />
            </div>

            <flux:input wire:model="shelterWebsite" :label="__('Website')" />

            <flux:input wire:model="shelterAddress" :label="__('Address')" />

            <div class="grid grid-cols-3 gap-4">
                <flux:input wire:model="shelterPostalCode" :label="__('Postal Code')" />
                <flux:input wire:model="shelterCity" :label="__('City')" field:class="col-span-2" />
            </div>

            <flux:textarea wire:model="shelterDescription" :label="__('Description')" />

            <flux:field>
                <flux:label>{{ __('Logo') }}</flux:label>

                @if ($shelterLogo)
                    <img
                        src="{{ $shelterLogo->temporaryUrl() }}"
                        alt="{{ __('Logo') }}"
                        class="h-16 w-16 rounded-lg object-cover ring-1 ring-neutral-200 dark:ring-neutral-700"
                    >
                @elseif ($existingLogoPath)
                    <img
                        src="{{ \Illuminate\Support\Facades\Storage::url($existingLogoPath) }}"
                        alt="{{ __('Logo') }}"
                        class="h-16 w-16 rounded-lg object-cover ring-1 ring-neutral-200 dark:ring-neutral-700"
                    >
                @endif

                <input type="file" wire:model="shelterLogo" accept="image/*">

                <flux:text size="sm" class="text-neutral-500 dark:text-neutral-400">
                    {{ __('PNG or JPG up to 2MB') }}
                </flux:text>

                <div wire:loading wire:target="shelterLogo">
                    <flux:text size="sm">{{ __('Uploading') }}&hellip;</flux:text>
                </div>

                <flux:error name="shelterLogo" />
            </flux:field>
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
