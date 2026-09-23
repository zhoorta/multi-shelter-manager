<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-amber-50 text-stone-800 antialiased dark:bg-stone-950 dark:text-stone-100">
        <header class="sticky top-0 z-30 border-b border-amber-100 bg-amber-50/85 backdrop-blur dark:border-stone-800 dark:bg-stone-950/85">
            <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3 sm:px-6">
                <a href="{{ route('home') }}" class="flex items-center gap-2" wire:navigate>
                    <span class="flex size-10 items-center justify-center rounded-2xl bg-orange-400 text-white shadow-md shadow-orange-200 dark:shadow-none">
                        <x-app-logo-icon class="size-6 fill-current" />
                    </span>
                    <span class="font-display text-2xl font-semibold tracking-tight text-stone-900 dark:text-white">{{ config('app.name') }}</span>
                </a>

                <nav class="flex items-center gap-2">
                    @if (config('app.public_portal_enabled'))
                        <a href="{{ route('home') }}#adopt" class="hidden rounded-full px-4 py-2 text-sm font-semibold text-stone-600 transition hover:bg-amber-100 hover:text-stone-900 sm:inline-block dark:text-stone-300 dark:hover:bg-stone-800 dark:hover:text-white">
                            {{ __('Adopt') }}
                        </a>
                        <a href="{{ route('shelters') }}" class="hidden rounded-full px-4 py-2 text-sm font-semibold text-stone-600 transition hover:bg-amber-100 hover:text-stone-900 sm:inline-block dark:text-stone-300 dark:hover:bg-stone-800 dark:hover:text-white" wire:navigate>
                            {{ __('Shelters') }}
                        </a>
                    @endif
                    @auth
                        <a href="{{ route('dashboard') }}" class="rounded-full bg-stone-900 px-5 py-2 text-sm font-semibold text-white shadow-sm transition hover:-translate-y-0.5 hover:bg-stone-700 dark:bg-white dark:text-stone-900 dark:hover:bg-stone-200" wire:navigate>
                            {{ __('Dashboard') }}
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="rounded-full bg-stone-900 px-5 py-2 text-sm font-semibold text-white shadow-sm transition hover:-translate-y-0.5 hover:bg-stone-700 dark:bg-white dark:text-stone-900 dark:hover:bg-stone-200" wire:navigate>
                            {{ __('Shelter area') }}
                        </a>
                    @endauth
                </nav>
            </div>
        </header>

        <main>
            {{ $slot }}
        </main>

        <footer class="border-t border-amber-100 bg-white/60 dark:border-stone-800 dark:bg-stone-900/60">
            <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-2 px-4 py-6 text-sm text-stone-500 sm:flex-row sm:px-6 dark:text-stone-400">
                <p>&copy; {{ now()->year }} {{ config('app.name') }} &middot; {{ __('Made with love for animals') }} 🐾</p>
                <nav class="flex items-center gap-4">
                    <a href="{{ route('privacy-policy') }}" class="font-medium hover:text-orange-500" wire:navigate>{{ __('Privacy Policy') }}</a>
                    <a href="{{ route('login') }}" class="font-medium hover:text-orange-500" wire:navigate>{{ __('Shelter area') }}</a>
                </nav>
            </div>
        </footer>

        @fluxScripts
    </body>
</html>
