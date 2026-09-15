---
paths:
  - 'app/Livewire/Pets/PetPrint.php,resources/views/livewire/pets/pet-print.blade.php,resources/views/layouts/print.blade.php'
---

# Layouts

## PetPrint is a full-page Livewire component with a custom bare layout, not the app chrome
Route pets/{pet}/print (name pets.print) opens App\Livewire\Pets\PetPrint in a new tab (pet-show.blade.php's "printer" icon button uses target="_blank"). It overrides Livewire's default component_layout ('layouts::app', the sidebar chrome) via `#[Layout('layouts.print')]` on render() — resources/views/layouts/print.blade.php is a minimal doctype/head/body view (no sidebar/header) that just outputs {{ $slot }} + @fluxScripts, following the same "app-logo + $slot" shape as layouts/auth/simple.blade.php but with no accent/dark styling since it's a printable document.

Same auth gate as PetShow (abort_unless role in ['manager','staff']) and eager-loads pet.shelter (not loaded by PetShow) since the printout shows shelter name/city/website/email/logo alongside the pet's own fields. The page has an on-page "Print" button (`onclick="window.print()"`, `print:hidden` via Tailwind's built-in print variant) — don't wire up an actual PDF/print library, window.print() is sufficient. New lang keys added: "Print", "Reference", "Website" (en+pt).
