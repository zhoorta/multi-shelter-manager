---
paths:
  - resources/views/layouts/app.blade.php
---

# Views Layouts

## Backoffice content area has a fixed blurred-blob backdrop
layouts/app.blade.php puts a `pointer-events-none fixed inset-0 -z-10` layer (data-test="app-backdrop") inside <flux:main> with the public pages' orange/pink/sky blur-3xl blobs. It is fixed + negative z rather than `relative overflow-hidden` on flux:main so it doesn't break sticky elements or clip dropdowns; the opaque sidebar/header hide it, so it only shows behind lists/forms. Views need no changes — keep content on bg-white / dark:bg-stone-900 cards for readability.
