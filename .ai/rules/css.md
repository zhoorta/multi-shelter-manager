---
paths:
  - resources/css/app.css
---

# Css

## neutral-* and zinc-* are remapped to the warm stone palette; accent is orange
To give the backoffice the public page's warm look without touching ~1000 view classes, app.css @theme redefines --color-neutral-* and --color-zinc-* (Flux's gray) to the Tailwind stone hex values, and the Flux accent is orange-500/600 (orange-400 in dark) instead of emerald. So `neutral-500` renders stone-500 app-wide — this is intentional. Layout chrome: page bg-amber-50 / dark:bg-stone-950, sidebar/header bg-white border-amber-100 / dark:bg-stone-900 border-stone-800; logo badge is rounded-xl bg-orange-400. New views may keep using neutral-* (it's warm) and flux variant="primary" for orange buttons.
