<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading level="2" class="sr-only">{{ __('Shelter settings') }}</flux:heading>

    <x-settings.layout :heading="__('Shelter')" :subheading="__('Update your shelter\'s contacts and public profile')">
        <form wire:submit="saveShelter" class="my-6 flex w-full flex-col gap-4">
            <div class="grid grid-cols-3 gap-4">
                <flux:field class="col-span-2">
                    <flux:label>{{ __('Name') }}</flux:label>
                    <flux:text>{{ $shelterName }}</flux:text>
                </flux:field>
                <flux:input wire:model="shelterShortName" :label="__('Short Name')" />
            </div>

            @include('livewire.partials.shelter-profile-fields')

            <div class="flex justify-end">
                <flux:button type="submit" variant="primary" data-test="save-shelter-button">{{ __('Save') }}</flux:button>
            </div>
        </form>
    </x-settings.layout>
</section>
