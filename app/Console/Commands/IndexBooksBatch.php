<?php

namespace App\Console\Commands;

use App\Models\Book;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class IndexBooksBatch extends Command
{
    protected $signature = 'scout:import-books
                            {--chunk=500 : Number of records to index per chunk}';

    protected $description = 'Batch import books into Scout search index with progress tracking';

    public function handle(): int
    {
        $chunkSize = (int)$this->option('chunk');
        $total = Book::where('is_active', true)->count();

        $this->info("Importing {$total} active books into Scout index...");
        $this->newLine();

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $processed = 0;
        Book::where('is_active', true)
            ->chunkById($chunkSize, function ($books) use ($bar, &$processed) {
                $books->each->searchable();
                $processed += $books->count();
                $bar->advance($books->count());
            });

        $bar->finish();
        $this->newLine(2);
        $this->info("Successfully indexed {$processed} books.");

        return 0;
    }
}
