<?php

namespace App\Exports;

use App\Models\Book;
use App\Models\Category;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class BooksExport implements FromQuery, WithHeadings, WithMapping, WithChunkReading
{
    protected $filters;
    protected $categoryCache = [];

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
        // Pre-load all categories into memory once
        $this->categoryCache = Category::pluck('name', 'id')->toArray();
    }

    public function chunkSize(): int
    {
        return 500;
    }

    public function query()
    {
        $query = Book::query()
            ->select(['id', 'isbn', 'title', 'author', 'price', 'format', 'stock_quantity', 'category_id', 'description', 'created_at'])
            ->where('is_active', true);

        if (!empty($this->filters['category_id'])) {
            $query->where('category_id', $this->filters['category_id']);
        }
        if (!empty($this->filters['min_price'])) {
            $query->where('price', '>=', $this->filters['min_price']);
        }
        if (!empty($this->filters['max_price'])) {
            $query->where('price', '<=', $this->filters['max_price']);
        }
        if (isset($this->filters['stock_status'])) {
            if ($this->filters['stock_status'] === 'in_stock') {
                $query->where('stock_quantity', '>', 0);
            } elseif ($this->filters['stock_status'] === 'out_of_stock') {
                $query->where('stock_quantity', '=', 0);
            }
        }
        if (!empty($this->filters['date_from'])) {
            $query->where('created_at', '>=', $this->filters['date_from'] . ' 00:00:00');
        }
        if (!empty($this->filters['date_to'])) {
            $query->where('created_at', '<=', $this->filters['date_to'] . ' 23:59:59');
        }

        $query->orderBy('id');
        return $query;
    }

    public function headings(): array
    {
        return [
            'ISBN',
            'Title',
            'Author',
            'Price',
            'Format',
            'Stock',
            'Category',
            'Description',
            'Created At',
        ];
    }

    public function map($book): array
    {
        return [
            $book->isbn,
            $book->title,
            $book->author,
            $book->price,
            $book->format ?? 'N/A',
            $book->stock_quantity,
            $this->categoryCache[$book->category_id] ?? 'N/A',
            $book->description,
            $book->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
