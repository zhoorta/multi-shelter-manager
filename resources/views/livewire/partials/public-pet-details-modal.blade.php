{{-- Public portal "public-pet-details" modal; the host component uses App\Livewire\Concerns\ShowsPublicPets. Pass showShelterLink => false on the shelter's own page. --}}
@php
    $cardTints = ['bg-orange-100 dark:bg-orange-950/60', 'bg-sky-100 dark:bg-sky-950/60', 'bg-lime-100 dark:bg-lime-950/60', 'bg-pink-100 dark:bg-pink-950/60', 'bg-violet-100 dark:bg-violet-950/60'];
@endphp

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
                    <div class="flex aspect-[16/9] items-center justify-center text-9xl">{{ $pet->species->emoji }}</div>
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
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <p class="font-display text-lg font-semibold text-stone-900 dark:text-white">🏠 {{ $pet->shelter->name }}</p>
                        @if ($showShelterLink ?? true)
                            <a href="{{ route('shelters.show', $pet->shelter) }}" class="text-sm font-semibold text-orange-600 hover:underline dark:text-orange-300" wire:navigate>
                                {{ __('See all animals from this shelter') }} →
                            </a>
                        @endif
                    </div>
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
