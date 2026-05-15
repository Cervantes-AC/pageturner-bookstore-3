<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewOrderAdmin extends Notification
{
    use Queueable;

    public function __construct(public Order $order) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Order #' . $this->order->id . ' Received')
            ->greeting('Hi Admin!')
            ->line('A new order has been placed.')
            ->line('Order #: ' . $this->order->id)
            ->line('Customer: ' . ($this->order->user->name ?? 'N/A'))
            ->line('Total: ₱' . number_format($this->order->total_amount, 2))
            ->action('View Order', url('/admin/orders'))
            ->line('Please process this order promptly.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'customer_name' => $this->order->user->name ?? 'N/A',
            'total_amount' => $this->order->total_amount,
            'message' => 'New order #' . $this->order->id . ' from ' . ($this->order->user->name ?? 'N/A'),
        ];
    }
}
