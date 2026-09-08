---
paths:
  - 'app/Livewire/Pets/**'
---

# Pets

## pets.gender enum only allows male/female — 'unknown' is a latent bug
The pets.gender column is `enum('gender', ['male', 'female'])` — no 'unknown' value exists at the DB level. PetForm's validation ('petGender' => 'in:male,female,unknown') and the gender <flux:select> in pet-form.blade.php both still offer 'unknown', which will throw a DB constraint violation if selected. PetFactory was fixed to only generate male/female. Not fixed in the UI/validation yet — decide whether to widen the DB enum or drop 'unknown' from the form before shipping.
