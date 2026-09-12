---
paths:
  - 'app/Livewire/Pets/AdoptionForm.php,app/Livewire/Pets/AdoptionShow.php,resources/views/livewire/pets/partials/adoption-box.blade.php'
---

# Partials

## AdoptionForm's back/cancel buttons are context-aware via a ?from=adoptions query param
AdoptionForm (edit mode) can be reached from two places: pet-show.blade.php's adoption-box loop, and adoption-show.blade.php (reached via the adoptions list, see [[pets-livewire-pets]]). The "back" (arrow-left) and "Cancel" buttons must return the user to wherever they came from, not always to the pet page.

Mechanism: partials/adoption-box.blade.php accepts an optional $backToAdoptionsList bool (default false). When true (only passed by adoption-show.blade.php), its Edit link appends ?from=adoptions to the pets.adopt.edit route. AdoptionForm::mount() reads request()->query('from') === 'adoptions' (only meaningful when editing, i.e. $adoption !== null) and sets public $backRoute/$backLabel accordingly: pets.adopt.show/$adoption->name (back to the single-adoption view, NOT the adoptions list index) vs the default pets.show/$pet->name. adoption-form.blade.php's two buttons use $backRoute/$backLabel instead of hardcoding route('pets.show', $pet). The post-save redirect in saveAdoption() intentionally still always goes to pets.show — only back/cancel are context-aware. Don't hardcode route('pets.show', $pet), and don't point this back route at pets.adoptions.index — that was tried and corrected; the natural "back" from an edit reached via View is the show page you viewed, not the list.
