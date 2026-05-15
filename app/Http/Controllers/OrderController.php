<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Order;
use App\Models\User;
use App\Services\OrderService;
use App\Notifications\OrderPlaced;
use App\Notifications\OrderStatusChanged;
use App\Notifications\NewOrderAdmin;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(private OrderService $orderService) {}

    public function index()
    {
        $orders = auth()->user()->isAdmin()
            ? Order::with('user')->orderBy('created_at', 'desc')->paginate(15)
            : Order::where('user_id', auth()->id())
                ->orderBy('created_at', 'desc')
                ->paginate(15);

        return view('orders.index', compact('orders'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.book_id' => 'required|exists:books,id',
            'items.*.quantity' => 'required|integer|min:1',
            'shipping_name' => 'required|string|max:255',
            'shipping_phone' => 'required|string|max:20',
            'shipping_address' => 'required|string|max:1000',
        ]);

        try {
            $order = $this->orderService->createOrder(
                auth()->id(),
                $request->items,
                $request->only(['shipping_name', 'shipping_phone', 'shipping_address'])
            );

            session()->forget('cart');

            $this->orderService->sendOrderNotifications($order);

            return redirect()->route('orders.show', $order)
                ->with('success', 'Order placed successfully!');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Order creation failed', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', $e->getMessage() ?: 'Failed to place order. Please try again.');
        }
    }

    public function show(Order $order)
    {
        if (auth()->id() !== $order->user_id && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $order->load('orderItems.book');
        return view('orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled',
        ]);

        $this->orderService->updateOrderStatus($order, $request->status);

        return back()->with('success', 'Order status updated!');
    }
}
