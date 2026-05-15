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
use Illuminate\Validation\Rule;

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
        $category = Category::where('name', $row['category'])->first();
        if (!$category) {
            return null;
        }

        $isbn = (string) trim($row['isbn']);
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
        ]);
    }

    public function rules(): array
    {
        return [
            'isbn' => [
                'required',
                'regex:/^(?:\d{9}[\dX]|\d{13})$/',
            ],
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'price' => 'required|numeric|min:0|max:9999.99',
            'stock' => 'required|integer|min:0',
            'category' => 'required|string|exists:categories,name',
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
