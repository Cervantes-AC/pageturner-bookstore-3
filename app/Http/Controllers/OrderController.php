<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Notifications\NewOrderNotification;
use App\Notifications\OrderStatusNotification;
use Illuminate\Http\Request;

class OrderController extends Controller
{
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

        $total = 0;
        $orderItems = [];

        foreach ($request->items as $item) {
            $book = Book::findOrFail($item['book_id']);

            if ($book->stock_quantity < $item['quantity']) {
                return back()->with('error', "Not enough stock for: {$book->title}");
            }

            $total += $book->price * $item['quantity'];

            $orderItems[] = [
                'book_id' => $book->id,
                'quantity' => $item['quantity'],
                'unit_price' => $book->price,
            ];
        }

        $order = Order::create([
            'user_id' => auth()->id(),
            'total_amount' => $total,
            'status' => 'pending',
            'shipping_name' => $request->shipping_name,
            'shipping_phone' => $request->shipping_phone,
            'shipping_address' => $request->shipping_address,
        ]);

        foreach ($orderItems as $item) {
            $order->orderItems()->create($item);
            Book::find($item['book_id'])->decrement('stock_quantity', $item['quantity']);
        }

        $order->load(['user', 'orderItems.book']);

        $order->user->notify(new OrderStatusNotification($order));

        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new NewOrderNotification($order));
        }

        session()->forget('cart');

        return redirect()->route('orders.show', $order)
            ->with('success', 'Order placed successfully!');
    }

    public function show(Order $order)
    {
        $this->authorize('view', $order);

        $order->load('orderItems.book');
        return view('orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $this->authorize('updateStatus', $order);

        $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled',
        ]);

        $oldStatus = $order->status;
        $order->update(['status' => $request->status]);

        // Notify customer about status change
        $order->user->notify(new OrderStatusNotification($order, $oldStatus));

        return back()->with('success', 'Order status updated!');
    }
}