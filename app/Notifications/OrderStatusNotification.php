<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusNotification extends Notification
{
    use Queueable;

    public $order;
    public $oldStatus;

    /**
     * Create a new notification instance.
     */
    public function __construct(Order $order, $oldStatus = null)
    {
        $this->order = $order;
        $this->oldStatus = $oldStatus;
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
        $message = (new MailMessage)
            ->subject('Order Status Update - Order #' . $this->order->id)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Your order status has been updated.');

        if ($this->oldStatus) {
            $message->line('Status changed from: **' . ucfirst($this->oldStatus) . '** to **' . ucfirst($this->order->status) . '**');
        } else {
            $message->line('Current status: **' . ucfirst($this->order->status) . '**');
        }

        $message->line('Order Total: $' . number_format($this->order->total_amount, 2))
            ->action('View Order Details', route('orders.show', $this->order))
            ->line('Thank you for shopping with PageTurner!');

        return $message;
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
            'status' => $this->order->status,
            'old_status' => $this->oldStatus,
            'total_amount' => $this->order->total_amount,
        ];
    }
}