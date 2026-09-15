---
paths:
  - 'app/Livewire/Documentation.php,resources/views/livewire/documentation.blade.php,resources/views/livewire/documentation/*.blade.php'
---

# Documentation

## Documentation page content lives in per-locale Blade partials, not lang JSON or the database
App\Livewire\Documentation (route `documentation`, linked from the sidebar's "Documentation" item) renders resources/views/livewire/documentation.blade.php, which does `@includeFirst(['livewire.documentation.instructions-'.app()->getLocale(), 'livewire.documentation.instructions-en'])`.

Long-form prose content (headings/paragraphs) belongs in these per-locale partials (instructions-en.blade.php, instructions-pt.blade.php), NOT in lang/en.json|pt.json (those are for short UI strings only) and NOT in a database table (no admin-editable requirement exists). Add a new locale by adding a new `instructions-{locale}.blade.php` partial; missing locales fall back to English via includeFirst.
