<?php

namespace App\Exports;

use App\Models\Book;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class BooksExport implements FromQuery, WithHeadings, WithMapping, WithChunkReading
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = Book::query()
            ->select(['isbn', 'title', 'author', 'price', 'format', 'stock_quantity', 'category_id', 'description', 'created_at'])
            ->with('category:id,name');

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
            $query->whereDate('created_at', '>=', $this->filters['date_from']);
        }
        if (!empty($this->filters['date_to'])) {
            $query->whereDate('created_at', '<=', $this->filters['date_to']);
        }

        $query->where('is_active', true);
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
            $book->category->name ?? 'N/A',
            $book->description,
            $book->created_at->format('Y-m-d H:i:s'),
        ];
    }

    public function chunkSize(): int
    {
        return 2000;
    }
}
