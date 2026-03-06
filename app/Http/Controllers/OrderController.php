<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Order;
use App\Models\OrderItem;
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

            $subtotal = $book->price * $item['quantity'];
            $total += $subtotal;

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
            // Reduce stock
            Book::find($item['book_id'])->decrement('stock_quantity', $item['quantity']);
        }

        // Clear cart after successful order
        session()->forget('cart');

        return redirect()->route('orders.show', $order)
            ->with('success', 'Order placed successfully!');
    }

    public function show(Order $order)
    {
        // Only allow owner or admin to view
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

        $order->update(['status' => $request->status]);

        return back()->with('success', 'Order status updated!');
    }
}