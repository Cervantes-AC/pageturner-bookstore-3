<?php

namespace App\Services;

use App\Events\AuditableActionPerformed;
use App\Models\Book;
use App\Models\Order;
use App\Models\User;
use App\Notifications\OrderPlaced;
use App\Notifications\OrderStatusChanged;
use App\Notifications\NewOrderAdmin;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderService
{
    /**
     * Create a new order with transaction management
     *
     * @param int $userId
     * @param array $items Array of ['book_id' => int, 'quantity' => int]
     * @param array $shippingInfo
     * @return Order
     * @throws \Exception
     */
    public function createOrder(int $userId, array $items, array $shippingInfo): Order
    {
        return DB::transaction(function () use ($userId, $items, $shippingInfo) {
            $total = 0;
            $orderItems = [];

            // Validate stock availability for all items first
            foreach ($items as $item) {
                $book = Book::lockForUpdate()->findOrFail($item['book_id']);

                if ($book->stock_quantity < $item['quantity']) {
                    throw new \Exception("Not enough stock for: {$book->title}");
                }

                $subtotal = $book->price * $item['quantity'];
                $total += $subtotal;

                $orderItems[] = [
                    'book_id' => $book->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $book->price,
                ];
            }

            // Create order
            $order = Order::create([
                'user_id' => $userId,
                'total_amount' => $total,
                'status' => 'pending',
                'shipping_name' => $shippingInfo['shipping_name'],
                'shipping_phone' => $shippingInfo['shipping_phone'],
                'shipping_address' => $shippingInfo['shipping_address'],
            ]);

            // Create order items and update stock
            foreach ($orderItems as $item) {
                $order->orderItems()->create($item);
                Book::find($item['book_id'])->decrement('stock_quantity', $item['quantity']);
            }

            // Dispatch audit event
            AuditableActionPerformed::dispatch(
                'order_placed',
                $order,
                null,
                $order->toArray(),
                "Order placed with {$order->orderItems()->count()} items"
            );

            return $order;
        });
    }

    /**
     * Update order status and notify user
     *
     * @param Order $order
     * @param string $status
     * @return void
     */
    public function updateOrderStatus(Order $order, string $status): void
    {
        $oldStatus = $order->status;
        $order->update(['status' => $status]);

        // Dispatch audit event
        AuditableActionPerformed::dispatch(
            'order_status_updated',
            $order,
            ['status' => $oldStatus],
            ['status' => $status],
            "Order status changed from {$oldStatus} to {$status}"
        );

        // Notify user
        $order->user->notify(new OrderStatusChanged($order));
    }

    /**
     * Send order notifications
     *
     * @param Order $order
     * @return void
     */
    public function sendOrderNotifications(Order $order): void
    {
        try {
            // Notify customer
            $order->user->notify(new OrderPlaced($order));

            // Notify admins
            $admins = User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                $admin->notify(new NewOrderAdmin($order));
            }
        } catch (\Exception $e) {
            Log::error('Failed to send order notifications', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Cancel order and restore stock
     *
     * @param Order $order
     * @return void
     */
    public function cancelOrder(Order $order): void
    {
        DB::transaction(function () use ($order) {
            // Restore stock
            foreach ($order->orderItems as $item) {
                $item->book->increment('stock_quantity', $item->quantity);
            }

            // Update status
            $order->update(['status' => 'cancelled']);

            // Dispatch audit event
            AuditableActionPerformed::dispatch(
                'order_cancelled',
                $order,
                ['status' => 'pending'],
                ['status' => 'cancelled'],
                'Order cancelled and stock restored'
            );
        });
    }
}
