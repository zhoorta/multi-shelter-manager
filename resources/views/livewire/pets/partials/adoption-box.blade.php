{{--
    Renders one adoption record's details plus an Edit link. Expects $pet
    and $adoption, and optionally $backToAdoptionsList (bool, default false)
    to send the edit form back to the adoptions list instead of the pet page.
    Included by pet-show.blade.php, looping over every adoption for a pet
    whose status is 'adopted', and by adoption-show.blade.php with
    $backToAdoptionsList set (see .ai/rules/pets.md — mirrors the
    sponsorship-box partial).
--}}
<div wire:key="adoption-{{ $adoption->id }}" class="flex flex-col gap-4 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
    <div class="flex w-full items-center justify-between">
        <flux:label>{{ __('Adoption') }}</flux:label>

        <flux:button
            :href="route('pets.adopt.edit', ($backToAdoptionsList ?? false)
                ? ['pet' => $pet, 'adoption' => $adoption, 'from' => 'adoptions']
                : ['pet' => $pet, 'adoption' => $adoption])"
            variant="filled"
            size="sm"
            icon="pencil"
            wire:navigate
        >
            {{ __('Edit') }}
        </flux:button>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <div>
            <flux:text class="text-neutral-500 dark:text-neutral-400">{{ __('Name') }}</flux:text>
            <flux:text class="text-neutral-700 dark:text-neutral-300">{{ $adoption->name ?? '—' }}</flux:text>
        </div>

        <div>
            <flux:text class="text-neutral-500 dark:text-neutral-400">{{ __('Email') }}</flux:text>
            <flux:text class="text-neutral-700 dark:text-neutral-300">{{ $adoption->email ?? '—' }}</flux:text>
        </div>

        <div>
            <flux:text class="text-neutral-500 dark:text-neutral-400">{{ __('Phone') }}</flux:text>
            <flux:text class="text-neutral-700 dark:text-neutral-300">{{ $adoption->phone ?? '—' }}</flux:text>
        </div>

        <div>
            <flux:text class="text-neutral-500 dark:text-neutral-400">{{ __('Address') }}</flux:text>
            <flux:text class="text-neutral-700 dark:text-neutral-300">{{ $adoption->address ?? '—' }}</flux:text>
        </div>

        <div>
            <flux:text class="text-neutral-500 dark:text-neutral-400">{{ __('Postal Code') }}</flux:text>
            <flux:text class="text-neutral-700 dark:text-neutral-300">{{ $adoption->postal_code ?? '—' }}</flux:text>
        </div>

        <div>
            <flux:text class="text-neutral-500 dark:text-neutral-400">{{ __('City') }}</flux:text>
            <flux:text class="text-neutral-700 dark:text-neutral-300">{{ $adoption->city ?? '—' }}</flux:text>
        </div>

        <div>
            <flux:text class="text-neutral-500 dark:text-neutral-400">{{ __('Adoption Date') }}</flux:text>
            <flux:text class="text-neutral-700 dark:text-neutral-300">{{ $adoption->adoption_date->format('d/m/Y') }}</flux:text>
        </div>

        <div>
            <flux:text class="text-neutral-500 dark:text-neutral-400">{{ __('Return Date') }}</flux:text>
            <flux:text class="text-neutral-700 dark:text-neutral-300">{{ $adoption->return_date?->format('d/m/Y') ?? '—' }}</flux:text>
        </div>

        <div>
            <flux:text class="text-neutral-500 dark:text-neutral-400">{{ __('Adoption Fee') }}</flux:text>
            <flux:text class="text-neutral-700 dark:text-neutral-300">{{ number_format((float) $adoption->adoption_fee, 2, ',', '.') }}</flux:text>
        </div>

        <div>
            <flux:text class="text-neutral-500 dark:text-neutral-400">{{ __('Application Status') }}</flux:text>
            <flux:badge size="sm">{{ __($adoption->application_status) }}</flux:badge>
        </div>
    </div>

    <div>
        <flux:text class="text-neutral-500 dark:text-neutral-400">{{ __('Notes') }}</flux:text>
        <flux:text class="text-neutral-700 dark:text-neutral-300 whitespace-pre-line">{{ $adoption->notes ?? '—' }}</flux:text>
    </div>
</div>
