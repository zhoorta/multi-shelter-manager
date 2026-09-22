<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Shelter;
use App\Models\User;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class ShelterMembershipAdded extends Notification
{
    /**
     * Create a new notification instance.
     */
    public function __construct(public Shelter $shelter, public string $role) {}

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
            ->subject(__('You now have access to a new shelter'))
            ->greeting(__('Hello, :name!', ['name' => $notifiable->name]))
            ->line(__('You have been added to :shelter as a :role.', [
                'shelter' => $this->shelter->name,
                'role' => __(Str::title($this->role)),
            ]))
            ->line(__('Use the shelter switcher in the sidebar to access it with your existing account.'))
            ->action(__('Go to Dashboard'), route('dashboard'));
    }
}
