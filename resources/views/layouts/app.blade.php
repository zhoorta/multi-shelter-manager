<x-layouts::app.sidebar :title="$title ?? null">
    <flux:main>
        <div aria-hidden="true" data-test="app-backdrop" class="pointer-events-none fixed inset-0 -z-10 overflow-hidden">
            <div class="absolute -top-24 left-1/4 size-96 rounded-full bg-orange-200/60 blur-3xl dark:bg-orange-900/30"></div>
            <div class="absolute top-1/3 -right-24 size-96 rounded-full bg-pink-200/60 blur-3xl dark:bg-pink-900/20"></div>
            <div class="absolute bottom-0 left-1/2 size-72 rounded-full bg-sky-200/50 blur-3xl dark:bg-sky-900/20"></div>
        </div>

        @if (session()->has(\App\Http\Middleware\ReadOnlyWhileImpersonating::SESSION_KEY))
            <form method="POST" action="{{ route('impersonation.stop') }}" data-test="impersonation-banner" class="mb-4 flex flex-wrap items-center justify-between gap-2 rounded-lg border border-amber-300 bg-amber-100 px-4 py-2 text-sm text-amber-900 dark:border-amber-700 dark:bg-amber-900/40 dark:text-amber-100">
                @csrf
                <span>{{ __('Viewing as :name (read-only)', ['name' => auth()->user()->name]) }}</span>
                <flux:button type="submit" size="sm" variant="filled">{{ __('Stop viewing') }}</flux:button>
            </form>
        @endif

        @if (\App\Actions\Demo\DemoSandbox::isDemoUser(auth()->user()))
            <div data-test="demo-banner" class="mb-4 rounded-lg border border-sky-300 bg-sky-100 px-4 py-2 text-sm text-sky-900 dark:border-sky-700 dark:bg-sky-900/40 dark:text-sky-100">
                {{ __('This is a demo with sample data. Everything here is deleted :minutes minutes after you started, so do not enter personal data.', ['minutes' => config('demo.ttl_minutes')]) }}
                @if (config('demo.contact'))
                    {{ __('Questions? :contact', ['contact' => config('demo.contact')]) }}
                @endif
            </div>
        @endif

        {{ $slot }}
    </flux:main>
</x-layouts::app.sidebar>
