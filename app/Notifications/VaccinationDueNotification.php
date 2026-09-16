<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\PetVaccine;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VaccinationDueNotification extends Notification
{
    /**
     * @param  Collection<int, PetVaccine>  $dueVaccinations
     */
    public function __construct(public Collection $dueVaccinations) {}

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
            ->subject(__('Vaccination Reminders'))
            ->markdown('mail.vaccination-due', [
                'notifiable' => $notifiable,
                'dueVaccinations' => $this->dueVaccinations,
            ]);
    }
}
