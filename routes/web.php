<?php

declare(strict_types=1);

use App\Livewire\Admin\ManageBreeds;
use App\Livewire\Admin\ManageSpecies;
use App\Livewire\Dashboard;
use App\Livewire\Facilities\ManageSpaces;
use App\Livewire\Pets\AdoptionForm;
use App\Livewire\Pets\ManagePets;
use App\Livewire\Pets\PetForm;
use App\Livewire\Pets\PetShow;
use Illuminate\Support\Facades\Route;

// Public guest routes.
Route::middleware(['guest'])->group(function (): void {
    Route::redirect('/', '/login')->name('home');

    // Login GET/POST routes are registered by Laravel Fortify
    // (see App\Providers\FortifyServiceProvider) and render
    // resources/views/livewire/auth/login.blade.php.
});

// Authenticated protected routes.
Route::middleware(['auth', 'verified'])->group(function (): void {
    Route::livewire('dashboard', Dashboard::class)->name('dashboard');

    Route::livewire('pets', ManagePets::class)->name('pets.index');
    Route::livewire('pets/create', PetForm::class)->name('pets.create');
    Route::livewire('pets/{pet}/edit', PetForm::class)->name('pets.edit');
    Route::livewire('pets/{pet}', PetShow::class)->name('pets.show');
    Route::livewire('pets/{pet}/adopt', AdoptionForm::class)->name('pets.adopt');

    Route::livewire('facilities', ManageSpaces::class)->name('facilities.index');

    // Users routes will be attached here.

    // Administration routes.
    Route::livewire('admin/species', ManageSpecies::class)->name('admin.species.index');
    Route::livewire('admin/breeds', ManageBreeds::class)->name('admin.breeds.index');
});

require __DIR__.'/settings.php';
