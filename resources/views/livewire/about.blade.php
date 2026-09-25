@php
    $cards = [
        ['icon' => '💛', 'tint' => 'bg-orange-100 dark:bg-orange-950/60', 'title' => __('Nonprofit'), 'text' => __('We have no commercial goals: no ads, no selling of data. Every feature exists to help shelters and their animals.')],
        ['icon' => '🏡', 'tint' => 'bg-sky-100 dark:bg-sky-950/60', 'title' => __('Built for shelters'), 'text' => __('Shelters use it to manage their animals, spaces, foster families, vaccinations, adoptions, sponsorships, volunteers and members, and to follow their work in reports, each one keeping its own data private.')],
        ['icon' => '🐾', 'tint' => 'bg-pink-100 dark:bg-pink-950/60', 'title' => __('One place to adopt'), 'text' => __('Animals from every partner shelter are shown together, and families can apply to adopt online, straight to the shelter caring for the animal.')],
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

        @if ($contactEmail)
            <div class="mt-12 flex flex-col items-center gap-4 rounded-[2rem] bg-white p-8 text-center shadow-sm ring-1 ring-amber-100 sm:p-10 dark:bg-stone-900 dark:ring-stone-800">
                <h2 class="font-display text-3xl font-semibold text-stone-900 dark:text-white">{{ __('Get in touch') }}</h2>
                <p class="max-w-xl text-stone-500 dark:text-stone-400">{{ __('Do you run a shelter and want to join, have a suggestion or want to help the project? Send us an e-mail.') }}</p>
                <a href="mailto:{{ $contactEmail }}" class="max-w-full truncate rounded-full bg-orange-500 px-7 py-3.5 font-display text-lg font-semibold text-white shadow-lg shadow-orange-300/60 transition hover:-translate-y-0.5 hover:bg-orange-600 dark:shadow-none">
                    ✉️ {{ $contactEmail }}
                </a>
            </div>
        @endif
    </section>
</div>
