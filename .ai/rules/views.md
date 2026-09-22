---
paths:
  - 'resources/views/**/*.blade.php'
---

# Views

## Don't mix inline @php(...) with @php ... @endphp blocks in one view
Blade's compiler mis-pairs an inline `@php($x = ...)` that appears before a `@php ... @endphp` block in the same template, producing "syntax error, unexpected token endif" in the compiled view (hit in resources/views/livewire/welcome.blade.php). Use the block form `@php ... @endphp` everywhere in a view that has any block.
