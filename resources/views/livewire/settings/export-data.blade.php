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

            <flux:separator class="my-2" />

            <flux:heading>{{ __('Recent exports') }}</flux:heading>

            <div class="overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <table class="w-full text-left text-sm">
                    <thead class="bg-neutral-50 text-xs uppercase text-neutral-500 dark:bg-neutral-800 dark:text-neutral-400">
                        <tr>
                            <th scope="col" class="px-4 py-3 font-medium">{{ __('Date') }}</th>
                            <th scope="col" class="px-4 py-3 font-medium">{{ __('User') }}</th>
                            <th scope="col" class="px-4 py-3 font-medium">{{ __('IP address') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                        @forelse ($this->recentExports as $export)
                            <tr wire:key="export-{{ $export->id }}">
                                <td class="px-4 py-3">{{ $export->created_at->format('d/m/Y H:i') }}</td>
                                <td class="px-4 py-3">{{ $export->user?->name ?? '—' }}</td>
                                <td class="px-4 py-3">{{ $export->ip_address ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-6 text-center text-neutral-500 dark:text-neutral-400">{{ __('No exports yet') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </x-settings.layout>
</section>
