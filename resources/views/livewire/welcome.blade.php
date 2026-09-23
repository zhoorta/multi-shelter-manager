@php
    $speciesEmoji = fn (?string $name): string => match (true) {
        str_contains(Str::lower((string) $name), 'cão'),
        str_contains(Str::lower((string) $name), 'cao'),
        str_contains(Str::lower((string) $name), 'dog'),
        str_contains(Str::lower((string) $name), 'canin') => '🐶',
        str_contains(Str::lower((string) $name), 'gat'),
        str_contains(Str::lower((string) $name), 'cat'),
        str_contains(Str::lower((string) $name), 'felin') => '🐱',
        str_contains(Str::lower((string) $name), 'coelh'),
        str_contains(Str::lower((string) $name), 'rabbit') => '🐰',
        str_contains(Str::lower((string) $name), 'ave'),
        str_contains(Str::lower((string) $name), 'bird') => '🐦',
        default => '🐾',
    };
    $cardTints = ['bg-orange-100 dark:bg-orange-950/60', 'bg-sky-100 dark:bg-sky-950/60', 'bg-lime-100 dark:bg-lime-950/60', 'bg-pink-100 dark:bg-pink-950/60', 'bg-violet-100 dark:bg-violet-950/60'];
    $chip = 'inline-flex items-center gap-1.5 rounded-full border-2 px-4 py-1.5 text-sm font-semibold transition hover:-translate-y-0.5';
    $chipOn = 'border-orange-400 bg-orange-400 text-white shadow-md shadow-orange-200 dark:shadow-none';
    $chipOff = 'border-amber-200 bg-white text-stone-600 hover:border-orange-300 dark:border-stone-700 dark:bg-stone-900 dark:text-stone-300';
    // Pill-shaped selects matching the filter chips; the native arrow is hidden and replaced by $selectChevron.
    $select = 'w-full cursor-pointer appearance-none rounded-full border-2 py-1.5 ps-4 pe-10 text-sm font-semibold transition focus:border-orange-400 focus:ring-4 focus:ring-orange-100 focus:outline-none disabled:cursor-not-allowed disabled:opacity-50 dark:focus:ring-orange-950';
    $selectOn = 'border-orange-400 bg-orange-50 text-orange-700 dark:bg-orange-950/50 dark:text-orange-200';
    $selectChevron = '<svg aria-hidden="true" viewBox="0 0 20 20" fill="currentColor" class="pointer-events-none absolute top-1/2 right-3.5 size-4 -translate-y-1/2 text-orange-500"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.17l3.71-3.94a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd"/></svg>';
    $selectOff = 'border-amber-200 bg-white text-stone-600 hover:border-orange-300 dark:border-stone-700 dark:bg-stone-900 dark:text-stone-300';
    $hasFilters = $speciesFilter !== '' || $genderFilter !== '' || $sizeFilter !== '' || $breedFilter !== '' || $regionFilter !== '';
@endphp

