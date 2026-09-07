---
paths:
  - 'app/Livewire/Wings/**'
---

# Wings

## Re-fetch the parent Wing through a scoped query before creating a nested Cage
Cage has no shelter_id of its own (see [[models]] note on MultiShelterTrait), so nothing stops a validation rule like `exists:wings,id` from accepting a wing_id that belongs to a different shelter — `exists` queries the raw table and ignores Eloquent global scopes. App\Livewire\Wings\ManageSpaces::createCage() and ::saveCage() both re-fetch the target wing via `Wing::query()->findOrFail($wingId)`, which *does* apply MultiShelterTrait's scope, so a tampered/cross-shelter wing id 404s instead of silently creating a cage on another shelter's wing. Keep this double-check (validate for shape, then findOrFail for tenancy) for any future action that creates a child record under a shelter-scoped parent. See tests/Feature/ManageSpacesTest.php ("cannot add a cage to a wing belonging to another shelter").

## Cage has no shelter scope of its own — use scopedCageQuery() for any direct Cage lookup
App\Livewire\Wings\ManageSpaces::scopedCageQuery() is the one place that scopes a direct Cage::query() to the acting user's shelter, via `->whereHas('wing', fn ($q) => $q->where('shelter_id', Auth::user()->shelter_id))` (Cage has no shelter_id column, so there's no global scope to rely on — see [[models]] note on MultiShelterTrait). editCage() and deleteCage() both call it before findOrFail(), so a cage id belonging to another shelter's wing 404s. Any new action that looks up a Cage directly by id (not via an already-scoped Wing's ->cages relation) must go through this helper instead of `Cage::query()->find(...)`. See tests/Feature/ManageSpacesTest.php ("cannot edit or delete a cage belonging to another shelter's wing").

## Deleting a Wing must soft-delete its Cages one by one, not via a bulk query delete
The cages.wing_id FK has cascadeOnDelete() at the DB level, but that only fires on a real (hard) delete — Wing uses SoftDeletes, so deleting a wing just sets its deleted_at and leaves existing cage rows untouched unless the app does it explicitly. ManageSpaces::deleteWing() now does `$wing->cages()->get()->each->delete()` before `$wing->delete()`. Don't swap that for `$wing->cages()->delete()` (a single mass UPDATE): SoftDeletingScope still turns it into a soft delete, but a bulk query delete doesn't fire per-model Eloquent events, so Blameable's `deleting` hook never runs and `deleted_by` stays null on the cascaded cages. See tests/Feature/ManageSpacesTest.php ("soft-deletes a wing's cages along with it, stamping deleted_by").
