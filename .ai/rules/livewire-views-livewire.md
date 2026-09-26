---
paths:
  - 'app/Livewire/Dashboard.php,resources/views/livewire/dashboard.blade.php'
---

# Livewire Views Livewire

## Dashboard "contact the site administrator" warnings link config('app.contact_email')
The missing-species and missing-breeds warnings add an "Email: <mailto>" line with config('app.contact_email') (CONTACT_EMAIL → PRIVACY_CONTACT_EMAIL → MAIL_FROM_ADDRESS), passed from Dashboard::render() as $administratorEmail; the line is omitted when it's null. No separate ADMIN_EMAIL setting on purpose: the platform contact is the site administrator. The facilities warning points to the shelter manager instead, so it has no e-mail.
