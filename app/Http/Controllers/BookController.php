<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use App\Services\BookFilterService;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index(Request $request, BookFilterService $filterService)
    {
        $query = Book::with('category');

        // Apply filters and sorting
        $query = $filterService->applyFilters($query, $request->all());
        $query = $filterService->applySorting($query, $request->sort);

        $books = $query->paginate(12);
        $categories = Category::all();
        $filterOptions = $filterService->getFilterOptions();
        $years = $filterOptions['years'];

        return view('books.index', compact('books', 'categories', 'filterOptions', 'years'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('books.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'publication_year' => 'nullable|integer|min:1000|max:' . (date('Y') + 1),
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'cover_image' => 'nullable|image|max:2048',
        ]);

        // Auto-generate ISBN
        $validated['isbn'] = $this->generateISBN();

        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')
                ->store('covers', 'public');
        }

        Book::create($validated);

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
        $categories = Category::all();
        return view('books.edit', compact('book', 'categories'));
    }

    public function update(Request $request, Book $book)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'publication_year' => 'nullable|integer|min:1000|max:' . (date('Y') + 1),
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'cover_image' => 'nullable|image|max:2048',
        ]);

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
        $book->delete();

        return redirect()->route('books.index')
            ->with('success', 'Book deleted successfully!');
    }

    private function generateISBN()
    {
        do {
            // Generate ISBN-13 format: 978-X-XXXX-XXXX-X using cryptographically secure random
            $isbn = '978-' . random_int(0, 9) . '-' . 
                    str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT) . '-' . 
                    str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT) . '-' . 
                    random_int(0, 9);
        } while (Book::where('isbn', $isbn)->exists());

        return $isbn;
    }
}
