<?php

use App\Console\Commands\SendVaccinationDueNotifications;
use App\Models\AdoptionApplication;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command(SendVaccinationDueNotifications::class)->daily();
Schedule::command('model:prune', ['--model' => [AdoptionApplication::class]])->daily();
