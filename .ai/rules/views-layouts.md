---
paths:
  - resources/views/layouts/app.blade.php
  - resources/views/layouts/print.blade.php
---

# Views Layouts

## Backoffice content area has a fixed blurred-blob backdrop
layouts/app.blade.php puts a `pointer-events-none fixed inset-0 -z-10` layer (data-test="app-backdrop") inside <flux:main> with the public pages' orange/pink/sky blur-3xl blobs. It is fixed + negative z rather than `relative overflow-hidden` on flux:main so it doesn't break sticky elements or clip dropdowns; the opaque sidebar/header hide it, so it only shows behind lists/forms. Views need no changes — keep content on bg-white / dark:bg-stone-900 cards for readability.

## Print layout always renders light
@fluxAppearance (in partials.head) adds the dark class from the user's saved appearance, so print pages that reuse screen markup with dark: classes printed dark cards. layouts/print.blade.php removes the class with an inline script plus a MutationObserver. Don't call Flux.applyAppearance('light'): it overwrites the user's saved preference. Keep the classList.contains('dark') guard, because classList.remove() rewrites the attribute even when nothing changes and the observer would loop forever (the page hangs).
