<?php

declare(strict_types=1);

use App\Livewire\Dashboard;
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

    // Pets routes will be attached here.

    // Wings routes will be attached here.

    // Users routes will be attached here.
});

require __DIR__.'/settings.php';
