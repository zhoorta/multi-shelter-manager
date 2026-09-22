<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Pet;
use App\Models\PetVaccine;
use App\Models\Shelter;
use App\Notifications\VaccinationDueNotification;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

#[Signature('app:send-vaccination-due-notifications')]
#[Description('Email shelter users about pet vaccinations due within the next seven days')]
class SendVaccinationDueNotifications extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        foreach (Shelter::query()->cursor() as $shelter) {
            $this->notifyShelter($shelter);
        }

        return self::SUCCESS;
    }

    /**
     * Email a single shelter's subscribed users about their pets'
     * vaccinations due within the next seven days. A shelter with no
     * subscribed users, or with nothing due, is skipped without affecting
     * any other shelter.
     */
    private function notifyShelter(Shelter $shelter): void
    {
        $recipients = $shelter->users()
            ->wherePivot('vaccination_notifications', true)
            ->get();

        if ($recipients->isEmpty()) {
            return;
        }

        $dueVaccinations = PetVaccine::query()
            ->whereHas('pet', fn ($query) => $query->where('shelter_id', $shelter->id))
            ->whereNull('administered_date')
            ->whereNull('notification_date')
            ->whereBetween('due_date', [today()->toDateString(), today()->addDays(7)->toDateString()])
            ->with(['pet', 'vaccine'])
            ->orderBy('due_date')
            ->orderBy(Pet::select('name')->whereColumn('id', 'pet_vaccines.pet_id'))
            ->get();

        if ($dueVaccinations->isEmpty()) {
            return;
        }

        Notification::send($recipients, new VaccinationDueNotification($dueVaccinations));

        PetVaccine::query()
            ->whereKey($dueVaccinations->pluck('id'))
            ->update([
                'notification_date' => now(),
                'notification_recipients' => json_encode($recipients->pluck('email')->values()->all()),
            ]);
    }
}
