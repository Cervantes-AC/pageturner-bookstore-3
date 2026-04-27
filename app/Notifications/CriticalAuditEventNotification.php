<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use OwenIt\Auditing\Models\Audit;

class CriticalAuditEventNotification extends Notification
{
    use Queueable;

    public function __construct(protected Audit $audit) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $model   = class_basename($this->audit->auditable_type);
        $event   = ucfirst($this->audit->event);
        $actor   = $this->audit->user?->name ?? 'System';
        $ip      = $this->audit->ip_address;
        $time    = $this->audit->created_at?->format('M d, Y H:i:s');

        return (new MailMessage)
            ->subject("⚠️ Critical Audit Event: {$event} on {$model}")
            ->greeting('Security Alert — PageTurner')
            ->line("A critical action was performed on your system.")
            ->line("**Event:** {$event}")
            ->line("**Model:** {$model} #{$this->audit->auditable_id}")
            ->line("**Performed by:** {$actor}")
            ->line("**IP Address:** {$ip}")
            ->line("**Time:** {$time}")
            ->action('View Audit Log', url('/admin/audit-logs/' . $this->audit->id))
            ->line('If this action was not authorized, please investigate immediately.');
    }
}
