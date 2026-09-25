<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\AdoptionApplication;
use App\Models\User;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdoptionApplicationReceived extends Notification
{
    public function __construct(public AdoptionApplication $application) {}

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
     * Get the mail representation of the notification. Only the applicant's
     * name goes in the e-mail; the full answers stay behind the login.
     */
    public function toMail(User $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('New adoption application for :name', ['name' => $this->application->pet->name]))
            ->markdown('mail.adoption-application-received', [
                'notifiable' => $notifiable,
                'application' => $this->application,
            ]);
    }
}
