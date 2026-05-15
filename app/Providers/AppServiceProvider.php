<?php

namespace App\Providers;

use App\Models\Book;
use App\Observers\BookObserver;
use App\Repositories\BookRepository;
use App\Services\BookCacheService;
use App\Listeners\AddTcpProtocolToBackup;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Spatie\Backup\Events\DumpingDatabase;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(BookCacheService::class);
        $this->app->singleton(BookRepository::class);
    }

    public function boot(): void
    {
        Book::observe(BookObserver::class);

        // Register listener to add TCP protocol to MySQL backups on Windows
        Event::listen(DumpingDatabase::class, AddTcpProtocolToBackup::class);

        if ($this->app->environment('local')) {
            DB::listen(function ($query) {
                if ($query->time > 100) {
                    logger()->warning('Slow query detected', [
                        'sql' => $query->sql,
                        'time' => $query->time . ' ms',
                        'bindings' => $query->bindings,
                    ]);
                }
            });
        }
    }
}
