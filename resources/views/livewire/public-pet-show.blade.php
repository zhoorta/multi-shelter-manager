<div class="relative overflow-hidden">
    <div aria-hidden="true" class="pointer-events-none absolute -top-24 -left-24 size-96 rounded-full bg-orange-200/60 blur-3xl dark:bg-orange-900/30"></div>
    <div aria-hidden="true" class="pointer-events-none absolute top-40 -right-24 size-96 rounded-full bg-pink-200/60 blur-3xl dark:bg-pink-900/20"></div>

    <section class="relative mx-auto max-w-3xl px-4 py-12 sm:px-6">
        <nav aria-label="{{ __('Breadcrumb') }}" class="flex flex-wrap items-center gap-1 text-sm font-semibold text-stone-500 dark:text-stone-400">
            <a href="{{ route('home') }}" class="transition hover:text-orange-500" wire:navigate>{{ config('app.name') }}</a>
            <span aria-hidden="true">›</span>
            <a href="{{ route('shelters.show', $pet->shelter) }}" class="transition hover:text-orange-500" wire:navigate>{{ $pet->shelter->name }}</a>
            <span aria-hidden="true">›</span>
            <span class="text-stone-700 dark:text-stone-200">{{ $pet->name }}</span>
        </nav>

        @if ($isAdopted)
            <div class="mt-6 flex flex-col items-center gap-3 rounded-[2rem] bg-pink-100 px-6 py-8 text-center dark:bg-pink-950/40">
                <span class="text-5xl">❤️</span>
                <p class="font-display text-2xl font-semibold text-stone-900 dark:text-white">{{ __(':name has already found a family!', ['name' => $pet->name]) }}</p>
                <a href="{{ route('shelters.show', $pet->shelter) }}" class="rounded-full bg-orange-500 px-5 py-2 text-sm font-semibold text-white shadow-sm transition hover:-translate-y-0.5 hover:bg-orange-600" wire:navigate>
                    {{ __('Meet other animals looking for a home') }} →
                </a>
            </div>
        @endif

        <div class="mt-6 overflow-hidden rounded-[2rem] bg-white shadow-sm ring-1 ring-amber-100 dark:bg-stone-900 dark:ring-stone-800">
            @include('livewire.partials.public-pet-profile', ['pet' => $pet, 'headingTag' => 'h1', 'isAdopted' => $isAdopted])
        </div>
    </section>
</div>
