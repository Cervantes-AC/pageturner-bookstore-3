<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderPlaced extends Notification
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
            ->subject('Order Confirmation - #' . $this->order->id)
            ->greeting('Hi ' . $notifiable->name . '!')
            ->line('Your order has been placed successfully.')
            ->line('Order #: ' . $this->order->id)
            ->line('Total: ₱' . number_format($this->order->total_amount, 2))
            ->line('Status: ' . ucfirst($this->order->status))
            ->action('View Order', url('/orders/' . $this->order->id))
            ->line('Thank you for shopping with PageTurner!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'total_amount' => $this->order->total_amount,
            'status' => $this->order->status,
            'message' => 'Order #' . $this->order->id . ' placed successfully.',
        ];
    }
}
