{{-- Public portal pet card; expects $pet (with species, breed, size, shelter.region, images, cage.wing). Opens the details modal via showPet(). Only the foster flag of the cage is used: never show the family or the cage. --}}
@php
    $cardTints = ['bg-orange-100 dark:bg-orange-950/60', 'bg-sky-100 dark:bg-sky-950/60', 'bg-lime-100 dark:bg-lime-950/60', 'bg-pink-100 dark:bg-pink-950/60', 'bg-violet-100 dark:bg-violet-950/60'];
    $mainImage = $pet->images->firstWhere('is_main', true) ?? $pet->images->first();
@endphp
<button
    type="button"
    wire:key="public-pet-{{ $pet->id }}"
    wire:click="showPet({{ $pet->id }})"
    class="group flex flex-col overflow-hidden rounded-[2rem] bg-white text-start shadow-sm ring-1 ring-amber-100 transition duration-300 hover:-translate-y-1.5 hover:shadow-xl hover:shadow-orange-100 focus:outline-none focus-visible:ring-4 focus-visible:ring-orange-300 dark:bg-stone-900 dark:ring-stone-800 dark:hover:shadow-none"
>
    <div class="relative aspect-[4/3] overflow-hidden {{ $cardTints[$pet->id % count($cardTints)] }}">
        @if ($mainImage)
            <img src="{{ Storage::url($mainImage->image_path) }}" alt="{{ $pet->name }}" loading="lazy" class="size-full object-cover transition duration-500 group-hover:scale-105">
        @else
            <div class="flex size-full items-center justify-center text-7xl transition duration-500 group-hover:scale-110 group-hover:rotate-6">{{ $pet->species->emoji }}</div>
        @endif

        @if ($pet->is_featured)
            <span class="absolute top-3 left-3 rounded-full bg-yellow-300 px-3 py-1 text-xs font-bold text-yellow-900 shadow-sm">⭐ {{ __('Featured') }}</span>
        @endif

        @if ($pet->isInFosterFamily())
            <span class="absolute bottom-3 left-3 rounded-full bg-white px-3 py-1 text-xs font-bold text-amber-800 shadow-sm dark:bg-stone-900 dark:text-amber-300">🏡 {{ __('In a foster family') }}</span>
        @endif

        <span @class([
            'absolute top-3 right-3 flex size-9 items-center justify-center rounded-full text-lg font-bold shadow-sm',
            'bg-sky-400 text-white' => $pet->gender === 'male',
            'bg-pink-400 text-white' => $pet->gender === 'female',
        ]) title="{{ __(Str::ucfirst($pet->gender)) }}">{{ $pet->gender === 'male' ? '♂' : '♀' }}</span>
    </div>

    <div class="flex flex-1 flex-col gap-3 p-5">
        <div>
            <h3 class="font-display text-2xl font-semibold text-stone-900 group-hover:text-orange-500 dark:text-white">{{ $pet->name }}</h3>
            <p class="text-sm text-stone-500 dark:text-stone-400">{{ $pet->breed->name }}</p>
        </div>

        <div class="flex flex-wrap gap-1.5">
            <span class="rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-800 dark:bg-amber-950 dark:text-amber-200">{{ $pet->species->name }}</span>
            @if ($pet->size)
                <span class="rounded-full bg-lime-100 px-2.5 py-1 text-xs font-semibold text-lime-800 dark:bg-lime-950 dark:text-lime-200">{{ $pet->size->name }}</span>
            @endif
            @if ($pet->age_in_words)
                <span class="rounded-full bg-violet-100 px-2.5 py-1 text-xs font-semibold text-violet-800 dark:bg-violet-950 dark:text-violet-200">🎂 {{ $pet->age_in_words }}</span>
            @endif
        </div>

        <p class="mt-auto flex items-center gap-1.5 border-t border-dashed border-amber-100 pt-3 text-sm text-stone-500 dark:border-stone-800 dark:text-stone-400">
            📍 <span class="truncate">{{ $pet->shelter->short_name ?: $pet->shelter->name }} · {{ collect([$pet->shelter->city, $pet->shelter->region?->name])->filter()->unique()->implode(', ') }}</span>
        </p>
    </div>
</button>
