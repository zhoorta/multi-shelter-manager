<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading level="2" class="sr-only">{{ __('Export data') }}</flux:heading>

    <x-settings.layout :heading="__('Export data')" :subheading="__('Download a copy of all your shelter\'s data')">
        <div class="my-6 flex w-full flex-col gap-4">
            <flux:text>{{ __('The ZIP contains one CSV file per area: pets, vaccinations, treatments, diagnoses, adoptions, adoption applications, sponsorships and payments, members and payments, volunteers, and spaces.') }}</flux:text>

            <flux:callout icon="exclamation-triangle" variant="warning">
                <flux:callout.text>{{ __('The file contains personal data of members, volunteers, adopters and sponsors. Store it safely and do not share it.') }}</flux:callout.text>
            </flux:callout>

            <div>
                <flux:button wire:click="export" variant="primary" icon="arrow-down-tray" data-test="export-data-button">{{ __('Download export') }}</flux:button>
            </div>
        </div>
    </x-settings.layout>
</section>
