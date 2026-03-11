<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewOrderNotification extends Notification
{
    use Queueable;

    public $order;

    /**
     * Create a new notification instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Order Placed - Order #' . $this->order->id)
            ->greeting('New Order Alert!')
            ->line('A new order has been placed by: **' . $this->order->user->name . '**')
            ->line('Order ID: #' . $this->order->id)
            ->line('Customer Email: ' . $this->order->user->email)
            ->line('Order Total: $' . number_format($this->order->total_amount, 2))
            ->line('Items: ' . $this->order->orderItems->count())
            ->action('View Order Details', route('admin.orders.show', $this->order))
            ->line('Please process this order promptly.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'customer_name' => $this->order->user->name,
            'customer_email' => $this->order->user->email,
            'total_amount' => $this->order->total_amount,
            'items_count' => $this->order->orderItems->count(),
        ];
    }
}