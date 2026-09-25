<?php

declare(strict_types=1);

use App\Http\Controllers\RobotsController;
use App\Http\Controllers\ShelterFeedController;
use App\Http\Controllers\SitemapController;
use App\Http\Middleware\EnsurePublicPortalEnabled;
use App\Livewire\About;
use App\Livewire\Admin\ManageActivities;
use App\Livewire\Admin\ManageBreeds;
use App\Livewire\Admin\ManageFurTypes;
use App\Livewire\Admin\ManageRegions;
use App\Livewire\Admin\ManageShelters;
use App\Livewire\Admin\ManageSicknesses;
use App\Livewire\Admin\ManageSizes;
use App\Livewire\Admin\ManageSpecies;
use App\Livewire\Admin\ManageUsers;
use App\Livewire\Admin\ManageVaccines;
use App\Livewire\Admin\ShelterForm;
use App\Livewire\Admin\UserForm;
use App\Livewire\Dashboard;
use App\Livewire\Documentation;
use App\Livewire\Facilities\ManageSpaces;
use App\Livewire\PartnerShelters;
use App\Livewire\PartnerShelterShow;
use App\Livewire\Pets\AdoptionForm;
use App\Livewire\Pets\AdoptionShow;
use App\Livewire\Pets\ManageAdoptions;
use App\Livewire\Pets\ManagePets;
use App\Livewire\Pets\ManageSponsorships;
use App\Livewire\Pets\ManageVaccinations;
use App\Livewire\Pets\PetForm;
use App\Livewire\Pets\PetPrint;
use App\Livewire\Pets\PetPrintList;
use App\Livewire\Pets\PetShow;
use App\Livewire\Pets\SponsorshipForm;
use App\Livewire\Pets\SponsorshipShow;
use App\Livewire\Pets\VaccinationForm;
use App\Livewire\PrivacyPolicy;
use App\Livewire\Setup;
use App\Livewire\Volunteers\ManageVolunteers;
use App\Livewire\Volunteers\VolunteerForm;
use App\Livewire\Volunteers\VolunteerShow;
use App\Livewire\Welcome;
use Illuminate\Support\Facades\Route;

// Public welcome page, open to guests and logged-in users alike; redirects to login when the portal is disabled.
Route::livewire('/', Welcome::class)->middleware(EnsurePublicPortalEnabled::class)->name('home');
Route::livewire('shelters', PartnerShelters::class)->middleware(EnsurePublicPortalEnabled::class)->name('shelters');
Route::livewire('shelters/{shelter}', PartnerShelterShow::class)->middleware(EnsurePublicPortalEnabled::class)->name('shelters.show');
Route::get('shelters/{shelter}/feed', ShelterFeedController::class)->middleware(EnsurePublicPortalEnabled::class)->name('shelters.feed');
Route::livewire('about', About::class)->name('about');
Route::livewire('privacy-policy', PrivacyPolicy::class)->name('privacy-policy');
Route::get('robots.txt', RobotsController::class)->name('robots');
Route::get('sitemap.xml', SitemapController::class)->middleware(EnsurePublicPortalEnabled::class)->name('sitemap');

// Public guest routes.
Route::middleware(['guest'])->group(function (): void {
    // First-run wizard to create the initial admin; only usable while no users exist.
    Route::livewire('setup', Setup::class)->name('setup');

    // Login GET/POST routes are registered by Laravel Fortify
    // (see App\Providers\FortifyServiceProvider) and render
    // resources/views/livewire/auth/login.blade.php.
});

// Authenticated protected routes.
Route::middleware(['auth', 'verified'])->group(function (): void {
    Route::livewire('dashboard', Dashboard::class)->name('dashboard');
    Route::livewire('documentation', Documentation::class)->name('documentation');

    Route::livewire('pets', ManagePets::class)->name('pets.index');
    Route::livewire('pets/sponsorships', ManageSponsorships::class)->name('pets.sponsorships.index');
    Route::livewire('pets/adoptions', ManageAdoptions::class)->name('pets.adoptions.index');
    Route::livewire('pets/vaccinations', ManageVaccinations::class)->name('pets.vaccinations.index');
    Route::livewire('pets/create', PetForm::class)->name('pets.create');
    Route::livewire('pets/print', PetPrintList::class)->name('pets.print.list');
    Route::livewire('pets/{pet}/edit', PetForm::class)->name('pets.edit');
    Route::livewire('pets/{pet}', PetShow::class)->name('pets.show');
    Route::livewire('pets/{pet}/print', PetPrint::class)->name('pets.print');
    Route::livewire('pets/{pet}/adopt', AdoptionForm::class)->name('pets.adopt');
    Route::livewire('pets/{pet}/adopt/{adoption}', AdoptionShow::class)->name('pets.adopt.show');
    Route::livewire('pets/{pet}/adopt/{adoption}/edit', AdoptionForm::class)->name('pets.adopt.edit');
    Route::livewire('pets/{pet}/sponsor', SponsorshipForm::class)->name('pets.sponsor');
    Route::livewire('pets/{pet}/sponsor/{sponsorship}', SponsorshipShow::class)->name('pets.sponsor.show');
    Route::livewire('pets/{pet}/sponsor/{sponsorship}/edit', SponsorshipForm::class)->name('pets.sponsor.edit');
    Route::livewire('pets/{pet}/vaccinate', VaccinationForm::class)->name('pets.vaccinate');
    Route::livewire('pets/{pet}/vaccinate/{petVaccine}/edit', VaccinationForm::class)->name('pets.vaccinate.edit');

    Route::livewire('volunteers', ManageVolunteers::class)->name('volunteers.index');
    Route::livewire('volunteers/create', VolunteerForm::class)->name('volunteers.create');
    Route::livewire('volunteers/{volunteer}/edit', VolunteerForm::class)->name('volunteers.edit');
    Route::livewire('volunteers/{volunteer}', VolunteerShow::class)->name('volunteers.show');

    Route::livewire('facilities', ManageSpaces::class)->name('facilities.index');

    // Administration routes.
    Route::livewire('admin/users', ManageUsers::class)->name('admin.users.index');
    Route::livewire('admin/users/create', UserForm::class)->name('admin.users.create');
    Route::livewire('admin/users/{user}/edit', UserForm::class)->name('admin.users.edit');
    Route::livewire('admin/shelters', ManageShelters::class)->name('admin.shelters.index');
    Route::livewire('admin/shelters/create', ShelterForm::class)->name('admin.shelters.create');
    Route::livewire('admin/shelters/{shelter}/edit', ShelterForm::class)->name('admin.shelters.edit');
    Route::livewire('admin/regions', ManageRegions::class)->name('admin.regions.index');
    Route::livewire('admin/species', ManageSpecies::class)->name('admin.species.index');
    Route::livewire('admin/breeds', ManageBreeds::class)->name('admin.breeds.index');
    Route::livewire('admin/sizes', ManageSizes::class)->name('admin.sizes.index');
    Route::livewire('admin/fur-types', ManageFurTypes::class)->name('admin.fur-types.index');
    Route::livewire('admin/vaccines', ManageVaccines::class)->name('admin.vaccines.index');
    Route::livewire('admin/sicknesses', ManageSicknesses::class)->name('admin.sicknesses.index');
    Route::livewire('admin/activities', ManageActivities::class)->name('admin.activities.index');
});

require __DIR__.'/settings.php';
