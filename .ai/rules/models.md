---
paths:
  - 'app/Models/*.php'
---

# Models

## Blameable trait: column-guarded, and pivot columns need explicit withPivot()
App\Traits\Blameable auto-stamps created_by/updated_by/deleted_by via model events, but only for columns that actually exist on the model's table (checked via Schema::hasColumn, cached per class+column). deleted_by is only stamped for models using SoftDeletes (guarded by isForceDeleting()); models without SoftDeletes skip it entirely since the row is about to be hard-deleted.

For custom pivot models (e.g. PetSickness, PetVaccine) that use Blameable: the belongsToMany() definition MUST list created_by/updated_by in ->withPivot([...]) or they'll be silently null when read back via $related->pivot, even though they're correctly saved in the DB (attach() with ->using() does fire model events and Blameable does stamp them — the read side just needs the columns declared).

User model intentionally does NOT use SoftDeletes even though the users table has deleted_at/deleted_by columns — DeleteUserForm relies on hard delete, and Model::fresh() bypasses global scopes (including SoftDeletingScope) so it would find a soft-deleted row instead of returning null, breaking that flow's tests. Don't add SoftDeletes to User without also revisiting account deletion.

## MultiShelterTrait: only for models with a direct shelter_id column
App\Traits\MultiShelterTrait enforces shelter data isolation: a global scope filters queries to Auth::user()->shelter_id, and a creating hook auto-assigns shelter_id from the acting user when not explicitly set. A user with shelter_id === null (i.e. an admin) is exempt from the scope and sees all shelters' data.

Currently applied to Pet, Wing, and User — the only models with a direct shelter_id column (verified via the create_tables/create_users_table migrations). Do NOT add it to Cage (scoped transitively via wing_id, no direct shelter_id column) or to global taxonomy models like Species/Breed/Color/FurType/Sickness/Vaccine (shared across all shelters, no shelter_id column at all). If a new model gains a direct shelter_id column, add this trait to it too.
