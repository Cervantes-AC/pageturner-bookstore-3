<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusChanged extends Notification
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
            ->subject('Order #' . $this->order->id . ' Status Updated')
            ->greeting('Hi ' . $notifiable->name . '!')
            ->line('Your order status has been updated.')
            ->line('Order #: ' . $this->order->id)
            ->line('New Status: ' . ucfirst($this->order->status))
            ->action('View Order', url('/orders/' . $this->order->id))
            ->line('Thank you for shopping with PageTurner!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'status' => $this->order->status,
            'message' => 'Order #' . $this->order->id . ' status changed to ' . $this->order->status,
        ];
    }
}
