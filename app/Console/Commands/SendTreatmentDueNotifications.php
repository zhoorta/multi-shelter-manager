<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Pet;
use App\Models\PetTreatment;
use App\Models\Shelter;
use App\Notifications\TreatmentDueNotification;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

#[Signature('app:send-treatment-due-notifications')]
#[Description('Email shelter users about preventive treatments due within the next seven days, one line per treatment round')]
class SendTreatmentDueNotifications extends Command
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
     * Email a single shelter's subscribed users about treatments due within
     * the next seven days. Treatments are given in rounds (e.g. deworming
     * every resident on the same day), so the email groups them per
     * treatment and due date instead of listing each pet. Reuses the
     * vaccination_notifications subscription.
     */
    private function notifyShelter(Shelter $shelter): void
    {
        $recipients = $shelter->users()
            ->wherePivot('vaccination_notifications', true)
            ->get();

        if ($recipients->isEmpty()) {
            return;
        }

        $dueTreatments = PetTreatment::query()
            ->whereIn('pet_id', Pet::query()->where('shelter_id', $shelter->id)->resident()->select('id'))
            ->pending()
            ->whereNull('notification_date')
            ->whereBetween('due_date', [today()->toDateString(), today()->addDays(7)->toDateString()])
            ->with(['pet', 'treatment'])
            ->orderBy('due_date')
            ->get();

        if ($dueTreatments->isEmpty()) {
            return;
        }

        Notification::send($recipients, new TreatmentDueNotification($dueTreatments));

        PetTreatment::query()
            ->whereKey($dueTreatments->pluck('id'))
            ->update([
                'notification_date' => now(),
                'notification_recipients' => json_encode($recipients->pluck('email')->values()->all()),
            ]);
    }
}
