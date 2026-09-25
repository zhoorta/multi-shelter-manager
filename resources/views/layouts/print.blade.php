<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
        {{-- Printouts are always light: undo the dark class that @fluxAppearance adds, without changing the saved preference. --}}
        <script>
            (() => {
                const keepLight = () => {
                    // Only touch the class when needed: remove() rewrites the attribute and would re-trigger the observer forever.
                    if (document.documentElement.classList.contains('dark')) {
                        document.documentElement.classList.remove('dark');
                    }
                };
                keepLight();
                new MutationObserver(keepLight).observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
            })();
        </script>
    </head>
    <body class="min-h-screen bg-white text-neutral-900 antialiased">
        {{ $slot }}

        @fluxScripts
    </body>
</html>
