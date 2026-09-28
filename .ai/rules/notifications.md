---
paths:
  - 'app/Console/Commands/SendVaccinationDueNotifications.php,app/Models/PetVaccine.php,app/Notifications/VaccinationDueNotification.php'
---

# Notifications

## Vaccination due-date reminder command owns notification_date/notification_recipients
App\Console\Commands\SendVaccinationDueNotifications (signature app:send-vaccination-due-notifications, scheduled daily in routes/console.php) is the only writer of pet_vaccines.notification_date and notification_recipients — previously unused columns from the base migration. Per shelter: skip entirely if no User has vaccination_notifications=true (loop continues to the next shelter, doesn't abort); otherwise select PetVaccine rows for that shelter's pets that are ->pending() (see views-livewire-pets-views-livewire-pets.md), notification_date NULL, and due_date within [today, today+7]; email all subscribed users one App\Notifications\VaccinationDueNotification per shelter; then bulk-update those PetVaccine rows via the query builder (not Eloquent saves) with notification_date=now() and notification_recipients=json_encode($recipients->pluck('email')). PetVaccine casts notification_recipients as 'array', but that cast only applies on Eloquent read/write — the command's bulk ->update() must json_encode() manually. A logged dose with its own due_date (next booster) IS included while pending, i.e. until a later dose of that vaccine is logged.
