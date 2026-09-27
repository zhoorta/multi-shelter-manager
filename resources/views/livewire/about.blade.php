@php
    $cards = [
        ['icon' => '💛', 'tint' => 'bg-orange-100 dark:bg-orange-950/60', 'title' => __('Free and nonprofit'), 'text' => __('Free for every shelter, with no commercial goals: no ads, no selling of data. Every feature exists to help shelters and their animals.')],
        ['icon' => '🏡', 'tint' => 'bg-sky-100 dark:bg-sky-950/60', 'title' => __('Built for shelters'), 'text' => __('Shelters use it to manage their animals, spaces, foster families, vaccinations, adoptions, sponsorships, volunteers and members, and to follow their work in reports, each one keeping its own data private.')],
        ['icon' => '🐾', 'tint' => 'bg-pink-100 dark:bg-pink-950/60', 'title' => __('One place to adopt'), 'text' => __('Animals from every partner shelter are shown together, and families can apply to adopt online, straight to the shelter caring for the animal.')],
    ];

    $screenshots = [
        ['image' => $screenshotsPath.'/dashboard.webp', 'caption' => __('Dashboard')],
        ['image' => $screenshotsPath.'/animal-record.webp', 'caption' => __('Animal record')],
        ['image' => $screenshotsPath.'/reports.webp', 'caption' => __('Reports')],
    ];
@endphp

