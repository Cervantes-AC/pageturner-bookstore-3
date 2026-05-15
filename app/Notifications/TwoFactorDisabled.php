<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TwoFactorDisabled extends Notification
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Two-Factor Authentication Disabled')
            ->greeting('Hi ' . $notifiable->name . '!')
            ->line('Two-factor authentication has been disabled on your account.')
            ->line('If you did not disable this, please change your password immediately.')
            ->action('View Profile', url('/profile'));
    }
}
