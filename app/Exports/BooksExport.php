<?php

namespace App\Exports;

use App\Models\Book;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class BooksExport implements FromQuery, WithHeadings, WithMapping, WithChunkReading, ShouldAutoSize
{
    protected array $filters;
    protected array $columns;

    public function __construct(array $filters = [], array $columns = [])
    {
        $this->filters  = $filters;
        $this->columns  = $columns ?: ['isbn', 'title', 'author', 'price', 'stock', 'category', 'description'];
    }

    public function query()
    {
        $query = Book::query()
            ->select([
                'isbn', 'title', 'author', 'publisher', 'price',
                'stock_quantity', 'published_at', 'format',
                'category_id', 'description', 'is_featured', 'created_at',
            ])
            ->with('category:id,name')
            ->where('is_active', true)
            ->orderBy('id');

        if (!empty($this->filters['category_id'])) {
            $query->where('category_id', $this->filters['category_id']);
        }
        if (!empty($this->filters['min_price'])) {
            $query->where('price', '>=', $this->filters['min_price']);
        }
        if (!empty($this->filters['max_price'])) {
            $query->where('price', '<=', $this->filters['max_price']);
        }
        if (isset($this->filters['in_stock']) && $this->filters['in_stock']) {
            $query->where('stock_quantity', '>', 0);
        }
        if (!empty($this->filters['date_from'])) {
            $query->whereDate('created_at', '>=', $this->filters['date_from']);
        }
        if (!empty($this->filters['date_to'])) {
            $query->whereDate('created_at', '<=', $this->filters['date_to']);
        }

        return $query;
    }

    public function headings(): array
    {
        $map = [
            'isbn'        => 'ISBN',
            'title'       => 'Title',
            'author'      => 'Author',
            'publisher'   => 'Publisher',
            'price'       => 'Price',
            'stock'       => 'Stock',
            'format'      => 'Format',
            'published_at'=> 'Published',
            'category'    => 'Category',
            'description' => 'Description',
            'featured'    => 'Featured',
            'created_at'  => 'Created At',
        ];

        return array_values(array_intersect_key($map, array_flip($this->columns)));
    }

    public function map($book): array
    {
        $all = [
            'isbn'        => $book->isbn,
            'title'       => $book->title,
            'author'      => $book->author,
            'publisher'   => $book->publisher,
            'price'       => $book->price,
            'stock'       => $book->stock_quantity,
            'format'      => $book->format,
            'published_at'=> $book->published_at?->format('Y-m-d'),
            'category'    => $book->category?->name,
            'description' => $book->description,
            'featured'    => $book->is_featured ? 'Yes' : 'No',
            'created_at'  => $book->created_at?->format('Y-m-d'),
        ];

        return array_values(array_intersect_key($all, array_flip($this->columns)));
    }

    public function chunkSize(): int
    {
        return 2000;
    }
}
