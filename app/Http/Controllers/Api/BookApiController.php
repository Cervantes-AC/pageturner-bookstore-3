<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookResource;
use App\Models\Book;
use App\Repositories\BookRepository;
use Illuminate\Http\Request;

class BookApiController extends Controller
{
    public function __construct(
        private readonly BookRepository $bookRepository,
    ) {}

    public function index(Request $request)
    {
        $perPage = min((int) $request->input('per_page', 20), 100);

        if ($request->filled('category_id')) {
            $books = $this->bookRepository->findByCategory((int) $request->category_id, $perPage);
        } elseif ($request->filled('search')) {
            $books = $this->bookRepository->search($request->search, $perPage);
        } else {
            $books = $this->bookRepository->getActiveCatalog($perPage);
        }

        return BookResource::collection($books);
    }

    public function show(Book $book)
    {
        $book->load(['category', 'reviews.user']);
        return new BookResource($book);
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
            'format'      => 'nullable|string|in:hardcover,paperback,ebook,audiobook',
        ]);

        $book = Book::create($validated);

        return response()->json(['data' => new BookResource($book), 'message' => 'Book created'], 201);
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
            'format'      => 'nullable|string|in:hardcover,paperback,ebook,audiobook',
        ]);

        $book->update($validated);

        return response()->json(['data' => new BookResource($book->fresh()), 'message' => 'Book updated']);
    }

    public function destroy(Book $book)
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        $book->delete();

        return response()->json(['message' => 'Book deleted']);
    }
}
