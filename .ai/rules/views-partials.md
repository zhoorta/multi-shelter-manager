---
paths:
  - 'app/Livewire/PartnerShelterShow.php,app/Livewire/Pets/PetShow.php,app/Models/Pet.php,app/Http/Controllers/ShelterFeedController.php,resources/views/partials/head.blade.php'
---

# Views Partials

## Social sharing: pet links are shelters/{shelter}?animal={id}
There is no standalone public pet page. Pet::publicUrl() returns route('shelters.show', [shelter, 'animal' => id]) only when the portal is enabled AND the pet passes publishedToPortal(); otherwise it returns null. PartnerShelterShow::mount() resolves ?animal= through $shelter->publishedPets() (unpublished or other-shelter ids are ignored), counts the view, and switches title/description/og:image to the pet. It passes canonicalUrl so og:url keeps ?animal= (Facebook scrapes og:url, so without it the preview would show the shelter). The modal is opened from the view with x-init + $nextTick, not Flux::modal()->show() in mount, which can fire before the modal listens. Pet::shareCaption() builds the caption shared by PetShow's "share-pet" modal (only for adoptable, available, living pets) and the RSS feed (route shelters.feed; linked only via <link rel="alternate"> on the shelter page, deliberately not shown in the share modal). This is phase 1 of the social posting plan (phase 2 = Meta Graph API).
