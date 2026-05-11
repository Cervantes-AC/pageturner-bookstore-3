<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;

class BookApiController extends Controller
{
    public function index(Request $request)
    {
        $query = Book::with('category');

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($w) use ($q) {
                $w->where('title', 'like', "%{$q}%")
                  ->orWhere('author', 'like', "%{$q}%")
                  ->orWhere('isbn', 'like', "%{$q}%");
            });
        }
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }
        if ($request->boolean('in_stock')) {
            $query->where('stock_quantity', '>', 0);
        }

        // Cursor-based pagination (Section 4.4.2)
        $perPage = min((int) $request->input('per_page', 20), 100);
        $cursor  = $request->input('cursor');

        if ($cursor) {
            $books = $query->where('id', '>', $cursor)
                ->orderBy('id')
                ->take($perPage)
                ->get();
        } else {
            $books = $query->orderBy('id')
                ->take($perPage)
                ->get();
        }

        $nextCursor = $books->count() === $perPage ? $books->last()->id : null;

        return response()->json([
            'data' => $books->map(fn($b) => [
                'id'            => $b->id,
                'isbn'          => $b->isbn,
                'title'         => $b->title,
                'author'        => $b->author,
                'price'         => (float) $b->price,
                'stock'         => $b->stock_quantity,
                'description'   => $b->description,
                'category_id'   => $b->category_id,
                'category_name' => $b->category?->name,
                'cover_image'   => $b->cover_image,
                'is_featured'   => $b->is_featured,
                'avg_rating'    => $b->average_rating,
                'created_at'    => $b->created_at?->toIso8601String(),
            ]),
            'meta' => [
                'per_page'    => $perPage,
                'next_cursor' => $nextCursor,
                'has_more'    => $nextCursor !== null,
            ],
        ])->setEtag(md5(json_encode([$books->pluck('id')->toArray(), $books->count()])));
    }

    public function show(Book $book)
    {
        $book->load(['category', 'reviews.user']);

        return response()->json([
            'data' => [
                'id'            => $book->id,
                'isbn'          => $book->isbn,
                'title'         => $book->title,
                'author'        => $book->author,
                'publication_year' => $book->publication_year,
                'price'         => (float) $book->price,
                'stock'         => $book->stock_quantity,
                'description'   => $book->description,
                'category_id'   => $book->category_id,
                'category_name' => $book->category?->name,
                'cover_image'   => $book->cover_image,
                'is_featured'   => $book->is_featured,
                'avg_rating'    => $book->average_rating,
                'reviews'       => $book->reviews->map(fn($r) => [
                    'id'        => $r->id,
                    'user'      => $r->user?->name,
                    'rating'    => $r->rating,
                    'comment'   => $r->comment,
                    'created_at'=> $r->created_at?->toIso8601String(),
                ]),
                'created_at'    => $book->created_at?->toIso8601String(),
                'updated_at'    => $book->updated_at?->toIso8601String(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title'       => 'required|string|max:255',
            'author'      => 'required|string|max:255',
            'isbn'        => 'required|string|unique:books,isbn',
            'price'       => 'required|numeric|min:0|max:9999.99',
            'stock'       => 'required|integer|min:0',
            'description' => 'nullable|string',
        ]);

        $book = Book::create($validated);

        return response()->json(['data' => $book, 'message' => 'Book created'], 201);
    }

    public function update(Request $request, Book $book)
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        $validated = $request->validate([
            'category_id' => 'exists:categories,id',
            'title'       => 'string|max:255',
            'author'      => 'string|max:255',
            'isbn'        => 'string|unique:books,isbn,' . $book->id,
            'price'       => 'numeric|min:0|max:9999.99',
            'stock'       => 'integer|min:0',
            'description' => 'nullable|string',
        ]);

        $book->update($validated);

        return response()->json(['data' => $book->fresh(), 'message' => 'Book updated']);
    }

    public function destroy(Book $book)
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        $book->delete();

        return response()->json(['message' => 'Book deleted']);
    }
}