<div class="relative overflow-hidden">
    <div aria-hidden="true" class="pointer-events-none absolute -top-24 -left-24 size-96 rounded-full bg-orange-200/60 blur-3xl dark:bg-orange-900/30"></div>
    <div aria-hidden="true" class="pointer-events-none absolute top-40 -right-24 size-96 rounded-full bg-pink-200/60 blur-3xl dark:bg-pink-900/20"></div>

    <section class="relative mx-auto max-w-5xl px-4 py-16 sm:px-6">
        <div class="flex flex-col items-center gap-3 text-center">
            <span class="inline-flex items-center gap-2 rounded-full bg-white px-4 py-1.5 text-sm font-semibold text-orange-600 shadow-sm ring-1 ring-orange-100 dark:bg-stone-900 dark:text-orange-300 dark:ring-stone-800">
                🐾 {{ __('About the project') }}
            </span>
            <h1 class="font-display text-5xl font-bold tracking-tight text-stone-900 dark:text-white">{{ __('About :app', ['app' => config('app.name')]) }}</h1>
            <p class="max-w-2xl text-lg leading-relaxed text-stone-500 dark:text-stone-400">
                {{ __(':app is a nonprofit project that helps animal shelters organise their daily work and find a loving home for the animals in their care.', ['app' => config('app.name')]) }}
            </p>
        </div>

        <div class="mt-12 grid gap-6 sm:grid-cols-3">
            @foreach ($cards as $card)
                <article class="flex flex-col overflow-hidden rounded-[2rem] bg-white shadow-sm ring-1 ring-amber-100 dark:bg-stone-900 dark:ring-stone-800">
                    <div class="flex items-center gap-3 p-6 {{ $card['tint'] }}">
                        <span class="flex size-12 shrink-0 items-center justify-center rounded-2xl bg-white text-2xl shadow-sm dark:bg-stone-900">{{ $card['icon'] }}</span>
                        <h2 class="font-display text-xl leading-tight font-semibold text-stone-900 dark:text-white">{{ $card['title'] }}</h2>
                    </div>
                    <p class="p-6 leading-relaxed text-stone-600 dark:text-stone-300">{{ $card['text'] }}</p>
                </article>
            @endforeach
        </div>

        <div class="mt-12 flex flex-col items-center gap-3 text-center">
            <h2 class="font-display text-3xl font-semibold text-stone-900 dark:text-white">{{ __('A look inside') }}</h2>
            <p class="max-w-xl text-stone-500 dark:text-stone-400">{{ __('The private area where each shelter organises its daily work.') }}</p>
        </div>

        <div
            x-data="{ index: 0, open: false, screenshots: @js(collect($screenshots)->map(fn (array $screenshot): array => ['url' => asset($screenshot['image']), 'caption' => $screenshot['caption']])) }"
            x-on:modal-show.window="$event.detail.name === 'about-screenshots' && (open = true)"
            x-on:modal-close.window="(!$event.detail.name || $event.detail.name === 'about-screenshots') && (open = false)"
            x-on:keydown.left.window="open && (index = (index - 1 + screenshots.length) % screenshots.length)"
            x-on:keydown.right.window="open && (index = (index + 1) % screenshots.length)"
        >
            <div class="mt-8 grid gap-6 sm:grid-cols-3">
                @foreach ($screenshots as $screenshot)
                    <figure class="flex flex-col gap-3">
                        <a
                            href="{{ asset($screenshot['image']) }}"
                            x-on:click.prevent="index = {{ $loop->index }}; $dispatch('modal-show', { name: 'about-screenshots' })"
                            class="cursor-zoom-in overflow-hidden rounded-2xl shadow-sm ring-1 ring-amber-100 transition hover:-translate-y-0.5 dark:ring-stone-800"
                        >
                            <img src="{{ asset($screenshot['image']) }}" alt="{{ $screenshot['caption'] }}" width="1920" height="1200" loading="lazy" class="w-full">
                        </a>
                        <figcaption class="text-center font-semibold text-stone-700 dark:text-stone-300">{{ $screenshot['caption'] }}</figcaption>
                    </figure>
                @endforeach
            </div>

            <flux:modal name="about-screenshots" variant="bare" class="h-dvh w-screen max-w-none p-0">
                <div class="relative flex h-full w-full flex-col items-center justify-center gap-4 bg-black/70 px-4 py-16">
                    <div class="absolute top-4 end-4 z-20">
                        <flux:modal.close>
                            <flux:button variant="ghost" icon="x-mark" size="sm" :aria-label="__('Close')" class="text-white! hover:text-white/70!" />
                        </flux:modal.close>
                    </div>

                    <button
                        type="button"
                        x-on:click="index = (index - 1 + screenshots.length) % screenshots.length"
                        class="absolute top-1/2 start-4 z-10 -translate-y-1/2 text-white/80 hover:text-white"
                        aria-label="{{ __('Previous photo') }}"
                    >
                        <flux:icon name="chevron-left" class="size-10" />
                    </button>

                    <img :src="screenshots[index].url" :alt="screenshots[index].caption" class="max-w-full rounded-xl object-contain" style="max-height: calc(100dvh - 9rem)">
                    <p x-text="screenshots[index].caption" class="font-semibold text-white"></p>

                    <button
                        type="button"
                        x-on:click="index = (index + 1) % screenshots.length"
                        class="absolute top-1/2 end-4 z-10 -translate-y-1/2 text-white/80 hover:text-white"
                        aria-label="{{ __('Next photo') }}"
                    >
                        <flux:icon name="chevron-right" class="size-10" />
                    </button>

                    <div class="absolute bottom-4 z-10 flex gap-1.5">
                        <template x-for="(screenshot, i) in screenshots" :key="i">
                            <button
                                type="button"
                                x-on:click="index = i"
                                class="h-2 w-2 rounded-full"
                                :class="i === index ? 'bg-white' : 'bg-white/40'"
                                :aria-label="screenshot.caption"
                            ></button>
                        </template>
                    </div>
                </div>
            </flux:modal>
        </div>

        @if ($contactEmail)
            <div class="mt-12 flex flex-col items-center gap-4 rounded-[2rem] bg-white p-8 text-center shadow-sm ring-1 ring-amber-100 sm:p-10 dark:bg-stone-900 dark:ring-stone-800">
                <h2 class="font-display text-3xl font-semibold text-stone-900 dark:text-white">{{ __('Does your shelter want to join?') }}</h2>
                <p class="max-w-xl text-stone-500 dark:text-stone-400">{{ __('Joining is free. We set up your shelter\'s account and help you bring in the animal records you already have, so you don\'t have to start from scratch.') }}</p>
                <div class="flex max-w-full flex-wrap items-center justify-center gap-4">
                    <a href="mailto:{{ $contactEmail }}" class="max-w-full truncate rounded-full bg-orange-500 px-7 py-3.5 font-display text-lg font-semibold text-white shadow-lg shadow-orange-300/60 transition hover:-translate-y-0.5 hover:bg-orange-600 dark:shadow-none">
                        ✉️ {{ $contactEmail }}
                    </a>
                    @if ($userGuideUrl)
                        <a href="{{ $userGuideUrl }}" target="_blank" rel="noopener" class="rounded-full bg-white px-7 py-3.5 font-display text-lg font-semibold text-stone-700 shadow-sm ring-1 ring-amber-200 transition hover:-translate-y-0.5 dark:bg-stone-900 dark:text-stone-200 dark:ring-stone-700">
                            📘 {{ __('User guide (PDF)') }}
                        </a>
                    @endif
                </div>
                <p class="max-w-xl text-sm text-stone-500 dark:text-stone-400">{{ __('Have a suggestion or want to help the project? Write to us as well.') }}</p>
            </div>
        @endif
    </section>
</div>
