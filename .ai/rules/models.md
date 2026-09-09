---
paths:
  - 'app/Models/*.php'
  - app/Models/Pet.php
---

# Models

## Blameable trait: column-guarded, and pivot columns need explicit withPivot()
App\Traits\Blameable auto-stamps created_by/updated_by/deleted_by via model events, but only for columns that actually exist on the model's table (checked via Schema::hasColumn, cached per class+column). deleted_by is only stamped for models using SoftDeletes (guarded by isForceDeleting()); models without SoftDeletes skip it entirely since the row is about to be hard-deleted.

For custom pivot models (e.g. PetSickness, PetVaccine) that use Blameable: the belongsToMany() definition MUST list created_by/updated_by in ->withPivot([...]) or they'll be silently null when read back via $related->pivot, even though they're correctly saved in the DB (attach() with ->using() does fire model events and Blameable does stamp them — the read side just needs the columns declared).

User model intentionally does NOT use SoftDeletes even though the users table has deleted_at/deleted_by columns — DeleteUserForm relies on hard delete, and Model::fresh() bypasses global scopes (including SoftDeletingScope) so it would find a soft-deleted row instead of returning null, breaking that flow's tests. Don't add SoftDeletes to User without also revisiting account deletion.

## MultiShelterTrait: only for models with a direct shelter_id column
App\Traits\MultiShelterTrait enforces shelter data isolation: a global scope filters queries to Auth::user()->shelter_id, and a creating hook auto-assigns shelter_id from the acting user when not explicitly set. A user with shelter_id === null (i.e. an admin) is exempt from the scope and sees all shelters' data.

Currently applied to Pet, Wing, and User — the only models with a direct shelter_id column (verified via the create_tables/create_users_table migrations). Do NOT add it to Cage (scoped transitively via wing_id, no direct shelter_id column) or to global taxonomy models like Species/Breed/Color/FurType/Sickness/Vaccine (shared across all shelters, no shelter_id column at all). If a new model gains a direct shelter_id column, add this trait to it too.

## Species and Breed now use Blameable + SoftDeletes
Species and Breed (global taxonomy lookups, no shelter_id) were originally created without created_by/updated_by/deleted_by or deleted_at columns — unlike their sibling admin-managed lookups Vaccine and Sickness. Migration 2026_09_07_165054_add_soft_deletes_and_blameable_to_species_and_breeds_tables added those columns so Species/Breed now use the Blameable + SoftDeletes traits, matching Vaccine/Sickness. This is unrelated to MultiShelterTrait (still correctly not applied to any of these four models, per the existing rule below) — it's about audit/soft-delete parity across the admin-managed global lookups, needed so App\Livewire\Admin\ManageSpecies can soft-delete a Species/Breed without breaking the FK from pets.species_id/pets.breed_id (which are RESTRICT, not cascade).

## pets.ref is required but not auto-populated by the DB — PetForm generates it
pets.ref is a NOT NULL string column with no default and no uniqueness constraint at the DB level. Nothing else sets it automatically, so any code creating a Pet must supply it. Format is `PET` + the pet's own id zero-padded to 5 digits (PET00001, PET00002, ...) — see [[pets.ref format is PET + zero-padded pet_id, set after insert]] in pets.md for why that means a two-step create+update. Only set on create, never touched on update. If you add another way to create pets (e.g. an import, an API endpoint, a seeder), you must generate 'ref' there too or the insert will fail.

## Pet uses checkin_date/checkout_date, not admission_date/departure_date
The pets table migration was reworked to rename admission_date/departure_date to checkin_date/checkout_date (departure_date dropped in favor of checkout_date). Pet.php's docblock, $fillable, and casts() were updated to match — don't reintroduce admission_date/departure_date, those columns no longer exist.
