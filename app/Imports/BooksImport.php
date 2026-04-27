<?php

namespace App\Imports;

use App\Models\Book;
use App\Models\Category;
use App\Models\ImportLog;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Validators\Failure;

class BooksImport implements
    ToModel,
    WithHeadingRow,
    WithValidation,
    WithChunkReading,
    WithBatchInserts,
    SkipsOnFailure
{
    use SkipsFailures;

    protected int $importLogId;
    protected string $mode;
    protected int $processed = 0;

    public function __construct(int $importLogId, string $mode = 'skip')
    {
        $this->importLogId = $importLogId;
        $this->mode = $mode;
    }

    public function model(array $row): ?Book
    {
        $category = Category::where('name', $row['category'])->first();
        if (!$category) return null;

        $existing = Book::where('isbn', $row['isbn'])->first();

        if ($existing) {
            if ($this->mode === 'update') {
                $existing->update([
                    'title'           => $row['title'],
                    'author'          => $row['author'],
                    'price'           => $row['price'],
                    'stock_quantity'  => $row['stock'],
                    'description'     => $row['description'] ?? null,
                    'category_id'     => $category->id,
                ]);
            }
            $this->processed++;
            return null;
        }

        $this->processed++;

        return new Book([
            'isbn'           => $row['isbn'],
            'title'          => $row['title'],
            'author'         => $row['author'],
            'price'          => $row['price'],
            'stock_quantity' => $row['stock'],
            'description'    => $row['description'] ?? null,
            'category_id'    => $category->id,
        ]);
    }

    public function rules(): array
    {
        return [
            'isbn'   => 'required|string',
            'title'  => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'price'  => 'required|numeric|min:0|max:9999.99',
            'stock'  => 'required|integer|min:0',
        ];
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function batchSize(): int
    {
        return 1000;
    }

    public function onFailure(Failure ...$failures): void
    {
        $log = ImportLog::find($this->importLogId);
        if (!$log) return;

        $errors = $log->errors ?? [];
        foreach ($failures as $failure) {
            $errors[] = [
                'row'      => $failure->row(),
                'attribute'=> $failure->attribute(),
                'errors'   => $failure->errors(),
                'values'   => $failure->values(),
            ];
        }
        $log->update([
            'errors'      => $errors,
            'failed_rows' => count($errors),
        ]);
    }
}
