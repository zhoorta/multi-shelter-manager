<div class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex items-center justify-between">
        <div class="flex flex-col gap-1">
            <flux:heading size="xl">{{ $pet->name }}</flux:heading>
            <flux:subheading>{{ __('Pets') }}</flux:subheading>
        </div>

        <div class="flex items-center gap-2">
            <flux:button :href="route('pets.index', ['speciesFilter' => $pet->species_id])" variant="filled" icon="arrow-left" wire:navigate>
                {{ $pet->species->name_plural }}
            </flux:button>

            <flux:button :href="route('pets.edit', $pet)" variant="primary" icon="pencil" wire:navigate>
                {{ __('Edit') }}
            </flux:button>
        </div>
    </div>

    @php
        $mainImage = $pet->images->firstWhere('is_main', true) ?? $pet->images->first();
        $otherImages = $pet->images->reject(fn ($image) => $image->is($mainImage));
    @endphp

    <div class="flex flex-col gap-8">
        <div class="flex flex-col gap-4 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <flux:label>{{ __('Photos') }}</flux:label>

            @if ($pet->images->isNotEmpty())
                <div class="grid grid-cols-3 gap-3 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8">
                    <img
                        src="{{ \Illuminate\Support\Facades\Storage::url($mainImage->image_path) }}"
                        alt="{{ $pet->name }}"
                        class="h-24 w-full rounded-lg object-cover ring-2 ring-neutral-900 dark:ring-white"
                    >

                    @foreach ($otherImages as $image)
                        <img
                            wire:key="pet-image-{{ $image->id }}"
                            src="{{ \Illuminate\Support\Facades\Storage::url($image->image_path) }}"
                            alt="{{ $pet->name }}"
                            class="h-24 w-full rounded-lg object-cover ring-1 ring-neutral-200 dark:ring-neutral-700"
                        >
                    @endforeach
                </div>
            @else
                <flux:text class="text-neutral-500 dark:text-neutral-400">{{ __('No photos uploaded') }}</flux:text>
            @endif
        </div>

        <div class="grid grid-cols-[max-content_1fr] items-baseline gap-x-2 gap-y-3 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <flux:text size="sm" class="text-neutral-500 dark:text-neutral-400">{{ __('Name') }}:</flux:text>
            <flux:text>{{ $pet->name }}</flux:text>

            <flux:text size="sm" class="text-neutral-500 dark:text-neutral-400">{{ __('Microchip / Chip') }}:</flux:text>
            <flux:text>{{ $pet->chip ?? '—' }}</flux:text>

            <flux:text size="sm" class="text-neutral-500 dark:text-neutral-400">{{ __('Species') }}:</flux:text>
            <flux:text>{{ $pet->species->name }}</flux:text>

            <flux:text size="sm" class="text-neutral-500 dark:text-neutral-400">{{ __('Breed') }}:</flux:text>
            <flux:text>{{ $pet->breed->name }}</flux:text>

            <flux:text size="sm" class="text-neutral-500 dark:text-neutral-400">{{ __('Gender') }}:</flux:text>
            <flux:text>{{ __(ucfirst($pet->gender)) }}</flux:text>
        </div>

        <div class="grid grid-cols-[max-content_1fr] items-baseline gap-x-2 gap-y-3 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <flux:text size="sm" class="text-neutral-500 dark:text-neutral-400">{{ __('Primary Color') }}:</flux:text>
            <flux:text>{{ $pet->primaryColor?->name ?? '—' }}</flux:text>

            <flux:text size="sm" class="text-neutral-500 dark:text-neutral-400">{{ __('Secondary Color') }}:</flux:text>
            <flux:text>{{ $pet->secondaryColor?->name ?? '—' }}</flux:text>

            <flux:text size="sm" class="text-neutral-500 dark:text-neutral-400">{{ __('Fur Type') }}:</flux:text>
            <flux:text>{{ $pet->furType?->name ?? '—' }}</flux:text>
        </div>

        <div class="grid grid-cols-[max-content_1fr] items-center gap-x-2 gap-y-3 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <flux:text size="sm" class="text-neutral-500 dark:text-neutral-400">{{ __('Is Neutered') }}:</flux:text>
            <flux:badge size="sm" :color="$pet->is_neutered ? 'lime' : 'zinc'">{{ $pet->is_neutered ? __('Yes') : __('No') }}</flux:badge>

            <flux:text size="sm" class="text-neutral-500 dark:text-neutral-400">{{ __('Is Adoptable') }}:</flux:text>
            <flux:badge size="sm" :color="$pet->is_adoptable ? 'lime' : 'zinc'">{{ $pet->is_adoptable ? __('Yes') : __('No') }}</flux:badge>

            <flux:text size="sm" class="text-neutral-500 dark:text-neutral-400">{{ __('Is Sponsorable') }}:</flux:text>
            <flux:badge size="sm" :color="$pet->is_sponsorable ? 'lime' : 'zinc'">{{ $pet->is_sponsorable ? __('Yes') : __('No') }}</flux:badge>
        </div>

        <div class="grid grid-cols-[max-content_1fr] items-center gap-x-2 gap-y-3 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <flux:text size="sm" class="text-neutral-500 dark:text-neutral-400">{{ __('Status') }}:</flux:text>
            <flux:badge size="sm">{{ __(ucfirst($pet->status)) }}</flux:badge>

            <flux:text size="sm" class="text-neutral-500 dark:text-neutral-400">{{ __('Cage') }}:</flux:text>
            <flux:text>{{ $pet->cage?->wing->name ?? __('No Wing Assigned') }} &middot; {{ $pet->cage->code ?? __('No Cage Assigned') }}</flux:text>
        </div>

        <div class="grid grid-cols-[max-content_1fr] items-baseline gap-x-2 gap-y-3 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <flux:text size="sm" class="text-neutral-500 dark:text-neutral-400">{{ __('Birth Date') }}:</flux:text>
            <flux:text>{{ $pet->birth_date?->format('d/m/Y') ?? '—' }}</flux:text>
        </div>
    </div>
</div>
