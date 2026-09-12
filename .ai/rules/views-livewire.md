---
paths:
  - 'resources/views/livewire/**/*.blade.php'
---

# Views Livewire

## Flux input: use field:class, not class, to size the field in a flex/grid row
A plain `class="..."` on `<flux:input>`/`<flux:select>`/etc. only reaches the inner input wrapper div (data-flux-input), not the outer `<ui-field data-flux-field>` element that is the actual grid/flex item when the input has a label. To span/size the field itself inside a `grid grid-cols-N` or `flex` row (e.g. `field:class="col-span-2"`), use the `field:` attribute prefix — Flux::attributesAfter('field:', ...) strips the prefix and forwards it to flux:field. Plain `class` silently has no layout effect in this case (see adoption-form.blade.php's Postal Code / City row).
