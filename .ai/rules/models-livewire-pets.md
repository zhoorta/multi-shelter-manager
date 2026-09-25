---
paths:
  - 'app/Livewire/AdoptionApplicationForm.php,app/Livewire/Pets/ManageAdoptionApplications.php,app/Models/AdoptionApplication.php,app/Livewire/Pets/AdoptionForm.php'
---

# Models Livewire Pets

## Adoption applications live apart from Adoption; approval goes through AdoptionForm
Public applications (route adoption-applications.create, adopt/{petRef}) are AdoptionApplication rows, never Adoption rows: an open Adoption makes Pet::determineStatus() return 'adopted'. The pet is resolved in mount() with withoutGlobalScope('shelter')->publishedToPortal(), not route binding, so staff of other shelters can apply too. Anti-spam without third parties or cookies: honeypot `website`, #[Locked] renderedAt with MINIMUM_FILL_SECONDS (both silently fake success), RateLimiter per IP inside the action (route throttle doesn't cover /livewire/update), one pending application per email per pet. The applicant gets no e-mail on purpose (the form can't be used to send mail). Shelter e-mail goes to members with shelter_users.adoption_application_notifications and role manager/staff only (never viewers); send failures are report()ed so the saved application isn't lost. Approve = link to pets.adopt?application={id}; AdoptionForm prefills and marks it approved + adoption_id on create. Retention: MassPrunable, RETENTION_MONTHS=6 after updated_at (trashed included), model:prune scheduled daily in routes/console.php; the privacy policy (all 10 locales) states 6 months, so change both together.
