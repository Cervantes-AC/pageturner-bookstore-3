<?php

namespace App\Imports;

use App\Models\Book;
use App\Models\Category;
use App\Models\ImportLog;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;

class BooksImport implements ToModel, WithHeadingRow, WithValidation, WithBatchInserts, WithChunkReading, SkipsOnFailure
{
    use SkipsFailures;

    protected $importLog;
    protected $updateExisting;

    public function __construct(ImportLog $importLog, bool $updateExisting = false)
    {
        $this->importLog = $importLog;
        $this->updateExisting = $updateExisting;
    }

    public function model(array $row)
    {
        $this->importLog->increment('processed_rows');

        $category = Category::firstOrCreate(
            ['name' => $row['category']],
            ['description' => $row['category'] . ' books']
        );

        if (!$category) {
            return null;
        }

        $isbn = preg_replace('/[^0-9X]/i', '', (string) $row['isbn']);
        $existing = Book::where('isbn', $isbn)->first();

        if ($existing) {
            if ($this->updateExisting) {
                $existing->update([
                    'title' => $row['title'],
                    'author' => $row['author'],
                    'price' => (float) $row['price'],
                    'stock_quantity' => (int) $row['stock'],
                    'category_id' => $category->id,
                    'description' => $row['description'] ?? $existing->description,
                    'format' => $row['format'] ?? $existing->format,
                    'is_active' => true,
                ]);
                return null;
            }
            return null;
        }

        return new Book([
            'isbn' => $isbn,
            'title' => $row['title'],
            'author' => $row['author'],
            'price' => (float) $row['price'],
            'stock_quantity' => (int) $row['stock'],
            'category_id' => $category->id,
            'description' => $row['description'] ?? '',
            'format' => $row['format'] ?? 'paperback',
            'is_active' => true,
        ]);
    }

    public function prepareForValidation($data, $index)
    {
        foreach (['isbn', 'title', 'author', 'category', 'format', 'description'] as $field) {
            if (array_key_exists($field, $data) && $data[$field] !== null) {
                $data[$field] = trim((string) $data[$field]);
            }
        }

        return $data;
    }

    public function rules(): array
    {
        return [
            'isbn' => [
                'required',
                'regex:/^(?:\d[\-\s]?){9}[\dXx]$|^(?:\d[\-\s]?){13}$/',
            ],
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'price' => 'required|numeric|min:0|max:9999.99',
            'stock' => 'required|integer|min:0',
            'category' => 'required|string|max:255',
        ];
    }

    public function batchSize(): int
    {
        return 1000;
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
