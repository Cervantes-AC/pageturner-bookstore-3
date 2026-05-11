<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use App\Services\BookCacheService;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function __construct(
        private readonly BookCacheService $cacheService,
    ) {}

    public function index(Request $request)
    {
        $query = Book::select([
            'id', 'isbn', 'title', 'author', 'price',
            'stock_quantity', 'published_at', 'category_id',
            'cover_image_url', 'format',
        ])->with('category:id,name');

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%")
                  ->orWhere('isbn', 'like', "%{$search}%");
            });
        }

        if ($request->filled('year')) {
            $query->where('publication_year', $request->year);
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'price_asc':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('price', 'desc');
                    break;
                case 'title_asc':
                    $query->orderBy('title', 'asc');
                    break;
                case 'title_desc':
                    $query->orderBy('title', 'desc');
                    break;
                case 'newest':
                    $query->orderBy('published_at', 'desc');
                    break;
                case 'oldest':
                    $query->orderBy('published_at', 'asc');
                    break;
                default:
                    $query->orderBy('published_at', 'desc');
            }
        } else {
            $query->orderBy('published_at', 'desc');
        }

        $books = $query->paginate(12);
        $categories = Category::all();

        $years = Book::whereNotNull('publication_year')
                    ->distinct()
                    ->orderBy('publication_year', 'desc')
                    ->pluck('publication_year');

        return view('books.index', compact('books', 'categories', 'years'));
    }

    public function create()
    {
        $this->authorize('create', Book::class);

        $categories = Category::all();
        return view('books.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Book::class);

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'publication_year' => 'nullable|integer|min:1000|max:' . (date('Y') + 1),
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'cover_image' => 'nullable|image|max:2048',
            'cover_image_url' => 'nullable|url|max:2048',
            'is_featured' => 'nullable|boolean',
            'format' => 'nullable|string|in:hardcover,paperback,ebook,audiobook',
        ]);

        $validated['isbn'] = $this->generateISBN();
        $validated['is_featured'] = $request->has('is_featured');

        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')
                ->store('covers', 'public');
        }

        Book::create($validated);
        $this->cacheService->invalidateCatalog();

        return redirect()->route('books.index')
            ->with('success', 'Book added successfully!');
    }

    public function show(Book $book)
    {
        $book->load(['category', 'reviews.user']);
        return view('books.show', compact('book'));
    }

    public function edit(Book $book)
    {
        $this->authorize('update', $book);

        $categories = Category::all();
        return view('books.edit', compact('book', 'categories'));
    }

    public function update(Request $request, Book $book)
    {
        $this->authorize('update', $book);

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'publication_year' => 'nullable|integer|min:1000|max:' . (date('Y') + 1),
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'cover_image' => 'nullable|image|max:2048',
            'cover_image_url' => 'nullable|url|max:2048',
            'is_featured' => 'nullable|boolean',
            'format' => 'nullable|string|in:hardcover,paperback,ebook,audiobook',
        ]);

        $validated['is_featured'] = $request->has('is_featured');

        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')
                ->store('covers', 'public');
        }

        $book->update($validated);

        return redirect()->route('books.show', $book)
            ->with('success', 'Book updated successfully!');
    }

    public function destroy(Book $book)
    {
        $this->authorize('delete', $book);

        $book->delete();

        return redirect()->route('books.index')
            ->with('success', 'Book deleted successfully!');
    }

    private function generateISBN()
    {
        do {
            $isbn = '978-' . rand(0, 9) . '-' .
                    str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT) . '-' .
                    str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT) . '-' .
                    rand(0, 9);
        } while (Book::where('isbn', $isbn)->exists());

        return $isbn;
    }
}
