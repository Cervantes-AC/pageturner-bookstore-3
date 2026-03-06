<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        $cartItems = [];
        $total = 0;

        foreach ($cart as $bookId => $quantity) {
            $book = Book::find($bookId);
            if ($book) {
                $cartItems[] = [
                    'book' => $book,
                    'quantity' => $quantity,
                    'subtotal' => $book->price * $quantity,
                ];
                $total += $book->price * $quantity;
            }
        }

        return view('cart.index', compact('cartItems', 'total'));
    }

    public function add(Request $request, Book $book)
    {
        if ($book->stock_quantity < 1) {
            return back()->with('error', 'This book is out of stock.');
        }

        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = session()->get('cart', []);
        $currentQuantity = $cart[$book->id] ?? 0;
        $newQuantity = $currentQuantity + $request->quantity;

        if ($newQuantity > $book->stock_quantity) {
            return back()->with('error', 'Not enough stock available. Only ' . $book->stock_quantity . ' items in stock.');
        }

        $cart[$book->id] = $newQuantity;
        session()->put('cart', $cart);

        return back()->with('success', 'Book added to cart!');
    }

    public function update(Request $request, Book $book)
    {
        $request->validate([
            'quantity' => 'required|integer|min:0',
        ]);

        $cart = session()->get('cart', []);

        if ($request->quantity == 0) {
            unset($cart[$book->id]);
        } else {
            if ($request->quantity > $book->stock_quantity) {
                return back()->with('error', 'Not enough stock available. Only ' . $book->stock_quantity . ' items in stock.');
            }
            $cart[$book->id] = $request->quantity;
        }

        session()->put('cart', $cart);

        return back()->with('success', 'Cart updated!');
    }

    public function remove(Book $book)
    {
        $cart = session()->get('cart', []);
        unset($cart[$book->id]);
        session()->put('cart', $cart);

        return back()->with('success', 'Book removed from cart!');
    }

    public function clear()
    {
        session()->forget('cart');
        return back()->with('success', 'Cart cleared!');
    }

    public function checkout()
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $items = [];
        $cartItems = [];
        $total = 0;

        foreach ($cart as $bookId => $quantity) {
            $book = Book::find($bookId);
            if ($book) {
                $items[] = [
                    'book_id' => $bookId,
                    'quantity' => $quantity,
                ];
                $cartItems[] = [
                    'book' => $book,
                    'quantity' => $quantity,
                    'subtotal' => $book->price * $quantity,
                ];
                $total += $book->price * $quantity;
            }
        }

        return view('cart.checkout', compact('items', 'cartItems', 'total'));
    }
}
