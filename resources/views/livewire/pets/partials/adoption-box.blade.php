{{--
    Renders one adoption record's details plus an Edit link. Expects $pet
    and $adoption, and optionally $backToAdoptionsList (bool, default false)
    to send the edit form back to the adoptions list instead of the pet page.
    Included by pet-show.blade.php, looping over every adoption for a pet
    whose status is 'adopted', and by adoption-show.blade.php with
    $backToAdoptionsList set (see .ai/rules/pets.md — mirrors the
    sponsorship-box partial).
--}}
<div wire:key="adoption-{{ $adoption->id }}" class="grid grid-cols-[max-content_1fr] items-start justify-items-start gap-x-2 gap-y-3 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
    <div class="col-span-2 flex w-full items-center justify-between">
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

    <flux:text size="sm" class="text-neutral-500 dark:text-neutral-400">{{ __('Name') }}:</flux:text>
    <flux:text>{{ $adoption->name ?? '—' }}</flux:text>

    <flux:text size="sm" class="text-neutral-500 dark:text-neutral-400">{{ __('Email') }}:</flux:text>
    <flux:text>{{ $adoption->email ?? '—' }}</flux:text>

    <flux:text size="sm" class="text-neutral-500 dark:text-neutral-400">{{ __('Phone') }}:</flux:text>
    <flux:text>{{ $adoption->phone ?? '—' }}</flux:text>

    <flux:text size="sm" class="text-neutral-500 dark:text-neutral-400">{{ __('Address') }}:</flux:text>
    <flux:text>{{ $adoption->address ?? '—' }}</flux:text>

    <flux:text size="sm" class="text-neutral-500 dark:text-neutral-400">{{ __('Postal Code') }}:</flux:text>
    <flux:text>{{ $adoption->postal_code ?? '—' }}</flux:text>

    <flux:text size="sm" class="text-neutral-500 dark:text-neutral-400">{{ __('City') }}:</flux:text>
    <flux:text>{{ $adoption->city ?? '—' }}</flux:text>

    <flux:text size="sm" class="text-neutral-500 dark:text-neutral-400">{{ __('Adoption Date') }}:</flux:text>
    <flux:text>{{ $adoption->adoption_date->format('d/m/Y') }}</flux:text>

    <flux:text size="sm" class="text-neutral-500 dark:text-neutral-400">{{ __('Return Date') }}:</flux:text>
    <flux:text>{{ $adoption->return_date?->format('d/m/Y') ?? '—' }}</flux:text>

    <flux:text size="sm" class="text-neutral-500 dark:text-neutral-400">{{ __('Adoption Fee') }}:</flux:text>
    <flux:text>{{ number_format((float) $adoption->adoption_fee, 2, ',', '.') }}</flux:text>

    <flux:text size="sm" class="text-neutral-500 dark:text-neutral-400">{{ __('Application Status') }}:</flux:text>
    <flux:badge size="sm">{{ __($adoption->application_status) }}</flux:badge>

    <flux:text size="sm" class="text-neutral-500 dark:text-neutral-400">{{ __('Notes') }}:</flux:text>
    <flux:text>{{ $adoption->notes ?? '—' }}</flux:text>
</div>
