<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\PetTreatment;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TreatmentDueNotification extends Notification
{
    /**
     * @param  Collection<int, PetTreatment>  $dueTreatments
     */
    public function __construct(public Collection $dueTreatments) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(User $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('Treatment Reminders'))
            ->markdown('mail.treatment-due', [
                'notifiable' => $notifiable,
                'treatmentRounds' => $this->treatmentRounds(),
            ]);
    }

    /**
     * Due treatments grouped into rounds: one per treatment and due date.
     *
     * @return array<int, array{treatment: string, due_date: string, pets: array<int, string>}>
     */
    public function treatmentRounds(): array
    {
        return $this->dueTreatments
            ->groupBy(fn (PetTreatment $petTreatment): string => $petTreatment->treatment_id.'|'.$petTreatment->due_date->toDateString())
            ->map(fn ($roundTreatments): array => [
                'treatment' => $roundTreatments->first()->treatment->name,
                'due_date' => $roundTreatments->first()->due_date->format('d/m/Y'),
                'pets' => $roundTreatments->map(fn (PetTreatment $petTreatment): string => $petTreatment->pet->name)->sort()->values()->all(),
            ])
            ->values()
            ->all();
    }
}
