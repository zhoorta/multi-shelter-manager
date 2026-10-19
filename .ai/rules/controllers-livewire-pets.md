---
paths:
  - 'app/Models/PetClinicalPhoto.php,app/Http/Controllers/ClinicalPhotoController.php,app/Livewire/Pets/ClinicalPhotoForm.php'
---

# Controllers Livewire Pets

## Clinical photos live on the private disk and are served only through an authenticated route
pet_clinical_photos (added 2026-10-19 for Uva's lumps) hold dated photos grouped by a free-text label. Files go to Storage disk 'local' (private), never 'public': the pet portal photos are public, these are health data. They are served only by ClinicalPhotoController (pets.clinical-photos.file; thumbnail by default, ?size=full, ?download=1) behind the scoped {pet} binding and module:health_records, so another shelter gets 404 and viewers may look. Thumbnails are made with GD (MakeImageThumbnail, null without GD = original served). Deleting is a soft delete and keeps the files so an admin can restore; file_size feeds the platform storage overview; the data export lists them as clinical_photos without the images. Upload limit is 10 MB per image, 10 per form.
