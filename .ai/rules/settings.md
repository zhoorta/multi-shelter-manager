---
paths:
  - 'app/Livewire/Settings/Profile.php,resources/views/livewire/settings/profile.blade.php'
---

# Settings

## Profile settings: name/email are read-only, account deletion removed
Per product decision, users can no longer change their own name/email or delete their own account from Settings > Profile. Profile::mount() still loads $name/$email onto public props, but updateProfileInformation() and the ProfileValidationRules trait were removed — profile.blade.php now renders them as plain flux:text, not flux:input. The self-service DeleteUserForm component/view and App\Concerns\ProfileValidationRules concern were deleted entirely (no other usage). Don't reintroduce editable name/email fields or the delete-account form here. Note: admin/manager-driven user management (ManageUsers::deleteUser, editing other users) is unrelated and untouched — this only affects a user's own profile page.
