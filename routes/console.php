<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ── Scheduled Tasks ───────────────────────────────────────────────────────────

Schedule::command('backup:run')
    ->dailyAt('02:00')
    ->withoutOverlapping()
    ->onSuccess(function () { Log::info('backup:run completed successfully.'); })
    ->onFailure(function () { Log::error('backup:run FAILED.'); });

Schedule::command('backup:clean')
    ->dailyAt('03:00')
    ->withoutOverlapping()
    ->onSuccess(function () { Log::info('backup:clean completed successfully.'); })
    ->onFailure(function () { Log::error('backup:clean FAILED.'); });

Schedule::command('order:cleanup-pending')
    ->hourly()
    ->withoutOverlapping()
    ->onSuccess(function () { Log::info('order:cleanup-pending completed.'); })
    ->onFailure(function () { Log::error('order:cleanup-pending FAILED.'); });

Schedule::command('auth:clear-resets')
    ->daily()
    ->onSuccess(function () { Log::info('session/reset cleanup completed.'); });

Schedule::command('log:rotate')
    ->weekly()
    ->withoutOverlapping()
    ->onSuccess(function () { Log::info('log:rotate completed.'); })
    ->onFailure(function () { Log::error('log:rotate FAILED.'); });

Schedule::command('report:generate-daily')
    ->dailyAt('06:00')
    ->withoutOverlapping()
    ->onSuccess(function () { Log::info('report:generate-daily completed.'); })
    ->onFailure(function () { Log::error('report:generate-daily FAILED.'); });

Schedule::command('notification:prune')
    ->weekly()
    ->withoutOverlapping()
    ->onSuccess(function () { Log::info('notification:prune completed.'); });

Schedule::command('audit:archive')
    ->monthly()
    ->withoutOverlapping()
    ->onSuccess(function () { Log::info('audit:archive completed.'); })
    ->onFailure(function () { Log::error('audit:archive FAILED.'); });
