---
paths:
  - 'app/Livewire/PartnerShelterShow.php,app/Livewire/PublicPetShow.php,app/Livewire/Pets/PetShow.php,app/Models/Pet.php,app/Http/Controllers/ShelterFeedController.php,resources/views/partials/head.blade.php,resources/views/livewire/partials/public-pet-profile.blade.php'
---

# Views Partials

## Public pet page: animais/{petId}/{slug} (App\Livewire\PublicPetShow)
Every published pet has its own page, route 'animals.show' = animais/{petId}/{slug?}. Only the id resolves the pet; the slug (Pet::publicSlug(): name + species + shelter city + region, repeated parts dropped; titles use Pet::publicLocation()) is descriptive, and a missing or stale slug 301-redirects to Pet::publicPageUrl(). The route must not start with /pets (robots.txt disallows /pets). Published pets (publishedToPortal) get the full page and count a view; pets adopted after being published (publish_to_portal, status 'adopted', alive, shelter not deleted) keep a "found a family" page with `noindex` (layoutData 'noindex' => true, OG tags stay) and no adoption/contact actions; anything else is 404. Pet::publicUrl() (share modal in PetShow) returns publicPageUrl() only when the portal is on and the pet is published. The RSS feed's item <link>/<guid> is the pet page URL. (The old shelters/{shelter}?animal={id} share links were dropped before launch, never redirected.) The public card is an <a href> to the page (crawlable, new tabs) whose plain click opens the modal instead. The modal and the page share livewire/partials/public-pet-profile.blade.php. The sitemap lists every published pet. Pages without an image fall back to public/images/share.png (1200x630) for og:image.
