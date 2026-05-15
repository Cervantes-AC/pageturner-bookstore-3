<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TwoFactorCode extends Notification
{
    use Queueable;

    public function __construct(public string $code) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Two-Factor Authentication Code')
            ->greeting('Hi ' . $notifiable->name . '!')
            ->line('Your two-factor authentication code is:')
            ->line('**' . $this->code . '**')
            ->line('This code will expire in 10 minutes.')
            ->line('If you did not attempt to login, please change your password immediately.');
    }
}
