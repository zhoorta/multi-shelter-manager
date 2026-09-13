---
paths:
  - resources/views/livewire/volunteers/volunteer-show.blade.php
---

# Livewire Volunteers

## volunteer-show subsection headings also dropped to default size
Superseding the "ask before changing" note in [[resources-views-livewire-pets]]: volunteer-show.blade.php's 4 subsection headings (Identification, Contacts, Volunteering, Notes) were changed from `<flux:heading size="lg">` to bare `<flux:heading>` (text-sm), matching pet-show.blade.php's subsection headings and the "Photos" `flux:label` size convention. The page-title heading (`size="xl"`, volunteer's name) is untouched.
