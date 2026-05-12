<?php

use Illuminate\Support\Facades\Schedule;
use App\Console\Commands\CleanupPendingOrders;
use App\Console\Commands\GenerateDailyReport;
use App\Console\Commands\PruneNotifications;
use App\Console\Commands\AuditArchive;
use App\Console\Commands\RotateLogs;

// Backup schedule
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

// Order cleanup - cancel pending orders > 24 hours
Schedule::command(CleanupPendingOrders::class)
    ->hourly()
    ->withoutOverlapping();

// Session cleanup
Schedule::command('session:gc')
    ->daily()
    ->description('Clear expired sessions');

// Log rotation - weekly
Schedule::command(RotateLogs::class)
    ->weekly()
    ->withoutOverlapping();

// Daily sales report at 6 AM
Schedule::command(GenerateDailyReport::class)
    ->dailyAt('06:00')
    ->withoutOverlapping();

// Prune old notifications (weekly)
Schedule::command(PruneNotifications::class)
    ->weekly()
    ->withoutOverlapping();

// Archive audit logs (monthly)
Schedule::command(AuditArchive::class)
    ->monthly()
    ->withoutOverlapping();
