---
paths:
  - 'app/Models/*.php'
---

# Models

## Blameable trait: column-guarded, and pivot columns need explicit withPivot()
App\Traits\Blameable auto-stamps created_by/updated_by/deleted_by via model events, but only for columns that actually exist on the model's table (checked via Schema::hasColumn, cached per class+column). deleted_by is only stamped for models using SoftDeletes (guarded by isForceDeleting()); models without SoftDeletes skip it entirely since the row is about to be hard-deleted.

For custom pivot models (e.g. PetSickness, PetVaccine) that use Blameable: the belongsToMany() definition MUST list created_by/updated_by in ->withPivot([...]) or they'll be silently null when read back via $related->pivot, even though they're correctly saved in the DB (attach() with ->using() does fire model events and Blameable does stamp them — the read side just needs the columns declared).

User model intentionally does NOT use SoftDeletes even though the users table has deleted_at/deleted_by columns — DeleteUserForm relies on hard delete, and Model::fresh() bypasses global scopes (including SoftDeletingScope) so it would find a soft-deleted row instead of returning null, breaking that flow's tests. Don't add SoftDeletes to User without also revisiting account deletion.
