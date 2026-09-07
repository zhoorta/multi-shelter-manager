---
paths:
  - 'app/Livewire/Admin/**'
---

# Admin

## Breed belongsTo Species is scoped by SoftDeletes — breeds of a deleted species must be excluded, not just null-guarded
Species uses SoftDeletes, so Breed::species() (belongsTo) silently returns null for a breed whose species was soft-deleted, since Species' global scope excludes trashed rows (reading $breed->species->name directly throws "Attempt to read property on null"). Per product decision, such breeds must not appear in the "all breeds" list at all — App\Livewire\Admin\ManageBreeds::breeds() filters them out with ->whereHas('species') (the default Species scope makes this equivalent to "species not trashed"), so plain ->with('species') + $breed->species->name is safe there without a null-guard. Don't reintroduce ->withTrashed() on that relation/order-by subquery — it would bring back the null-species rows. See tests/Feature/ManageBreedsTest.php ("hides breeds whose species has been soft-deleted"). ManageBreeds::species() (the dropdown source) likewise never lists trashed species, including for a breed currently assigned to one.

## Create/edit forms use a single shared Flux modal per resource
ManageSpecies and ManageBreeds use one persistent `<flux:modal name="...">` (species-form / breed-form) reused for both create and edit, opened client-side via `<flux:modal.trigger name="...">` wrapping the row's Edit button or the page's Create button (each trigger also fires a `wire:click` to populate/reset the form fields server-side). After a successful save, the component closes it server-side with `Flux::modal('name')->close()` (dispatches a `modal-close` browser event — assertable in tests via `assertDispatched('modal-close', name: '...')`). Cancel buttons just wrap `<flux:modal.close>` and don't need a dedicated reset method.

Dropdowns (species filter, species picker in the breed modal) must exclude soft-deleted `Species` — don't special-case a currently-assigned trashed species back into the list; the `exists:species,id` validation rule still allows saving a breed whose species was soft-deleted after assignment, so editing/saving still works even though the dropdown won't show that option as selected.
