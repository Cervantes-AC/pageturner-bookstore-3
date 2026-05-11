<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderApiController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $query = Order::with('orderItems.book')
            ->where('user_id', $user->id)
            ->latest();

        $perPage = min((int) $request->input('per_page', 20), 100);
        $cursor  = $request->input('cursor');

        if ($cursor) {
            $orders = $query->where('id', '<', $cursor)->take($perPage)->get();
        } else {
            $orders = $query->take($perPage)->get();
        }

        $nextCursor = $orders->count() === $perPage ? $orders->last()->id : null;

        return response()->json([
            'data' => $orders->map(fn($o) => [
                'id'            => $o->id,
                'status'        => $o->status,
                'total_amount'  => (float) $o->total_amount,
                'shipping_name' => $o->shipping_name,
                'items'         => $o->orderItems->map(fn($i) => [
                    'book_title' => $i->book?->title,
                    'quantity'   => $i->quantity,
                    'unit_price' => (float) $i->unit_price,
                    'subtotal'   => $i->subtotal,
                ]),
                'created_at'    => $o->created_at?->toIso8601String(),
            ]),
            'meta' => [
                'per_page'    => $perPage,
                'next_cursor' => $nextCursor,
                'has_more'    => $nextCursor !== null,
            ],
        ]);
    }

    public function show(Order $order)
    {
        abort_if($order->user_id !== auth()->id() && !auth()->user()?->isAdmin(), 403);

        $order->load('orderItems.book', 'user');

        return response()->json([
            'data' => [
                'id'            => $order->id,
                'user'          => $order->user?->name,
                'status'        => $order->status,
                'total_amount'  => (float) $order->total_amount,
                'shipping_name' => $order->shipping_name,
                'shipping_address' => $order->shipping_address,
                'items'         => $order->orderItems->map(fn($i) => [
                    'book_title' => $i->book?->title,
                    'quantity'   => $i->quantity,
                    'unit_price' => (float) $i->unit_price,
                    'subtotal'   => $i->subtotal,
                ]),
                'created_at'    => $order->created_at?->toIso8601String(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $cart = session('cart', []);
        if (empty($cart)) {
            return response()->json(['message' => 'Cart is empty'], 400);
        }

        $total = 0;
        $items = [];
        foreach ($cart as $bookId => $qty) {
            $book = \App\Models\Book::find($bookId);
            if (!$book || $book->stock_quantity < $qty) {
                return response()->json(['message' => "Insufficient stock for: {$book?->title}"], 400);
            }
            $total += $book->price * $qty;
            $items[] = ['book' => $book, 'qty' => $qty];
        }

        $order = Order::create([
            'user_id'     => auth()->id(),
            'total_amount'=> $total,
            'status'      => 'pending',
        ]);

        foreach ($items as $item) {
            $order->orderItems()->create([
                'book_id'    => $item['book']->id,
                'quantity'   => $item['qty'],
                'unit_price' => $item['book']->price,
            ]);
            $item['book']->decrement('stock_quantity', $item['qty']);
        }

        session()->forget('cart');

        return response()->json([
            'data'    => $order->load('orderItems.book'),
            'message' => 'Order placed successfully',
        ], 201);
    }

    public function adminIndex(Request $request)
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        $query = Order::with('user', 'orderItems.book')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $perPage = min((int) $request->input('per_page', 20), 100);
        $cursor  = $request->input('cursor');

        if ($cursor) {
            $orders = $query->where('id', '<', $cursor)->take($perPage)->get();
        } else {
            $orders = $query->take($perPage)->get();
        }

        $nextCursor = $orders->count() === $perPage ? $orders->last()->id : null;

        return response()->json([
            'data' => $orders->map(fn($o) => [
                'id'           => $o->id,
                'customer'     => $o->user?->name,
                'customer_email' => $o->user?->email,
                'status'       => $o->status,
                'total_amount' => (float) $o->total_amount,
                'items_count'  => $o->orderItems->count(),
                'created_at'   => $o->created_at?->toIso8601String(),
            ]),
            'meta' => [
                'per_page'    => $perPage,
                'next_cursor' => $nextCursor,
                'has_more'    => $nextCursor !== null,
            ],
        ]);
    }
}
