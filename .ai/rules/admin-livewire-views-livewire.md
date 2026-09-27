---
paths:
  - 'app/Livewire/Admin/PlatformOverview.php,resources/views/livewire/admin/platform-overview.blade.php,app/Livewire/Dashboard.php,resources/views/livewire/dashboard.blade.php'
---

# Admin Livewire Views Livewire

## Admins get the platform overview on the dashboard, totals only
Admins have no shelter (current_shelter_id null), so the shelter dashboard was empty for them. dashboard.blade.php renders <livewire:admin.platform-overview /> for is_admin, and Dashboard::mount() returns early for admins with empty collections. App\Livewire\Admin\PlatformOverview (abort_unless is_admin) shows platform counters (shelters, users logged in within 30 days via users.last_login, pets in care, this year's approved non-returned adoptions — same rules as the shelter dashboard counters), a shelters table ordered by least recent users.last_login first (amber when never/older than 30 days) with last pets.updated_at, "Setup to complete" (shelters without species / cages / users, and species enabled in shelter_species with no breeds), and invitations not accepted (non-admin users with last_login null). Per product decision it shows totals only: never pet cards, pet names or adopters/sponsors.
