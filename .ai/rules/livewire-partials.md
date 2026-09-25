---
paths:
  - resources/views/livewire/partials/public-pet-card.blade.php
---

# Livewire Partials

## Public foster badge uses only the wing flag
The public card and details modal show "In a foster family" via Pet::isInFosterFamily(). Welcome, PartnerShelterShow and ShowsPublicPets::selectedPet eager-load only 'cage:id,wing_id' and 'cage.wing:id,is_foster' for it. Never render the cage code (the family's name), the volunteer or anything else about the cage on public pages.
