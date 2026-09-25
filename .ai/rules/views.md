---
paths:
  - 'resources/views/**/*.blade.php'
---

# Views

## Don't mix inline @php(...) with @php ... @endphp blocks in one view
Blade's compiler mis-pairs an inline `@php($x = ...)` that appears before a `@php ... @endphp` block in the same template, producing "syntax error, unexpected token endif" in the compiled view (hit in resources/views/livewire/welcome.blade.php). Use the block form `@php ... @endphp` everywhere in a view that has any block.

## New Tailwind classes need a rebuild; never hide security-relevant fields with utilities
Local dev usually serves the compiled public/build CSS (no public/hot), so a Tailwind class not used anywhere before (e.g. py-14, -left-[9999px]) has no effect until `npm run build` / `npm run dev`. Prefer classes already in use, and tell the user to rebuild after adding new ones. Anything whose hiding matters for behaviour (e.g. the honeypot in adoption-application-form.blade.php) uses an inline style so it never depends on the build.