<div>
    {{-- Hero --}}
    <section class="relative overflow-hidden">
        <div aria-hidden="true" class="pointer-events-none absolute -top-24 -left-24 size-96 rounded-full bg-orange-200/60 blur-3xl dark:bg-orange-900/30"></div>
        <div aria-hidden="true" class="pointer-events-none absolute top-40 -right-24 size-96 rounded-full bg-pink-200/60 blur-3xl dark:bg-pink-900/20"></div>
        <div aria-hidden="true" class="pointer-events-none absolute bottom-0 left-1/3 size-72 rounded-full bg-sky-200/50 blur-3xl dark:bg-sky-900/20"></div>

        <div class="relative mx-auto grid max-w-7xl items-center gap-12 px-4 py-16 sm:px-6 lg:grid-cols-2 lg:py-24">
            <div class="flex flex-col items-start gap-6">
                <span class="inline-flex items-center gap-2 rounded-full bg-white px-4 py-1.5 text-sm font-semibold text-orange-600 shadow-sm ring-1 ring-orange-100 dark:bg-stone-900 dark:text-orange-300 dark:ring-stone-800">
                    🏡 {{ __('Many shelters, one big family') }}
                </span>

                <h1 class="font-display text-5xl leading-[1.05] font-bold tracking-tight text-stone-900 sm:text-6xl dark:text-white">
                    {{ __('Every paw deserves') }}
                    <span class="relative whitespace-nowrap text-orange-500">
                        {{ __('a home') }}
                        <svg aria-hidden="true" viewBox="0 0 200 12" class="absolute -bottom-2 left-0 h-3 w-full text-pink-300" preserveAspectRatio="none"><path d="M2 9c40-7 120-9 196-2" stroke="currentColor" stroke-width="5" fill="none" stroke-linecap="round"/></svg>
                    </span>
                    💛
                </h1>

                <p class="max-w-xl text-lg leading-relaxed text-stone-600 dark:text-stone-300">
                    {{ __(':app brings together animal shelters from all over the country on a single platform. Each shelter manages its own animals, and here you can meet all of them in one place — and find your new best friend.', ['app' => config('app.name')]) }}
                </p>

                <div class="flex flex-wrap items-center gap-3">
                    <a href="#adopt" class="rounded-full bg-orange-500 px-7 py-3.5 font-display text-lg font-semibold text-white shadow-lg shadow-orange-300/60 transition hover:-translate-y-0.5 hover:bg-orange-600 dark:shadow-none">
                        {{ __('Meet the animals') }} 🐾
                    </a>
                    <a href="#how-it-works" class="rounded-full bg-white px-7 py-3.5 font-display text-lg font-semibold text-stone-700 shadow-sm ring-1 ring-amber-200 transition hover:-translate-y-0.5 dark:bg-stone-900 dark:text-stone-200 dark:ring-stone-700">
                        {{ __('How it works') }}
                    </a>
                </div>
            </div>

            <div class="relative mx-auto w-full max-w-md">
                <div class="grid grid-cols-2 gap-4">
                    <div class="flex aspect-square rotate-[-4deg] items-center justify-center rounded-[2.5rem] bg-orange-300 text-8xl shadow-xl shadow-orange-200 transition hover:rotate-0 dark:bg-orange-800 dark:shadow-none">🐶</div>
                    <div class="mt-10 flex aspect-square rotate-[5deg] items-center justify-center rounded-[2.5rem] bg-sky-300 text-8xl shadow-xl shadow-sky-200 transition hover:rotate-0 dark:bg-sky-800 dark:shadow-none">🐱</div>
                    <div class="-mt-6 flex aspect-square rotate-[3deg] items-center justify-center rounded-[2.5rem] bg-lime-300 text-8xl shadow-xl shadow-lime-200 transition hover:rotate-0 dark:bg-lime-800 dark:shadow-none">🐰</div>
                    <div class="mt-4 flex aspect-square rotate-[-3deg] items-center justify-center rounded-[2.5rem] bg-pink-300 text-8xl shadow-xl shadow-pink-200 transition hover:rotate-0 dark:bg-pink-800 dark:shadow-none">🐹</div>
                </div>
                <span aria-hidden="true" class="absolute -top-6 right-6 animate-bounce text-4xl">💕</span>
                <span aria-hidden="true" class="absolute -bottom-4 -left-4 text-4xl">🦴</span>
            </div>
        </div>

        {{-- Stats --}}
        <div class="relative mx-auto max-w-5xl px-4 pb-16 sm:px-6">
            <div class="grid gap-4 sm:grid-cols-3">
                @foreach ([
                    ['value' => $this->stats['shelters'], 'label' => __('Partner shelters'), 'icon' => '🏠', 'tint' => 'text-orange-500', 'url' => route('shelters')],
                    ['value' => $this->stats['pets'], 'label' => __('Animals waiting for a home'), 'icon' => '🐾', 'tint' => 'text-pink-500', 'url' => null],
                    ['value' => $this->stats['regions'], 'label' => __('Regions covered'), 'icon' => '📍', 'tint' => 'text-sky-500', 'url' => null],
                ] as $stat)
                    <{{ $stat['url'] ? 'a' : 'div' }}
                        wire:key="stat-{{ $loop->index }}"
                        @if ($stat['url']) href="{{ $stat['url'] }}" wire:navigate @endif
                        @class([
                            'flex items-center gap-4 rounded-3xl bg-white p-5 shadow-sm ring-1 ring-amber-100 dark:bg-stone-900 dark:ring-stone-800',
                            'transition hover:-translate-y-1 hover:shadow-lg hover:ring-orange-300 dark:hover:ring-orange-800' => $stat['url'],
                        ])
                    >
                        <span class="flex size-14 shrink-0 items-center justify-center rounded-2xl bg-amber-50 text-3xl dark:bg-stone-800">{{ $stat['icon'] }}</span>
                        <div>
                            <div class="font-display text-3xl font-bold {{ $stat['tint'] }}">{{ $stat['value'] }}</div>
                            <div class="text-sm font-medium text-stone-500 dark:text-stone-400">{{ $stat['label'] }}</div>
                        </div>
                    </{{ $stat['url'] ? 'a' : 'div' }}>
                @endforeach
            </div>
        </div>
    </section>

    {{-- How it works --}}
    <section id="how-it-works" class="scroll-mt-20 bg-white py-16 dark:bg-stone-900">
        <div class="mx-auto max-w-7xl px-4 sm:px-6">
            <h2 class="text-center font-display text-4xl font-bold text-stone-900 dark:text-white">{{ __('How it works') }}</h2>
            <p class="mx-auto mt-3 max-w-2xl text-center text-stone-500 dark:text-stone-400">{{ __('A simple bridge between those who care for animals and those who want to give them a family.') }}</p>

            <div class="mt-12 grid gap-6 md:grid-cols-3">
                @foreach ([
                    ['icon' => '🏠', 'bg' => 'bg-orange-100 dark:bg-orange-950/50', 'title' => __('Shelters join in'), 'text' => __('Each shelter manages its animals, spaces, vaccines and volunteers in its own private area.')],
                    ['icon' => '🔎', 'bg' => 'bg-sky-100 dark:bg-sky-950/50', 'title' => __('You search in one place'), 'text' => __('Animals ready for adoption from every shelter appear together here. Filter by species, gender, size and breed.')],
                    ['icon' => '💌', 'bg' => 'bg-pink-100 dark:bg-pink-950/50', 'title' => __('You meet your new friend'), 'text' => __('Found a match? Contact the shelter directly to arrange a visit and start the adoption.')],
                ] as $step)
                    <div wire:key="step-{{ $loop->index }}" class="rounded-[2rem] {{ $step['bg'] }} p-8 transition hover:-translate-y-1">
                        <div class="flex items-center gap-3">
                            <span class="flex size-14 items-center justify-center rounded-2xl bg-white text-3xl shadow-sm dark:bg-stone-900">{{ $step['icon'] }}</span>
                            <span class="font-display text-5xl font-bold text-stone-900/10 dark:text-white/10">{{ $loop->iteration }}</span>
                        </div>
                        <h3 class="mt-5 font-display text-2xl font-semibold text-stone-900 dark:text-white">{{ $step['title'] }}</h3>
                        <p class="mt-2 leading-relaxed text-stone-600 dark:text-stone-300">{{ $step['text'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Adoption listing --}}
    <section id="adopt" class="scroll-mt-20 py-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6">
            <div class="flex flex-col items-center gap-3 text-center">
                <h2 class="font-display text-4xl font-bold text-stone-900 dark:text-white">{{ __('Looking for a family') }} 🏡</h2>
                <p class="max-w-2xl text-stone-500 dark:text-stone-400">{{ __('These friends are waiting for someone just like you. Use the filters to find your perfect match.') }}</p>
            </div>

            {{-- Filters --}}
            <div class="mt-10 flex flex-col gap-5 rounded-[2rem] bg-white p-6 shadow-sm ring-1 ring-amber-100 dark:bg-stone-900 dark:ring-stone-800">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="me-1 text-sm font-bold tracking-wide text-stone-400 uppercase">{{ __('Species') }}</span>
                    <button type="button" wire:click="$set('speciesFilter', '')" @class([$chip, $speciesFilter === '' ? $chipOn : $chipOff])>
                        🐾 {{ __('All') }}
                    </button>
                    @foreach ($this->species as $species)
                        <button
                            type="button"
                            wire:key="species-chip-{{ $species->id }}"
                            wire:click="$set('speciesFilter', '{{ $species->id }}')"
                            @class([$chip, $speciesFilter === (string) $species->id ? $chipOn : $chipOff])
                        >
                            {{ $speciesEmoji($species->name) }} {{ $species->name_plural }}
                        </button>
                    @endforeach
                </div>

                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-[auto_1fr_1fr_1fr_auto] lg:items-end">
                    <div class="flex flex-col gap-2">
                        <span class="text-sm font-bold tracking-wide text-stone-400 uppercase">{{ __('Gender') }}</span>
                        <div class="flex flex-wrap gap-2">
                            <button type="button" wire:click="$set('genderFilter', '')" @class([$chip, $genderFilter === '' ? $chipOn : $chipOff])>{{ __('All') }}</button>
                            <button type="button" wire:click="$set('genderFilter', 'male')" @class([$chip, $genderFilter === 'male' ? $chipOn : $chipOff])>♂ {{ __('Male') }}</button>
                            <button type="button" wire:click="$set('genderFilter', 'female')" @class([$chip, $genderFilter === 'female' ? $chipOn : $chipOff])>♀ {{ __('Female') }}</button>
                        </div>
                    </div>

                    <label class="flex flex-col gap-2">
                        <span class="text-sm font-bold tracking-wide text-stone-400 uppercase">{{ __('Region') }}</span>
                        <div class="relative">
                            <select wire:model.live="regionFilter" @class([$select, $regionFilter !== '' ? $selectOn : $selectOff])>
                                <option value="">📍 {{ __('All') }}</option>
                                @foreach ($this->regions as $region)
                                    <option wire:key="region-option-{{ $region->id }}" value="{{ $region->id }}">{{ $region->name }}</option>
                                @endforeach
                            </select>
                            {!! $selectChevron !!}
                        </div>
                    </label>

                    <label class="flex flex-col gap-2">
                        <span class="text-sm font-bold tracking-wide text-stone-400 uppercase">{{ __('Size') }}</span>
                        <div class="relative">
                            <select wire:model.live="sizeFilter" @class([$select, $sizeFilter !== '' ? $selectOn : $selectOff]) @disabled($speciesFilter === '')>
                                <option value="">{{ $speciesFilter === '' ? __('Choose a species first') : __('All') }}</option>
                                @foreach ($this->sizes as $size)
                                    <option wire:key="size-option-{{ $size->id }}" value="{{ $size->id }}">{{ $size->name }}</option>
                                @endforeach
                            </select>
                            {!! $selectChevron !!}
                        </div>
                    </label>

                    <label class="flex flex-col gap-2">
                        <span class="text-sm font-bold tracking-wide text-stone-400 uppercase">{{ __('Breed') }}</span>
                        <div class="relative">
                            <select wire:model.live="breedFilter" @class([$select, $breedFilter !== '' ? $selectOn : $selectOff]) @disabled($speciesFilter === '')>
                                <option value="">{{ $speciesFilter === '' ? __('Choose a species first') : __('All') }}</option>
                                @foreach ($this->breeds as $breed)
                                    <option wire:key="breed-option-{{ $breed->id }}" value="{{ $breed->id }}">{{ $breed->name }}</option>
                                @endforeach
                            </select>
                            {!! $selectChevron !!}
                        </div>
                    </label>

                    @if ($hasFilters)
                        <button type="button" wire:click="clearFilters" class="justify-self-start rounded-full px-4 py-2.5 text-sm font-semibold text-orange-600 transition hover:bg-orange-50 dark:text-orange-300 dark:hover:bg-stone-800">
                            ✕ {{ __('Clear filters') }}
                        </button>
                    @endif
                </div>
            </div>

            {{-- Pet grid --}}
            <div class="relative mt-10">
                <div wire:loading.flex wire:target="speciesFilter, genderFilter, sizeFilter, breedFilter, regionFilter, clearFilters, gotoPage, nextPage, previousPage" class="absolute inset-0 z-10 items-start justify-center rounded-3xl bg-amber-50/70 pt-24 dark:bg-stone-950/70">
                    <span class="animate-bounce text-5xl">🐾</span>
                </div>

                @if ($this->pets->isEmpty())
                    <div class="flex flex-col items-center gap-3 rounded-[2rem] border-2 border-dashed border-amber-200 bg-white/60 px-6 py-16 text-center dark:border-stone-700 dark:bg-stone-900/60">
                        <span class="text-6xl">🐕‍🦺</span>
                        <p class="font-display text-2xl font-semibold text-stone-700 dark:text-stone-200">{{ __('No friends found with these filters') }}</p>
                        <p class="text-stone-500 dark:text-stone-400">{{ __('Try changing the filters — new animals arrive all the time!') }}</p>
                    </div>
                @else
                    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                        @foreach ($this->pets as $pet)
                            @php
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
                                        <div class="flex size-full items-center justify-center text-7xl transition duration-500 group-hover:scale-110 group-hover:rotate-6">{{ $speciesEmoji($pet->species->name) }}</div>
                                    @endif

                                    @if ($pet->is_featured)
                                        <span class="absolute top-3 left-3 rounded-full bg-yellow-300 px-3 py-1 text-xs font-bold text-yellow-900 shadow-sm">⭐ {{ __('Featured') }}</span>
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
                        @endforeach
                    </div>

                    <div class="mt-10">
                        {{ $this->pets->links(data: ['scrollTo' => '#adopt']) }}
                    </div>
                @endif
            </div>
        </div>
    </section>

    {{-- Pet details --}}
    <flux:modal name="public-pet-details" :closable="false" class="w-full max-w-2xl !p-0 backdrop:bg-stone-950/50! backdrop:backdrop-blur-sm">
        @if ($pet = $this->selectedPet)
            @php
                $mainImage = $pet->images->firstWhere('is_main', true) ?? $pet->images->first();
                $galleryUrls = $mainImage
                    ? collect([$mainImage])->merge($pet->images->reject(fn ($image) => $image->is($mainImage)))->map(fn ($image) => Storage::url($image->image_path))->values()
                    : collect();
            @endphp
            <div wire:key="public-pet-details-{{ $pet->id }}" class="relative overflow-hidden rounded-xl">
                <flux:modal.close>
                    <button type="button" aria-label="{{ __('Close') }}" class="absolute top-3 right-3 z-20 flex size-10 items-center justify-center rounded-full bg-white/95 text-stone-700 shadow-lg ring-1 ring-black/5 transition hover:scale-110 hover:bg-white hover:text-orange-500 focus:outline-none focus-visible:ring-4 focus-visible:ring-orange-300">
                        <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" class="size-5"><path d="M6 6l12 12M18 6L6 18"/></svg>
                    </button>
                </flux:modal.close>
                {{-- Photo slider: a scroll-snap track (native swipe on touch) kept in sync with arrows, dots, thumbnails and the arrow keys. --}}
                <div
                    x-data="{
                        current: 0,
                        total: {{ $galleryUrls->count() }},
                        go(index) {
                            this.current = (index + this.total) % this.total;
                            this.$refs.track.scrollTo({ left: this.current * this.$refs.track.clientWidth, behavior: 'smooth' });
                        },
                        sync() {
                            this.current = Math.round(this.$refs.track.scrollLeft / this.$refs.track.clientWidth);
                        },
                    }"
                    x-on:keydown.left.window="total > 1 && go(current - 1)"
                    x-on:keydown.right.window="total > 1 && go(current + 1)"
                    class="relative {{ $cardTints[$pet->id % count($cardTints)] }}"
                >
                    @if ($galleryUrls->isNotEmpty())
                        <div x-ref="track" x-on:scroll.debounce.60ms="sync()" class="flex aspect-[4/3] snap-x snap-mandatory overflow-x-auto [scrollbar-width:none] sm:aspect-[16/10] [&::-webkit-scrollbar]:hidden">
                            @foreach ($galleryUrls as $url)
                                <div wire:key="public-pet-slide-{{ $loop->index }}" class="relative size-full shrink-0 snap-center overflow-hidden">
                                    {{-- Blurred copy fills the frame so portrait/landscape photos show whole, never cropped. --}}
                                    <img src="{{ $url }}" alt="" aria-hidden="true" class="absolute inset-0 size-full scale-110 object-cover opacity-60 blur-2xl">
                                    <img src="{{ $url }}" alt="{{ $pet->name }} — {{ __('Photo') }} {{ $loop->iteration }}" @if (! $loop->first) loading="lazy" @endif class="relative size-full object-contain">
                                </div>
                            @endforeach
                        </div>

                        @if ($galleryUrls->count() > 1)
                            <button type="button" x-on:click="go(current - 1)" aria-label="{{ __('Previous photo') }}" class="absolute top-1/2 left-3 flex size-11 -translate-y-1/2 items-center justify-center rounded-full bg-white/90 text-2xl font-bold text-stone-700 shadow-lg transition hover:scale-110 hover:bg-white">
                                ‹
                            </button>
                            <button type="button" x-on:click="go(current + 1)" aria-label="{{ __('Next photo') }}" class="absolute top-1/2 right-3 flex size-11 -translate-y-1/2 items-center justify-center rounded-full bg-white/90 text-2xl font-bold text-stone-700 shadow-lg transition hover:scale-110 hover:bg-white">
                                ›
                            </button>

                            <span class="absolute top-3 left-3 rounded-full bg-black/50 px-3 py-1 text-xs font-semibold text-white backdrop-blur" x-text="`📷 ${current + 1} / ${total}`"></span>

                            <div class="absolute bottom-3 left-1/2 flex -translate-x-1/2 gap-1.5 rounded-full bg-black/30 px-2.5 py-1.5 backdrop-blur">
                                @foreach ($galleryUrls as $url)
                                    <button
                                        type="button"
                                        wire:key="public-pet-dot-{{ $loop->index }}"
                                        x-on:click="go({{ $loop->index }})"
                                        aria-label="{{ __('Photo') }} {{ $loop->iteration }}"
                                        class="h-2 rounded-full transition-all"
                                        :class="current === {{ $loop->index }} ? 'w-5 bg-white' : 'w-2 bg-white/60 hover:bg-white'"
                                    ></button>
                                @endforeach
                            </div>
                        @endif
                    @else
                        <div class="flex aspect-[16/9] items-center justify-center text-9xl">{{ $speciesEmoji($pet->species->name) }}</div>
                    @endif

                    @if ($galleryUrls->count() > 1)
                        <div class="flex gap-2 overflow-x-auto bg-white px-6 pt-4 [scrollbar-width:none] dark:bg-stone-900 [&::-webkit-scrollbar]:hidden">
                            @foreach ($galleryUrls as $url)
                                <button
                                    type="button"
                                    wire:key="public-pet-thumb-{{ $loop->index }}"
                                    x-on:click="go({{ $loop->index }})"
                                    aria-label="{{ __('Photo') }} {{ $loop->iteration }}"
                                    class="size-16 shrink-0 overflow-hidden rounded-2xl transition"
                                    :class="current === {{ $loop->index }} ? 'ring-3 ring-orange-400 ring-offset-2 dark:ring-offset-stone-900' : 'opacity-60 hover:opacity-100'"
                                >
                                    <img src="{{ $url }}" alt="" loading="lazy" class="size-full object-cover">
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="flex flex-col gap-5 p-6">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h3 class="font-display text-3xl font-bold text-stone-900 dark:text-white">{{ $pet->name }}</h3>
                            <p class="text-stone-500 dark:text-stone-400">{{ $pet->species->name }} · {{ $pet->breed->name }}</p>
                        </div>
                        <span @class([
                            'rounded-full px-3 py-1 text-sm font-bold',
                            'bg-sky-100 text-sky-700 dark:bg-sky-950 dark:text-sky-200' => $pet->gender === 'male',
                            'bg-pink-100 text-pink-700 dark:bg-pink-950 dark:text-pink-200' => $pet->gender === 'female',
                        ])>{{ $pet->gender === 'male' ? '♂' : '♀' }} {{ __(Str::ucfirst($pet->gender)) }}</span>
                    </div>

                    <dl class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                        @foreach (array_filter([
                            __('Age') => $pet->age_in_words,
                            __('Size') => $pet->size?->name,
                            __('Fur Type') => $pet->furType?->name,
                            __('Neutered') => $pet->is_neutered ? __('Yes') : __('No'),
                        ]) as $label => $value)
                            <div wire:key="pet-fact-{{ $loop->index }}" class="rounded-2xl bg-amber-50 p-3 dark:bg-stone-800">
                                <dt class="text-xs font-bold tracking-wide text-stone-400 uppercase">{{ $label }}</dt>
                                <dd class="mt-0.5 font-semibold text-stone-800 dark:text-stone-100">{{ $value }}</dd>
                            </div>
                        @endforeach
                    </dl>

                    @if ($pet->description)
                        {{-- pets.description is sanitized to a tag allowlist on write (see PetForm::sanitizeDescription). --}}
                        <div class="prose prose-stone max-w-none text-stone-600 dark:text-stone-300 [&_ol]:list-decimal [&_ol]:ps-5 [&_ul]:list-disc [&_ul]:ps-5">
                            {!! $pet->description !!}
                        </div>
                    @endif

                    <div class="flex flex-col gap-3 rounded-2xl bg-orange-50 p-4 dark:bg-orange-950/40">
                        <p class="font-display text-lg font-semibold text-stone-900 dark:text-white">🏠 {{ $pet->shelter->name }}</p>
                        <p class="text-sm text-stone-600 dark:text-stone-300">
                            {{ collect([$pet->shelter->address, $pet->shelter->postal_code, $pet->shelter->city, $pet->shelter->region?->name])->filter()->implode(', ') }}
                        </p>
                        <div class="flex flex-wrap gap-2">
                            @if ($pet->shelter->email)
                                <a href="mailto:{{ $pet->shelter->email }}?subject={{ rawurlencode(__('Adoption of :name (:ref)', ['name' => $pet->name, 'ref' => $pet->ref])) }}" class="rounded-full bg-orange-500 px-5 py-2 text-sm font-semibold text-white shadow-sm transition hover:-translate-y-0.5 hover:bg-orange-600">
                                    💌 {{ __('I want to adopt') }}
                                </a>
                            @endif
                            @if ($pet->shelter->phone)
                                <a href="tel:{{ $pet->shelter->phone }}" class="rounded-full bg-white px-5 py-2 text-sm font-semibold text-stone-700 ring-1 ring-orange-200 transition hover:-translate-y-0.5 dark:bg-stone-900 dark:text-stone-200 dark:ring-stone-700">
                                    📞 {{ $pet->shelter->phone }}
                                </a>
                            @endif
                            @if ($pet->shelter->website)
                                <a href="{{ $pet->shelter->website }}" target="_blank" rel="noopener" class="rounded-full bg-white px-5 py-2 text-sm font-semibold text-stone-700 ring-1 ring-orange-200 transition hover:-translate-y-0.5 dark:bg-stone-900 dark:text-stone-200 dark:ring-stone-700">
                                    🌐 {{ __('Website') }}
                                </a>
                            @endif
                        </div>
                        <p class="text-xs text-stone-500 dark:text-stone-400">{{ __('Reference') }}: {{ $pet->ref }}</p>
                    </div>
                </div>
            </div>
        @endif
    </flux:modal>
</div>
