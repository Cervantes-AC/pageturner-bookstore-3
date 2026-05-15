<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TwoFactorEnabled extends Notification
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Two-Factor Authentication Enabled')
            ->greeting('Hi ' . $notifiable->name . '!')
            ->line('Two-factor authentication has been enabled on your account.')
            ->line('Your account is now more secure.')
            ->action('View Profile', url('/profile'))
            ->line('If you did not enable this, please change your password immediately.');
    }
}
