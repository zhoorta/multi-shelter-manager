<?php

use App\Http\Middleware\BlockInDemo;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\ExportData;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\Security;
use App\Livewire\Settings\ShelterProfile;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::livewire('settings/profile', Profile::class)->middleware(BlockInDemo::class)->name('profile.edit');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::livewire('settings/appearance', Appearance::class)->name('appearance.edit');

    Route::livewire('settings/shelter', ShelterProfile::class)->name('shelter-profile.edit');

    Route::livewire('settings/export', ExportData::class)->middleware(BlockInDemo::class)->name('data-export.edit');

    Route::livewire('settings/security', Security::class)
        ->middleware([
            BlockInDemo::class,
            'password.confirm',
        ])
        ->name('security.edit');
});
