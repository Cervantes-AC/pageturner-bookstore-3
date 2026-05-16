<?php

namespace Tests\Feature;

use App\Exports\BooksExport;
use App\Exports\QueuedBooksExport;
use App\Imports\BooksImport;
use App\Imports\QueuedBooksImport;
use App\Models\AuditLog;
use App\Models\BackupMonitoring;
use App\Models\Book;
use App\Models\Category;
use App\Models\ImportLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Excel as ExcelWriter;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class OperationalValidationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'scout.driver' => null,
            'queue.default' => 'sync',
        ]);
    }

    public function test_book_import_uses_chunking_and_batch_inserts(): void
    {
        $import = new BooksImport($this->importLog(), false);

        $this->assertSame(1000, $import->chunkSize());
        $this->assertSame(1000, $import->batchSize());
    }

    public function test_malformed_book_import_reports_validation_failures(): void
    {
        $path = $this->writeBookCsv('malformed-books.csv', [
            ['9780000000001', 'Valid Book', 'Ada Writer', '19.99', '10', 'Fiction', 'paperback', 'Valid row'],
            ['bad-isbn', 'Broken ISBN', 'Ada Writer', '19.99', '10', 'Fiction', 'paperback', 'Invalid ISBN'],
            ['9780000000003', '', 'Ada Writer', 'free', '10', 'Fiction', 'paperback', 'Missing title and bad price'],
        ]);

        $import = new BooksImport($this->importLog(), false);

        Excel::import($import, $path, null, ExcelWriter::CSV);

        $this->assertDatabaseCount('books', 1);
        $this->assertCount(3, $import->failures());
    }

    public function test_books_export_is_query_backed_and_chunked_for_large_files(): void
    {
        $export = new BooksExport();

        $this->assertSame(500, $export->chunkSize());
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Builder::class, $export->query());
    }

    public function test_queued_import_and_export_jobs_complete(): void
    {
        Storage::fake('public');
        Category::factory()->create(['name' => 'Queued']);

        $path = $this->writeBookCsv('queued-books.csv', [
            ['9780000000201', 'Queued Book 1', 'Queue Author', '19.99', '10', 'Queued', 'paperback', 'Queued row'],
            ['9780000000202', 'Queued Book 2', 'Queue Author', '20.99', '8', 'Queued', 'paperback', 'Queued row'],
        ]);

        Excel::queueImport(new QueuedBooksImport($this->importLog(), false), $path, null, ExcelWriter::CSV);

        $this->assertDatabaseCount('books', 2);

        Excel::queue(new QueuedBooksExport(), 'queued-books.csv', 'public', ExcelWriter::CSV);

        Storage::disk('public')->assertExists('queued-books.csv');
    }

    public function test_large_book_import_stays_under_memory_limit_when_enabled(): void
    {
        if (! env('RUN_LARGE_VALIDATION_TESTS')) {
            $this->markTestSkipped('Set RUN_LARGE_VALIDATION_TESTS=1 to run the 10,000+ row import validation.');
        }

        $path = $this->writeBookCsv('large-books.csv', $this->bookRows(10025));
        $import = new BooksImport($this->importLog(), false);

        Excel::import($import, $path, null, ExcelWriter::CSV);

        $this->assertDatabaseCount('books', 10025);
        $this->assertLessThan(256 * 1024 * 1024, memory_get_peak_usage(true));
    }

    public function test_large_book_export_completes_when_enabled(): void
    {
        if (! env('RUN_LARGE_VALIDATION_TESTS')) {
            $this->markTestSkipped('Set RUN_LARGE_VALIDATION_TESTS=1 to run the 50,000+ row export validation.');
        }

        Storage::fake('public');
        $category = Category::factory()->create();

        foreach (array_chunk(iterator_to_array($this->rawBookRows(50001, $category->id)), 1000) as $chunk) {
            DB::table('books')->insert($chunk);
        }

        Excel::store(new BooksExport(), 'validation-books.csv', 'public', ExcelWriter::CSV);

        Storage::disk('public')->assertExists('validation-books.csv');
        $this->assertSame(50001, Book::count());
    }

    public function test_manual_backup_execution_records_successful_backup(): void
    {
        Storage::fake('local');
        $admin = $this->admin();
        $backupDirectory = config('backup.backup.name');

        $kernel = \Mockery::mock(\Illuminate\Foundation\Console\Kernel::class)->makePartial();
        $kernel->shouldReceive('call')
            ->with('backup:run', ['--only-db' => true])
            ->andReturnUsing(function () use ($backupDirectory) {
                Storage::disk('local')->put($backupDirectory . '/manual-backup.zip', 'backup-bytes');
                return 0;
            });
        $kernel->shouldReceive('output')->andReturn('Backup completed.');
        $this->app[\Illuminate\Contracts\Console\Kernel::class] = $kernel;

        $this->actingAs($admin)
            ->post(route('admin.backup.run'))
            ->assertRedirect(route('admin.backup.index'));

        $backup = BackupMonitoring::first();

        $this->assertSame('success', $backup->status);
        $this->assertSame($backupDirectory . '/manual-backup.zip', $backup->file_path);
        Storage::disk('local')->assertExists($backup->file_path);
    }

    public function test_scheduled_backup_and_retention_policy_are_configured(): void
    {
        Artisan::call('schedule:list');
        $output = Artisan::output();

        $this->assertStringContainsString('backup:run', $output);
        $this->assertStringContainsString('backup:clean', $output);
        $this->assertSame(7, config('backup.cleanup.default_strategy.keep_all_backups_for_days'));
        $this->assertSame(5000, config('backup.cleanup.default_strategy.delete_oldest_backups_when_using_more_megabytes_than'));
    }

    public function test_audit_entries_redact_sensitive_data_and_detect_tampering(): void
    {
        $category = Category::factory()->create();

        $this->actingAs($this->admin())
            ->post(route('admin.books.store'), [
                'category_id' => $category->id,
                'title' => 'Audited Book',
                'author' => 'Audit Author',
                'price' => 25,
                'stock_quantity' => 5,
                'description' => 'Tracked',
                'password' => 'secret-value',
            ]);

        $audit = AuditLog::where('event', 'created')->firstOrFail();

        $this->assertSame('[REDACTED]', $audit->new_values['password']);
        $this->assertTrue($audit->hasValidChecksum());

        $audit->forceFill(['new_values' => ['title' => 'Changed after the fact']]);

        $this->assertFalse($audit->hasValidChecksum());
    }

    public function test_audit_log_filtering_by_event(): void
    {
        AuditLog::create([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'user_id' => $this->admin()->id,
            'event' => 'created',
            'auditable_type' => Book::class,
            'auditable_id' => 1,
            'checksum' => 'example',
        ]);

        $this->actingAs($this->admin())
            ->get(route('admin.audit.index', ['event' => 'created']))
            ->assertOk()
            ->assertSee('created');
    }

    public function test_rate_limiting_returns_429_with_headers_and_burst_protection(): void
    {
        config(['rate-limiting.tiers.validation-burst' => ['limit' => 2, 'decay' => 1]]);
        RateLimiter::clear('api|ip:127.0.0.1');

        Route::middleware('api-rate-limiter:validation-burst')
            ->get('/validation/rate-limit', fn () => response()->json(['ok' => true]));

        $this->getJson('/validation/rate-limit')->assertOk()
            ->assertHeader('X-RateLimit-Limit', '2')
            ->assertHeader('X-RateLimit-Remaining', '1');

        $this->getJson('/validation/rate-limit')->assertOk()
            ->assertHeader('X-RateLimit-Remaining', '0');

        $this->getJson('/validation/rate-limit')
            ->assertStatus(429)
            ->assertHeader('X-RateLimit-Limit', '2')
            ->assertHeader('X-RateLimit-Remaining', '0')
            ->assertHeader('Retry-After');
    }

    public function test_tiered_rate_limits_use_user_role_limits(): void
    {
        $this->assertSame('standard', User::factory()->make(['role' => 'customer'])->getRateLimitTier());
        $this->assertSame('premium', User::factory()->make(['role' => 'premium'])->getRateLimitTier());
        $this->assertSame('admin', User::factory()->make(['role' => 'admin'])->getRateLimitTier());
    }

    private function admin(): User
    {
        return User::factory()->create([
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);
    }

    private function importLog(): ImportLog
    {
        return ImportLog::create([
            'user_id' => $this->admin()->id,
            'filename' => 'validation.csv',
            'type' => 'book',
            'status' => 'processing',
        ]);
    }

    private function writeBookCsv(string $filename, iterable $rows): string
    {
        $path = storage_path('framework/testing/' . $filename);

        if (! is_dir(dirname($path))) {
            mkdir(dirname($path), 0777, true);
        }

        $file = new \SplFileObject($path, 'w');
        $file->fputcsv(['isbn', 'title', 'author', 'price', 'stock', 'category', 'format', 'description']);

        foreach ($rows as $row) {
            $file->fputcsv($row);
        }

        return $path;
    }

    private function bookRows(int $count): \Generator
    {
        for ($i = 1; $i <= $count; $i++) {
            yield [
                '978' . str_pad((string) $i, 10, '0', STR_PAD_LEFT),
                'Validation Book ' . $i,
                'Author ' . $i,
                '19.99',
                '10',
                'Validation',
                'paperback',
                'Large import validation row.',
            ];
        }
    }

    private function rawBookRows(int $count, int $categoryId): \Generator
    {
        for ($i = 1; $i <= $count; $i++) {
            yield [
                'category_id' => $categoryId,
                'title' => 'Export Book ' . $i,
                'author' => 'Author ' . $i,
                'isbn' => '978' . str_pad((string) $i, 10, '0', STR_PAD_LEFT),
                'price' => 19.99,
                'format' => 'paperback',
                'stock_quantity' => 10,
                'description' => 'Large export validation row.',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
    }
}
