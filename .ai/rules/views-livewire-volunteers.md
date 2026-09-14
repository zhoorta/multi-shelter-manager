---
paths:
  - 'app/Livewire/Volunteers/ManageVolunteers.php,resources/views/livewire/volunteers/manage-volunteers.blade.php'
---

# Views Livewire Volunteers

## ManageVolunteers: search + 3 dropdown filters, no pagination
ManageVolunteers gained search/speciesFilter/dayFilter/activityFilter public string props (all wire:model.live, no updating* resetPage hooks since it stays a plain ->get() Collection, not paginated like ManagePets). search does a multi-column `where(fn => orWhere(...))` LIKE across name/phone/email/tin/notes. speciesFilter and activityFilter use `whereHas('species'|'activities', fn => where('<table>.id', $filter))` against the pivots. dayFilter uses `whereHas('availabilities', fn => where('day_index', $filter))` — no need to also check mornings/afternoons since VolunteerForm only ever creates/keeps an availability row when at least one is true (see [[volunteers]]). Two new #[Computed] props, `species()` and `activities()`, list ALL rows (`Species::query()->orderBy('name')->get()`, `Activity::query()->orderBy('name')->get()`) to populate the filter dropdowns, mirroring VolunteerForm's identical computed props. Blade filter row uses flux:select with both :placeholder="__('All')" and :label (e.g. __('Sector'), __('Day of the Week'), __('Activities')) since 3 selects sit side by side and need distinct labels, unlike ManagePets' single unlabeled status select.

## ManageVolunteers: now paginated (20/page), matches ManagePets
Updated from the earlier version of this rule: ManageVolunteers now uses WithPagination + returns LengthAwarePaginator (was a plain ->get() Collection), with updatingSearch/updatingSpeciesFilter/updatingDayFilter/updatingActivityFilter each calling resetPage(), mirroring ManagePets exactly. paginate(20). Blade renders <flux:pagination :paginator="$this->volunteers" /> in a `px-6 py-3` div after the table, same placement as manage-pets.blade.php. The search input got :label="__('Search')" added (new "Search" lang key) purely so it vertically aligns with the three filter flux:selects, which all carry :label (Sector/Day of the Week/Activities) — an unlabeled flux:input renders shorter than a labeled flux:select in the same sm:items-center row.
