---
paths:
  - 'app/Livewire/Admin/ManageUsers.php,resources/views/livewire/admin/manage-users.blade.php'
---

# Livewire Admin

## ManageUsers is now paginated (20/page), matches ManagePets/ManageVolunteers
ManageUsers now `use WithPagination`; `users()` returns `LengthAwarePaginator<int, User>` via `->paginate(20)` instead of a plain `->get()` Collection. `updatedFilterShelterId()` (which only did `unset($this->users)`) was replaced with `updatingFilterShelterId(): void { $this->resetPage(); }`, mirroring ManagePets/ManageVolunteers' `updating*` → `resetPage()` convention (see [[pets-views-livewire-volunteers]] and [[views-livewire-volunteers]]). Blade renders `<flux:pagination :paginator="$this->users" class="!border-t-0 !pt-0" />` in its own `px-6 py-3` div as a sibling AFTER the table's bordered wrapper closes, not nested inside it — same placement as manage-pets.blade.php/manage-volunteers.blade.php.
