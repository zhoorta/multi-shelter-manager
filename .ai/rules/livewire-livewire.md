---
paths:
  - 'resources/views/partials/head.blade.php,app/Livewire/Welcome.php,app/Livewire/About.php,app/Livewire/AdoptionApplicationForm.php'
---

# Livewire Livewire

## Public SEO: platform-first home/about, form pages noindex
User decision 2026-09-27: the home page title/description, the default share image (public/images/share.png, 1200x630, og:image:width/height emitted only when it's used) and the About page lead with the free shelter-management platform; adoption SEO lives on the pet and shelter pages. Home emits WebSite + Organization JSON-LD in an @graph. og:locale maps the app locale to a territory (pt → pt_PT, …) in head.blade.php; add a locale there too when adding one. The adoption application form (adopt/{petRef}) is `noindex, follow`.
