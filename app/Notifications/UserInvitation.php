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
            ->subject(__("You've Been Invited to :app platform", ['app' => config('app.name')]))
            ->greeting(__('Hello, :name!', ['name' => $notifiable->name]));

        if ($notifiable->is_admin) {
            $message->line(__('You have been invited to join the :app platform as an administrator.', ['app' => config('app.name')]));
        } else {
            $message->line(__('You have been invited to join the :app platform.', ['app' => config('app.name')]));

            $membership = $notifiable->shelters()->first();

            if ($membership !== null) {
                $message->line(__('Your shelter: :shelter', ['shelter' => $membership->name]));
                $message->line(__('Your role: :role', ['role' => __(Str::title($membership->pivot->role))]));
            }
        }

        return $message
            ->action(__('Create Password'), $url)
            ->line(__('This invitation link will expire in :count hours.', ['count' => (int) (config('auth.passwords.users.expire') / 60)]))
            ->line(__('If you did not expect this invitation, you can safely ignore this email.'));
    }
}
