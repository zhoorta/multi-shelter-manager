---
paths:
  - 'app/Models/*.php'
  - app/Models/Pet.php
  - app/Models/FurType.php
  - app/Models/Cage.php
---

# Models

## Blameable trait: column-guarded, and pivot columns need explicit withPivot()
App\Traits\Blameable auto-stamps created_by/updated_by/deleted_by via model events, but only for columns that actually exist on the model's table (checked via Schema::hasColumn, cached per class+column). deleted_by is only stamped for models using SoftDeletes (guarded by isForceDeleting()); models without SoftDeletes skip it entirely since the row is about to be hard-deleted.

For custom pivot models (e.g. PetSickness, PetVaccine) that use Blameable: the belongsToMany() definition MUST list created_by/updated_by in ->withPivot([...]) or they'll be silently null when read back via $related->pivot, even though they're correctly saved in the DB (attach() with ->using() does fire model events and Blameable does stamp them — the read side just needs the columns declared).

User now uses SoftDeletes (added on top of its existing deleted_at/deleted_by columns). `$user->delete()` (both DeleteUserForm's self-service account deletion and ManageUsers::deleteUser) soft-deletes. Model::fresh() bypasses global scopes (including SoftDeletingScope), so `$user->fresh()` after a delete returns the soft-deleted row, not null — assert `->trashed()` instead of null (see tests/Feature/Settings/ProfileUpdateTest.php). Soft-deleted users are excluded from default queries (including the auth login lookup), but the `email` column is still uniquely constrained at the DB level across trashed and non-trashed rows, so re-inviting a previously deleted user's email will fail unless that concern is addressed separately.

## MultiShelterTrait: only for models with a direct shelter_id column
App\Traits\MultiShelterTrait enforces shelter data isolation: a global scope filters queries to Auth::user()->current_shelter_id, and a creating hook auto-assigns shelter_id from the acting user's current shelter when not explicitly set. A user with is_admin=true is exempt from the scope and sees all shelters' data (see [[traits]] for the full current_shelter_id/is_admin mechanics).

Currently applied to Pet and Facility — the only models with a direct shelter_id column (verified via the create_tables migration). User does NOT use this trait — the users table has no shelter_id column at all; a user's shelter membership(s) live on the shelter_users pivot instead (see [[models-models]]). Wing used to have a direct shelter_id and this trait, but lost both when Facility was introduced as an intermediate level (Shelter -> Facility -> Wing -> Cage) — Wing is now scoped transitively via facility_id (see [[facilities]]). Do NOT add it to Wing or Cage (both scoped transitively, no direct shelter_id column) or to global taxonomy models like Species/Breed/Color/FurType/Sickness/Vaccine (shared across all shelters, no shelter_id column at all). If a new model gains a direct shelter_id column, add this trait to it too.

## Species and Breed now use Blameable + SoftDeletes
Species and Breed (global taxonomy lookups, no shelter_id) were originally created without created_by/updated_by/deleted_by or deleted_at columns — unlike their sibling admin-managed lookups Vaccine and Sickness. Migration 2026_09_07_165054_add_soft_deletes_and_blameable_to_species_and_breeds_tables added those columns so Species/Breed now use the Blameable + SoftDeletes traits, matching Vaccine/Sickness. This is unrelated to MultiShelterTrait (still correctly not applied to any of these four models, per the existing rule below) — it's about audit/soft-delete parity across the admin-managed global lookups, needed so App\Livewire\Admin\ManageSpecies can soft-delete a Species/Breed without breaking the FK from pets.species_id/pets.breed_id (which are RESTRICT, not cascade).

## pets.ref is required but not auto-populated by the DB — PetForm generates it
pets.ref is a NOT NULL string column with no default and no uniqueness constraint at the DB level. Nothing else sets it automatically, so any code creating a Pet must supply it. Format is `PET` + the pet's own id zero-padded to 5 digits (PET00001, PET00002, ...) — see [[pets.ref format is PET + zero-padded pet_id, set after insert]] in pets.md for why that means a two-step create+update. Only set on create, never touched on update. If you add another way to create pets (e.g. an import, an API endpoint, a seeder), you must generate 'ref' there too or the insert will fail.

## Pet uses checkin_date/checkout_date, not admission_date/departure_date
The pets table migration was reworked to rename admission_date/departure_date to checkin_date/checkout_date (departure_date dropped in favor of checkout_date). Pet.php's docblock, $fillable, and casts() were updated to match — don't reintroduce admission_date/departure_date, those columns no longer exist.

## FurType now uses Blameable + SoftDeletes, like Species/Breed/Vaccine/Sickness
FurType (global taxonomy lookup, no shelter_id) was originally created without created_by/updated_by/deleted_by or deleted_at. Migration 2026_09_10_212501_add_soft_deletes_and_blameable_to_fur_types_table added those columns so App\Livewire\Admin\ManageFurTypes (admin/fur-types route) can soft-delete a FurType without breaking the RESTRICT FK from pets.fur_type_id. This completes parity across all admin-managed global lookups (Species, Breed, Vaccine, Sickness, FurType) — see [[models]] rule on Species/Breed. Colors is the only remaining global lookup without admin management/Blameable+SoftDeletes as of this change.

## Pet's species/breed/furType/size relations use withTrashed() — required, not optional
Species, Breed, FurType, and Size all use SoftDeletes, but their FKs on pets (species_id/breed_id/fur_type_id/size_id) are RESTRICT, not cascade — an admin can soft-delete a lookup row while pets still reference it. Without withTrashed(), $pet->breed (etc.) silently resolves to null once the row is trashed, and code that reads ->name directly (manage-pets.blade.php, pet-show.blade.php, pet-print.blade.php, pet-print-list.blade.php, manage-adoptions/manage-sponsorships pet->species->name) throws "Attempt to read property on null". Fixed by adding ->withTrashed() to Pet::species()/breed()/furType()/size() so a pet always resolves its historical selection regardless of the lookup's soft-delete state. Don't remove withTrashed() from these four relations without re-auditing every blade file that reads ->name off them non-null-safe. This is unrelated to the admin.md rule that keeps ->whereHas('species') on ManageBreeds' own listing query (that one intentionally hides orphaned breeds from the admin list; this one intentionally keeps a pet's own reference resolvable).

## Pet age_in_words stops at date_of_death
Pet::ageInWords() measures birth_date → date_of_death when the pet is deceased, else birth_date → now (periodInWords() takes an optional $to). Every view using age_in_words (manage-pets, pet-show, pet-print, pet-print-list, public card/modal) therefore shows age at death for deceased pets. pet-show.blade.php shows an "Age" row right after "Death Date" in the Identification subsection.

## Cage capacity is indicative, never a hard limit
cages.capacity is only a guideline: a cage can hold more animals than its capacity. Never block assignments or warn about "over capacity" (e.g. in PetForm, imports, or reports) based on it.
