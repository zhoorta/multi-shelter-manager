@php
    $chip = 'inline-flex items-center gap-1.5 rounded-full border-2 px-4 py-1.5 text-sm font-semibold transition hover:-translate-y-0.5';
    $chipOn = 'border-orange-400 bg-orange-400 text-white shadow-md shadow-orange-200 dark:shadow-none';
    $chipOff = 'border-amber-200 bg-white text-stone-600 hover:border-orange-300 dark:border-stone-700 dark:bg-stone-900 dark:text-stone-300';
    $contactLink = 'rounded-full bg-white px-4 py-1.5 text-sm font-semibold text-stone-700 ring-1 ring-orange-200 transition hover:-translate-y-0.5 dark:bg-stone-900 dark:text-stone-200 dark:ring-stone-700';
    $cardTints = ['bg-orange-100 dark:bg-orange-950/60', 'bg-sky-100 dark:bg-sky-950/60', 'bg-lime-100 dark:bg-lime-950/60', 'bg-pink-100 dark:bg-pink-950/60', 'bg-violet-100 dark:bg-violet-950/60'];
@endphp

<div class="relative overflow-hidden">
    <div aria-hidden="true" class="pointer-events-none absolute -top-24 -left-24 size-96 rounded-full bg-orange-200/60 blur-3xl dark:bg-orange-900/30"></div>
    <div aria-hidden="true" class="pointer-events-none absolute top-40 -right-24 size-96 rounded-full bg-pink-200/60 blur-3xl dark:bg-pink-900/20"></div>

    <section class="relative mx-auto max-w-7xl px-4 py-16 sm:px-6">
        <div class="flex flex-col items-center gap-3 text-center">
            <span class="inline-flex items-center gap-2 rounded-full bg-white px-4 py-1.5 text-sm font-semibold text-orange-600 shadow-sm ring-1 ring-orange-100 dark:bg-stone-900 dark:text-orange-300 dark:ring-stone-800">
                🏡 {{ __('Many shelters, one big family') }}
            </span>
            <h1 class="font-display text-5xl font-bold tracking-tight text-stone-900 dark:text-white">{{ __('Partner shelters') }}</h1>
            <p class="max-w-2xl text-lg text-stone-500 dark:text-stone-400">{{ __('Meet the shelters that care for the animals on :app. Get in touch to visit, volunteer or adopt.', ['app' => config('app.name')]) }}</p>
        </div>

        @if ($this->regions->isNotEmpty())
            <div class="mt-10 flex flex-wrap items-center justify-center gap-2">
                <span class="me-1 text-sm font-bold tracking-wide text-stone-400 uppercase">{{ __('Region') }}</span>
                <button type="button" wire:click="$set('regionFilter', '')" @class([$chip, $regionFilter === '' ? $chipOn : $chipOff])>
                    📍 {{ __('All') }}
                </button>
                @foreach ($this->regions as $region)
                    <button
                        type="button"
                        wire:key="region-chip-{{ $region->id }}"
                        wire:click="$set('regionFilter', '{{ $region->id }}')"
                        @class([$chip, $regionFilter === (string) $region->id ? $chipOn : $chipOff])
                    >
                        {{ $region->name }}
                    </button>
                @endforeach
            </div>
        @endif

        <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3" wire:loading.class="opacity-60">
            @forelse ($this->shelters as $shelter)
                <article wire:key="shelter-{{ $shelter->id }}" class="flex flex-col overflow-hidden rounded-[2rem] bg-white shadow-sm ring-1 ring-amber-100 transition hover:-translate-y-1 hover:shadow-lg dark:bg-stone-900 dark:ring-stone-800">
                    <div class="flex items-center gap-4 p-6 {{ $cardTints[$loop->index % count($cardTints)] }}">
                        @if ($shelter->logo_path)
                            <img src="{{ Storage::url($shelter->logo_path) }}" alt="{{ $shelter->name }}" loading="lazy" class="size-16 shrink-0 rounded-2xl bg-white object-contain p-1 shadow-sm">
                        @else
                            <span class="flex size-16 shrink-0 items-center justify-center rounded-2xl bg-white text-3xl shadow-sm dark:bg-stone-900">🏠</span>
                        @endif
                        <div class="min-w-0">
                            <h2 class="font-display text-xl leading-tight font-semibold text-stone-900 dark:text-white">{{ $shelter->name }}</h2>
                            <p class="mt-1 truncate text-sm font-medium text-stone-600 dark:text-stone-300">
                                📍 {{ collect([$shelter->city, $shelter->region?->name])->filter()->unique()->implode(', ') }}
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-1 flex-col gap-4 p-6">
                        @if ($shelter->description)
                            <p class="line-clamp-4 leading-relaxed text-stone-600 dark:text-stone-300">{{ $shelter->description }}</p>
                        @endif

                        @if ($shelter->address || $shelter->postal_code)
                            <p class="text-sm text-stone-500 dark:text-stone-400">
                                {{ collect([$shelter->address, $shelter->postal_code, $shelter->city])->filter()->implode(', ') }}
                            </p>
                        @endif

                        <p class="mt-auto inline-flex items-center gap-2 font-display text-lg font-semibold text-pink-500">
                            🐾 {{ trans_choice(':count animal waiting for a home|:count animals waiting for a home', $shelter->available_pets_count) }}
                        </p>

                        @if ($shelter->email || $shelter->phone || $shelter->website)
                            <div class="flex flex-wrap gap-2">
                                @if ($shelter->email)
                                    <a href="mailto:{{ $shelter->email }}" class="rounded-full bg-orange-500 px-4 py-1.5 text-sm font-semibold text-white shadow-sm transition hover:-translate-y-0.5 hover:bg-orange-600">
                                        ✉️ {{ __('Email') }}
                                    </a>
                                @endif
                                @if ($shelter->phone)
                                    <a href="tel:{{ $shelter->phone }}" class="{{ $contactLink }}">📞 {{ $shelter->phone }}</a>
                                @endif
                                @if ($shelter->website)
                                    <a href="{{ $shelter->website }}" target="_blank" rel="noopener" class="{{ $contactLink }}">🌐 {{ __('Website') }}</a>
                                @endif
                            </div>
                        @endif
                    </div>
                </article>
            @empty
                <div class="col-span-full rounded-[2rem] bg-white p-12 text-center text-stone-500 shadow-sm ring-1 ring-amber-100 dark:bg-stone-900 dark:text-stone-400 dark:ring-stone-800">
                    🏠 {{ __('No shelters found in this region.') }}
                </div>
            @endforelse
        </div>
    </section>
</div>
