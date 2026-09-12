<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\User;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class UserInvitation extends Notification
{
    /**
     * Create a new notification instance.
     */
    public function __construct(public string $token) {}

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
        $url = route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->email,
        ]);

        $message = (new MailMessage)
            ->subject(__("You've Been Invited"))
            ->greeting(__('Hello, :name!', ['name' => $notifiable->name]))
            ->line(__('You have been invited to join the Shelter Manager platform.'))
            ->line(__('Your role: :role', ['role' => __(Str::title($notifiable->role))]));

        if ($notifiable->shelter_id !== null && $notifiable->shelter) {
            $message->line(__('Your shelter: :shelter', ['shelter' => $notifiable->shelter->name]));
        }

        return $message
            ->action(__('Create Password'), $url)
            ->line(__('This invitation link will expire in :count hours.', ['count' => (int) (config('auth.passwords.users.expire') / 60)]))
            ->line(__('If you did not expect this invitation, you can safely ignore this email.'));
    }
}
