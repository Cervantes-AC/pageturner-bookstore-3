<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use App\Services\BookFilterService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::withCount('books');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        match ($request->input('sort')) {
            'name_desc' => $query->orderBy('name', 'desc'),
            'books_desc' => $query->orderBy('books_count', 'desc'),
            'books_asc' => $query->orderBy('books_count', 'asc'),
            default => $query->orderBy('name'),
        };

        $categories = $query->paginate(9)->withQueryString();

        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories',
            'description' => 'nullable|string',
        ]);

        Category::create($validated);

        $redirect = request()->routeIs('admin.*')
            ? redirect()->route('admin.dashboard')
            : redirect()->route('categories.index');

        return $redirect->with('success', 'Category created successfully!');
    }

    public function show(Request $request, Category $category, BookFilterService $filterService)
    {
        $query = Book::with('category')->where('category_id', $category->id);
        $query = $filterService->applyFilters($query, $request->except('category'));
        $query = $filterService->applySorting($query, $request->sort);

        $books = $query->paginate(12)->withQueryString();
        $categories = Category::withCount('books')->get();
        $filterOptions = $filterService->getFilterOptions();
        $years = $category->books()
            ->whereNotNull('publication_year')
            ->distinct()
            ->orderBy('publication_year', 'desc')
            ->pluck('publication_year');

        return view('categories.show', compact('category', 'books', 'categories', 'filterOptions', 'years'));
    }

    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string',
        ]);

        $category->update($validated);

        $redirect = request()->routeIs('admin.*')
            ? redirect()->route('admin.dashboard')
            : redirect()->route('categories.index');

        return $redirect->with('success', 'Category updated successfully!');
    }

    public function destroy(Category $category)
    {
        $category->delete();

        $redirect = request()->routeIs('admin.*')
            ? redirect()->route('admin.dashboard')
            : redirect()->route('categories.index');

        return $redirect->with('success', 'Category deleted successfully!');
    }
}
