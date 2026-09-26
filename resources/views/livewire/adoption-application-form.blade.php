@php
    $mainImage = $pet->images->firstWhere('is_main', true) ?? $pet->images->first();
@endphp

<div class="relative overflow-hidden">
    <div aria-hidden="true" class="pointer-events-none absolute -top-24 -left-24 size-96 rounded-full bg-orange-200/60 blur-3xl dark:bg-orange-900/30"></div>
    <div aria-hidden="true" class="pointer-events-none absolute top-40 -right-24 size-96 rounded-full bg-pink-200/60 blur-3xl dark:bg-pink-900/20"></div>

    <section class="relative mx-auto max-w-3xl px-4 py-12 sm:px-6">
        <a href="{{ $pet->publicPageUrl() }}" class="inline-flex items-center gap-1 text-sm font-semibold text-stone-500 transition hover:text-orange-500 dark:text-stone-400" wire:navigate>
            ← {{ $pet->name }}
        </a>

        <div class="mt-6 flex items-center gap-5 rounded-[2rem] bg-white p-6 shadow-sm ring-1 ring-amber-100 sm:p-8 dark:bg-stone-900 dark:ring-stone-800">
            @if ($mainImage)
                <img src="{{ Storage::url($mainImage->image_path) }}" alt="{{ $pet->name }}" class="size-24 shrink-0 rounded-3xl object-cover shadow-sm">
            @else
                <span class="flex size-24 shrink-0 items-center justify-center rounded-3xl bg-orange-100 text-5xl dark:bg-orange-950/60">{{ $pet->species->emoji }}</span>
            @endif

            <div class="min-w-0">
                <h1 class="font-display text-3xl font-bold tracking-tight text-stone-900 sm:text-4xl dark:text-white">{{ __('Adopt :name', ['name' => $pet->name]) }}</h1>
                <p class="mt-1 text-stone-500 dark:text-stone-400">{{ $pet->species->name }} · {{ $pet->breed->name }} · 🏠 {{ $pet->shelter->name }}</p>
            </div>
        </div>

        @if ($isSubmitted)
            <div class="mt-8 flex flex-col items-center gap-3 rounded-[2rem] bg-white px-6 py-16 text-center shadow-sm ring-1 ring-amber-100 dark:bg-stone-900 dark:ring-stone-800" data-test="application-sent">
                <span class="text-6xl">💌</span>
                <h2 class="font-display text-3xl font-semibold text-stone-900 dark:text-white">{{ __('Application sent!') }}</h2>
                <p class="max-w-xl text-stone-500 dark:text-stone-400">{{ __(':shelter received your application and will contact you soon. Thank you for choosing to adopt!', ['shelter' => $pet->shelter->name]) }}</p>
                <a href="{{ route('home') }}" class="mt-3 rounded-full bg-orange-500 px-6 py-3 font-semibold text-white shadow-sm transition hover:-translate-y-0.5 hover:bg-orange-600" wire:navigate>
                    🐾 {{ __('See more animals') }}
                </a>
            </div>
        @else
            <form wire:submit="submitApplication" class="mt-8 flex flex-col gap-6">
                {{-- Honeypot: hidden from people and screen readers; bots that fill it in are silently ignored. --}}
                {{-- Inline style, not utilities: hiding it must never depend on the CSS build. --}}
                <div aria-hidden="true" style="position: absolute; left: -9999px; width: 1px; height: 1px; overflow: hidden;">
                    <label for="website">Website</label>
                    <input type="text" id="website" name="website" wire:model="website" tabindex="-1" autocomplete="off">
                </div>

                <div class="flex flex-col gap-4 rounded-[2rem] bg-white p-6 shadow-sm ring-1 ring-amber-100 sm:p-8 dark:bg-stone-900 dark:ring-stone-800">
                    <h2 class="font-display text-xl font-semibold text-stone-900 dark:text-white">{{ __('About you') }}</h2>

                    <flux:input wire:model="applicantName" :label="__('Name')" autocomplete="name" required />

                    <div class="grid gap-4 sm:grid-cols-2">
                        <flux:input wire:model="applicantEmail" type="email" :label="__('Email')" autocomplete="email" required />
                        <flux:input wire:model="applicantPhone" type="tel" :label="__('Phone')" autocomplete="tel" required />
                    </div>

                    <div class="grid gap-4 sm:grid-cols-3">
                        <flux:input wire:model="applicantPostalCode" :label="__('Postal Code')" autocomplete="postal-code" />
                        <flux:input wire:model="applicantCity" :label="__('City')" autocomplete="address-level2" field:class="sm:col-span-2" required />
                    </div>
                </div>

                <div class="flex flex-col gap-4 rounded-[2rem] bg-white p-6 shadow-sm ring-1 ring-amber-100 sm:p-8 dark:bg-stone-900 dark:ring-stone-800">
                    <h2 class="font-display text-xl font-semibold text-stone-900 dark:text-white">{{ __('Your home') }}</h2>

                    <flux:radio.group wire:model="housingType" :label="__('Housing Type')" variant="segmented">
                        <flux:radio value="apartment" :label="__('Apartment')" />
                        <flux:radio value="house" :label="__('House')" />
                    </flux:radio.group>

                    <div class="flex flex-col gap-3">
                        <flux:switch wire:model="hasGarden" :label="__('I have a garden or yard')" align="left" />
                        <flux:switch wire:model="hasChildren" :label="__('There are children at home')" align="left" />
                    </div>

                    <flux:input wire:model="otherAnimals" :label="__('Other animals at home')" :placeholder="__('E.g. one cat, 5 years old')" />

                    <flux:textarea wire:model="message" :label="__('Why do you want to adopt?')" rows="5" required />
                </div>

                <div class="flex flex-col gap-4 rounded-[2rem] bg-white p-6 shadow-sm ring-1 ring-amber-100 sm:p-8 dark:bg-stone-900 dark:ring-stone-800">
                    <flux:field variant="inline">
                        <flux:checkbox wire:model="hasConsented" />
                        <flux:label>
                            <span>
                                {!! __('I agree that :shelter uses this data to assess my application, as described in the :policy.', [
                                    'shelter' => e($pet->shelter->name),
                                    'policy' => '<a href="'.e(route('privacy-policy')).'" target="_blank" class="font-semibold text-orange-600 underline dark:text-orange-300">'.e(__('Privacy Policy')).'</a>',
                                ]) !!}
                            </span>
                        </flux:label>
                        <flux:error name="hasConsented" />
                    </flux:field>

                    <div class="flex justify-end">
                        <button type="submit" class="rounded-full bg-orange-500 px-7 py-3 font-display text-lg font-semibold text-white shadow-sm transition hover:-translate-y-0.5 hover:bg-orange-600 disabled:opacity-60" wire:loading.attr="disabled">
                            💌 {{ __('Send application') }}
                        </button>
                    </div>
                </div>
            </form>
        @endif
    </section>
</div>
