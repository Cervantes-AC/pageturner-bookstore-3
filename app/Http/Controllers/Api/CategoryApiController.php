<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryApiController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('books')->orderBy('name')->get();

        return response()->json([
            'data' => $categories->map(fn($c) => [
                'id'          => $c->id,
                'name'        => $c->name,
                'description' => $c->description,
                'books_count' => $c->books_count,
                'created_at'  => $c->created_at?->toIso8601String(),
            ]),
        ]);
    }

    public function show(Category $category)
    {
        $category->load('books');

        return response()->json([
            'data' => [
                'id'          => $category->id,
                'name'        => $category->name,
                'description' => $category->description,
                'books'       => $category->books->map(fn($b) => [
                    'id'     => $b->id,
                    'title'  => $b->title,
                    'author' => $b->author,
                    'price'  => (float) $b->price,
                    'stock'  => $b->stock_quantity,
                ]),
                'created_at'  => $category->created_at?->toIso8601String(),
            ],
        ]);
    }
}
