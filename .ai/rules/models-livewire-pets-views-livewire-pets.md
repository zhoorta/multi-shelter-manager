---
paths:
  - 'app/Models/Pet.php,app/Livewire/Pets/PetPrint.php,resources/views/livewire/pets/pet-print.blade.php'
---

# Models Livewire Pets Views Livewire Pets

## pet-print's status field swaps label/value based on adopted/deceased, driven by a shared Pet::periodInWords helper
Pet::ageInWords() (existing, based on birth_date) and the new Pet::timeInCaptivity() (based on checkin_date) both delegate to a private static Pet::periodInWords(CarbonInterface $from) helper — don't duplicate the years/months trans_choice logic again if a third "elapsed time in words" field is ever needed. periodInWords must type-hint CarbonInterface (not Support\Carbon) since the date-cast columns yield CarbonImmutable at runtime, which isn't a Carbon subtype.

pet-print.blade.php's grid has a status-dependent field right after Is Sponsorable: label/value is "In captivity" + $pet->time_in_captivity when the pet is neither adopted nor deceased, "Adopted at" + the latest adoption's adoption_date when $pet->status === 'adopted' (mirrors pet-show's adoption-badge logic — needs 'adoptions' eager-loaded in PetPrint::mount(), latest first), or "Deceased at" + date_of_death when date_of_death is set (checked first, takes priority). The Age field earlier in the same grid is hidden entirely (`@unless ($pet->date_of_death)`) when deceased — age from birth_date is redundant/confusing next to a death date.
