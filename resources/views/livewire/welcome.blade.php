@php
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
                    ['icon' => '🏠', 'bg' => 'bg-orange-100 dark:bg-orange-950/50', 'title' => __('Shelters join in'), 'text' => __('Each shelter manages its animals, spaces, foster families, vaccines and volunteers in its own private area.')],
                    ['icon' => '🔎', 'bg' => 'bg-sky-100 dark:bg-sky-950/50', 'title' => __('You search in one place'), 'text' => __('Animals ready for adoption from every shelter appear together here. Filter by species, gender, size and breed.')],
                    ['icon' => '💌', 'bg' => 'bg-pink-100 dark:bg-pink-950/50', 'title' => __('You meet your new friend'), 'text' => __('Found a match? Send your adoption application online and the shelter will contact you to arrange a visit.')],
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
                            {{ $species->emoji }} {{ $species->name_plural }}
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
                            @include('livewire.partials.public-pet-card', ['pet' => $pet])
                        @endforeach
                    </div>

                    <div class="mt-10">
                        {{ $this->pets->links(data: ['scrollTo' => '#adopt']) }}
                    </div>
                @endif
            </div>
        </div>
    </section>

    @include('livewire.partials.public-pet-details-modal')
</div>
