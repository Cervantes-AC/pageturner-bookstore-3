<?php

use Illuminate\Support\Facades\Schedule;
use App\Console\Commands\CleanupPendingOrders;
use App\Console\Commands\GenerateDailyReport;
use App\Console\Commands\PruneNotifications;
use App\Console\Commands\AuditArchive;
use App\Console\Commands\RotateLogs;

Schedule::command('backup:run')
    ->dailyAt('02:00')
    ->withoutOverlapping()
    ->onSuccess(function () {
        \Illuminate\Support\Facades\Log::info('Scheduled backup completed successfully.');
    })
    ->onFailure(function () {
        \Illuminate\Support\Facades\Log::error('Scheduled backup failed!');
    });

Schedule::command('backup:clean')
    ->dailyAt('03:00')
    ->withoutOverlapping();

Schedule::command(CleanupPendingOrders::class)
    ->hourly()
    ->withoutOverlapping();

Schedule::command('session:gc')
    ->daily()
    ->description('Clear expired sessions');

Schedule::command(RotateLogs::class)
    ->weekly()
    ->withoutOverlapping();

Schedule::command(GenerateDailyReport::class)
    ->dailyAt('06:00')
    ->withoutOverlapping();

Schedule::command(PruneNotifications::class)
    ->weekly()
    ->withoutOverlapping();

Schedule::command(AuditArchive::class)
    ->monthly()
    ->withoutOverlapping();

Schedule::command('app:refresh-materialized-views')
    ->hourly()
    ->withoutOverlapping()
    ->description('Refresh bestseller stats materialized view');

Schedule::command('scout:import-books')
    ->dailyAt('04:00')
    ->withoutOverlapping()
    ->description('Batch import books into Scout search index');
