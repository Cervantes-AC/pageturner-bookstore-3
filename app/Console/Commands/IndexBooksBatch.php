<?php

namespace App\Console\Commands;

use App\Models\Book;
use Illuminate\Console\Command;

class IndexBooksBatch extends Command
{
    protected $signature = 'scout:import-books
                            {--chunk=500 : Number of records to process per chunk}';
    protected $description = 'Import all active books into Scout search index in batches';

    public function handle(): int
    {
        $chunkSize = (int) $this->option('chunk');
        $this->info("Starting chunked Scout import (chunk size: {$chunkSize})...");

        $count = Book::where('is_active', true)->count();
        $this->info("Total active books to index: {$count}");

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        Book::where('is_active', true)
            ->chunkById($chunkSize, function ($books) use ($bar) {
                $books->each->searchable();
                $bar->advance($books->count());
            });

        $bar->finish();
        $this->newLine();
        $this->info('Scout import completed.');

        return 0;
    }
}
