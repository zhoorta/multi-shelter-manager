<div class="flex h-full w-full flex-1 flex-col gap-6">
    @if (! auth()->user()->is_admin && (! $hasCages || ! $speciesConfigured || $this->speciesWithoutBreeds->isNotEmpty()))
        <div class="flex flex-col gap-3">
            @if (! $hasCages && ! auth()->user()->isManagerOfCurrentShelter())
                <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800 dark:border-amber-900/50 dark:bg-amber-950/40 dark:text-amber-200">
                    {{ __('No facilities defined. Contact the shelter manager to configure the Facilities, Wings and Cages.') }}
                </div>
            @elseif (! $hasCages)
                <a
                    href="{{ route('facilities.index') }}"
                    wire:navigate
                    class="block rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800 hover:bg-amber-100 dark:border-amber-900/50 dark:bg-amber-950/40 dark:text-amber-200 dark:hover:bg-amber-950/60"
                >
                    {{ __('No facilities defined. Please configure the Facilities, Wings and Cages on the Facilities option.') }}
                </a>
            @endif

            @if (! $speciesConfigured)
                <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800 dark:border-amber-900/50 dark:bg-amber-950/40 dark:text-amber-200">
                    {{ __('No pet species defined for the shelter. Please contact the site administrator to configure the shelter species.') }}
                </div>
            @endif

            @foreach ($this->speciesWithoutBreeds as $speciesName)
                <div wire:key="species-without-breeds-{{ $speciesName }}" class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800 dark:border-amber-900/50 dark:bg-amber-950/40 dark:text-amber-200">
                    {{ __('No breeds of :species exist. Please contact the site administrator to configure the breeds.', ['species' => $speciesName]) }}
                </div>
            @endforeach
        </div>
    @endif

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="flex flex-col gap-2 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <span class="text-sm font-medium text-neutral-500 dark:text-neutral-400">{{ __('Total Pets') }}</span>
            <span class="text-3xl font-semibold text-neutral-900 dark:text-white">{{ $activePetsCount }}</span>
        </div>

        <div class="flex flex-col gap-2 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <span class="text-sm font-medium text-neutral-500 dark:text-neutral-400">{{ __('Adoptions') }}</span>
            <span class="text-3xl font-semibold text-neutral-900 dark:text-white">{{ $adoptionsPetsCount }}</span>
        </div>

        <div class="flex flex-col gap-2 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <span class="text-sm font-medium text-neutral-500 dark:text-neutral-400">{{ __('Available Capacity') }}</span>
            <span class="text-3xl font-semibold text-neutral-900 dark:text-white">{{ $availableCapacity }}</span>
        </div>

        <div class="flex flex-col gap-2 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <span class="text-sm font-medium text-neutral-500 dark:text-neutral-400">{{ __('Total Staff') }}</span>
            <span class="text-3xl font-semibold text-neutral-900 dark:text-white">{{ $staffCount }}</span>
        </div>
    </div>

    <div class="overflow-hidden rounded-xl border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-neutral-900">
        <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
            <h2 class="text-base font-semibold text-neutral-900 dark:text-white">{{ __('Pets with Unknown Location') }}</h2>
        </div>

        <div class="p-6">
            @if ($unknownLocationPets->isEmpty())
                <p class="text-center text-sm text-neutral-500 dark:text-neutral-400">{{ __('No Pets with Unknown Location') }}</p>
            @else
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5">
                    @foreach ($unknownLocationPets as $pet)
                        @php
                            $mainImage = $pet->images->firstWhere('is_main', true) ?? $pet->images->first();
                        @endphp
                        <a
                            wire:key="unknown-location-{{ $pet->id }}"
                            href="{{ route('pets.show', $pet) }}"
                            wire:navigate
                            class="flex flex-col items-center gap-2 rounded-lg p-3 text-center hover:bg-neutral-50 dark:hover:bg-neutral-800"
                        >
                            <span class="text-xs text-neutral-500 dark:text-neutral-400">{{ $pet->species->name }} - {{ $pet->ref }}</span>

                            <flux:avatar
                                size="xl"
                                class="size-20"
                                :src="$mainImage ? \Illuminate\Support\Facades\Storage::url($mainImage->image_path) : null"
                                :name="$pet->name"
                            />

                            <span class="text-sm font-medium text-neutral-900 hover:underline dark:text-white">{{ $pet->name }}</span>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <div class="overflow-hidden rounded-xl border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-neutral-900">
        <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
            <h2 class="text-base font-semibold text-neutral-900 dark:text-white">{{ __('Recent Adoptions') }}</h2>
        </div>

        <div class="p-6">
            @if ($recentAdoptions->isEmpty())
                <p class="text-center text-sm text-neutral-500 dark:text-neutral-400">{{ __('No Recent Adoptions') }}</p>
            @else
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5">
                    @foreach ($recentAdoptions as $adoption)
                        @php
                            $pet = $adoption->pet;
                            $mainImage = $pet->images->firstWhere('is_main', true) ?? $pet->images->first();
                        @endphp
                        <a
                            wire:key="recent-adoption-{{ $adoption->id }}"
                            href="{{ route('pets.show', $pet) }}"
                            wire:navigate
                            class="flex flex-col items-center gap-2 rounded-lg p-3 text-center hover:bg-neutral-50 dark:hover:bg-neutral-800"
                        >
                            <span class="text-xs text-neutral-500 dark:text-neutral-400">{{ $pet->species->name }} - {{ $pet->ref }}</span>

                            <flux:avatar
                                size="xl"
                                class="size-20"
                                :src="$mainImage ? \Illuminate\Support\Facades\Storage::url($mainImage->image_path) : null"
                                :name="$pet->name"
                            />

                            <span class="text-sm font-medium text-neutral-900 hover:underline dark:text-white">{{ $pet->name }}</span>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <div class="overflow-hidden rounded-xl border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-neutral-900">
        <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
            <h2 class="text-base font-semibold text-neutral-900 dark:text-white">{{ __('Recent Passings') }}</h2>
        </div>

        <div class="p-6">
            @if ($recentPassings->isEmpty())
                <p class="text-center text-sm text-neutral-500 dark:text-neutral-400">{{ __('No Recent Passings') }}</p>
            @else
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5">
                    @foreach ($recentPassings as $pet)
                        @php
                            $mainImage = $pet->images->firstWhere('is_main', true) ?? $pet->images->first();
                        @endphp
                        <a
                            wire:key="recent-passing-{{ $pet->id }}"
                            href="{{ route('pets.show', $pet) }}"
                            wire:navigate
                            class="flex flex-col items-center gap-2 rounded-lg p-3 text-center hover:bg-neutral-50 dark:hover:bg-neutral-800"
                        >
                            <span class="text-xs text-neutral-500 dark:text-neutral-400">{{ $pet->species->name }} - {{ $pet->ref }}</span>

                            <flux:avatar
                                size="xl"
                                class="size-20"
                                :src="$mainImage ? \Illuminate\Support\Facades\Storage::url($mainImage->image_path) : null"
                                :name="$pet->name"
                            />

                            <span class="text-sm font-medium text-neutral-900 hover:underline dark:text-white">{{ $pet->name }}</span>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <div class="overflow-hidden rounded-xl border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-neutral-900">
        <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
            <h2 class="text-base font-semibold text-neutral-900 dark:text-white">{{ __('Recent Sponsorships') }}</h2>
        </div>

        <div class="p-6">
            @if ($recentSponsorships->isEmpty())
                <p class="text-center text-sm text-neutral-500 dark:text-neutral-400">{{ __('No Recent Sponsorships') }}</p>
            @else
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5">
                    @foreach ($recentSponsorships as $sponsorship)
                        @php
                            $pet = $sponsorship->pet;
                            $mainImage = $pet->images->firstWhere('is_main', true) ?? $pet->images->first();
                        @endphp
                        <a
                            wire:key="recent-sponsorship-{{ $sponsorship->id }}"
                            href="{{ route('pets.show', $pet) }}"
                            wire:navigate
                            class="flex flex-col items-center gap-2 rounded-lg p-3 text-center hover:bg-neutral-50 dark:hover:bg-neutral-800"
                        >
                            <span class="text-xs text-neutral-500 dark:text-neutral-400">{{ $pet->species->name }} - {{ $pet->ref }}</span>

                            <flux:avatar
                                size="xl"
                                class="size-20"
                                :src="$mainImage ? \Illuminate\Support\Facades\Storage::url($mainImage->image_path) : null"
                                :name="$pet->name"
                            />

                            <span class="text-sm font-medium text-neutral-900 hover:underline dark:text-white">{{ $pet->name }}</span>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <div class="overflow-hidden rounded-xl border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-neutral-900">
        <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
            <h2 class="text-base font-semibold text-neutral-900 dark:text-white">{{ __('Recent Intakes') }}</h2>
        </div>

        <div class="p-6">
            @if ($recentIntakes->isEmpty())
                <p class="text-center text-sm text-neutral-500 dark:text-neutral-400">{{ __('No Recent Intakes') }}</p>
            @else
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5">
                    @foreach ($recentIntakes as $pet)
                        @php
                            $mainImage = $pet->images->firstWhere('is_main', true) ?? $pet->images->first();
                        @endphp
                        <a
                            wire:key="recent-intake-{{ $pet->id }}"
                            href="{{ route('pets.show', $pet) }}"
                            wire:navigate
                            class="flex flex-col items-center gap-2 rounded-lg p-3 text-center hover:bg-neutral-50 dark:hover:bg-neutral-800"
                        >
                            <span class="text-xs text-neutral-500 dark:text-neutral-400">{{ $pet->species->name }} - {{ $pet->ref }}</span>

                            <flux:avatar
                                size="xl"
                                class="size-20"
                                :src="$mainImage ? \Illuminate\Support\Facades\Storage::url($mainImage->image_path) : null"
                                :name="$pet->name"
                            />

                            <span class="text-sm font-medium text-neutral-900 hover:underline dark:text-white">{{ $pet->name }}</span>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
