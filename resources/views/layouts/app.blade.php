<x-layouts::app.sidebar :title="$title ?? null">
    <flux:main>
        <div aria-hidden="true" data-test="app-backdrop" class="pointer-events-none fixed inset-0 -z-10 overflow-hidden">
            <div class="absolute -top-24 left-1/4 size-96 rounded-full bg-orange-200/60 blur-3xl dark:bg-orange-900/30"></div>
            <div class="absolute top-1/3 -right-24 size-96 rounded-full bg-pink-200/60 blur-3xl dark:bg-pink-900/20"></div>
            <div class="absolute bottom-0 left-1/2 size-72 rounded-full bg-sky-200/50 blur-3xl dark:bg-sky-900/20"></div>
        </div>

        {{ $slot }}
    </flux:main>
</x-layouts::app.sidebar>
