@php
    $contactLink = 'rounded-full bg-white px-5 py-2 text-sm font-semibold text-stone-700 ring-1 ring-orange-200 transition hover:-translate-y-0.5 dark:bg-stone-900 dark:text-stone-200 dark:ring-stone-700';
@endphp

<div class="relative overflow-hidden">
    <div aria-hidden="true" class="pointer-events-none absolute -top-24 -left-24 size-96 rounded-full bg-orange-200/60 blur-3xl dark:bg-orange-900/30"></div>
    <div aria-hidden="true" class="pointer-events-none absolute top-40 -right-24 size-96 rounded-full bg-pink-200/60 blur-3xl dark:bg-pink-900/20"></div>

    <section class="relative mx-auto max-w-7xl px-4 py-12 sm:px-6">
        <a href="{{ route('shelters') }}" class="inline-flex items-center gap-1 text-sm font-semibold text-stone-500 transition hover:text-orange-500 dark:text-stone-400" wire:navigate>
            ← {{ __('Partner shelters') }}
        </a>

        {{-- Shelter details --}}
        <div class="mt-6 flex flex-col gap-6 rounded-[2rem] bg-white p-6 shadow-sm ring-1 ring-amber-100 sm:flex-row sm:items-start sm:p-8 dark:bg-stone-900 dark:ring-stone-800">
            @if ($shelter->logo_path)
                <img src="{{ Storage::url($shelter->logo_path) }}" alt="{{ $shelter->name }}" class="size-24 shrink-0 rounded-3xl bg-white object-contain p-1 shadow-sm ring-1 ring-amber-100 dark:ring-stone-800">
            @else
                <span class="flex size-24 shrink-0 items-center justify-center rounded-3xl bg-orange-100 text-5xl dark:bg-orange-950/60">🏠</span>
            @endif

            <div class="flex min-w-0 flex-1 flex-col gap-4">
                <div>
                    <h1 class="font-display text-4xl font-bold tracking-tight text-stone-900 dark:text-white">{{ $shelter->name }}</h1>
                    <p class="mt-1 font-medium text-stone-500 dark:text-stone-400">
                        📍 {{ collect([$shelter->address, $shelter->postal_code, $shelter->city, $shelter->region?->name])->filter()->unique()->implode(', ') }}
                    </p>
                </div>

                @if ($shelter->description)
                    <p class="max-w-3xl leading-relaxed whitespace-pre-line text-stone-600 dark:text-stone-300">{{ $shelter->description }}</p>
                @endif

                @if ($shelter->email || $shelter->phone || $shelter->website)
                    <div class="flex flex-wrap gap-2">
                        @if ($shelter->email)
                            <a href="mailto:{{ $shelter->email }}" class="rounded-full bg-orange-500 px-5 py-2 text-sm font-semibold text-white shadow-sm transition hover:-translate-y-0.5 hover:bg-orange-600">
                                ✉️ {{ $shelter->email }}
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
        </div>

        {{-- Pets for adoption --}}
        <div id="shelter-pets" class="mt-14 scroll-mt-20">
            <h2 class="font-display text-3xl font-bold text-stone-900 dark:text-white">
                {{ __('Looking for a family') }} 🏡
                <span class="ms-1 align-middle text-lg font-semibold text-pink-500">({{ $this->pets->total() }})</span>
            </h2>

            <div class="relative mt-6">
                <div wire:loading.flex wire:target="gotoPage, nextPage, previousPage" class="absolute inset-0 z-10 items-start justify-center rounded-3xl bg-amber-50/70 pt-24 dark:bg-stone-950/70">
                    <span class="animate-bounce text-5xl">🐾</span>
                </div>

                @if ($this->pets->isEmpty())
                    <div class="flex flex-col items-center gap-3 rounded-[2rem] border-2 border-dashed border-amber-200 bg-white/60 px-6 py-16 text-center dark:border-stone-700 dark:bg-stone-900/60">
                        <span class="text-6xl">🐕‍🦺</span>
                        <p class="font-display text-2xl font-semibold text-stone-700 dark:text-stone-200">{{ __('This shelter has no animals for adoption right now') }}</p>
                        <p class="text-stone-500 dark:text-stone-400">{{ __('New animals arrive all the time — come back soon!') }}</p>
                    </div>
                @else
                    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                        @foreach ($this->pets as $pet)
                            @include('livewire.partials.public-pet-card', ['pet' => $pet])
                        @endforeach
                    </div>

                    <div class="mt-10">
                        {{ $this->pets->links(data: ['scrollTo' => '#shelter-pets']) }}
                    </div>
                @endif
            </div>
        </div>
    </section>

    @include('livewire.partials.public-pet-details-modal', ['showShelterLink' => false])
</div>
