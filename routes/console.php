<?php

use App\Models\ScheduledTask;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ── Helper: Track scheduled task execution ────────────────────
function trackTask(string $command, string $frequency, \Closure $fn): void
{
    $start = microtime(true);
    $task  = ScheduledTask::create([
        'command'     => $command,
        'frequency'   => $frequency,
        'status'      => 'running',
        'started_at'  => now(),
    ]);

    try {
        $fn();
        $task->update([
            'status'      => 'success',
            'finished_at' => now(),
            'duration'    => microtime(true) - $start,
        ]);
    } catch (\Throwable $e) {
        $task->update([
            'status'      => 'failed',
            'output'      => $e->getMessage(),
            'finished_at' => now(),
            'duration'    => microtime(true) - $start,
        ]);
        Log::error("{$command} FAILED: " . $e->getMessage());
    }
}

// ── Scheduled Tasks ───────────────────────────────────────────

Schedule::command('backup:run')
    ->dailyAt('02:00')
    ->withoutOverlapping()
    ->onSuccess(function () {
        Log::info('backup:run completed successfully.');
        trackTask('backup:run', 'daily', fn() => null);
    })
    ->onFailure(function () { Log::error('backup:run FAILED.'); });

Schedule::command('backup:clean')
    ->dailyAt('03:00')
    ->withoutOverlapping()
    ->onSuccess(function () {
        Log::info('backup:clean completed successfully.');
        trackTask('backup:clean', 'daily', fn() => null);
    })
    ->onFailure(function () { Log::error('backup:clean FAILED.'); });

Schedule::command('order:cleanup-pending')
    ->hourly()
    ->withoutOverlapping()
    ->onSuccess(function () {
        Log::info('order:cleanup-pending completed.');
        trackTask('order:cleanup-pending', 'hourly', fn() => null);
    })
    ->onFailure(function () { Log::error('order:cleanup-pending FAILED.'); });

Schedule::command('auth:clear-resets')
    ->daily()
    ->onSuccess(function () {
        Log::info('session/reset cleanup completed.');
        trackTask('auth:clear-resets', 'daily', fn() => null);
    });

Schedule::command('log:rotate')
    ->weekly()
    ->withoutOverlapping()
    ->onSuccess(function () {
        Log::info('log:rotate completed.');
        trackTask('log:rotate', 'weekly', fn() => null);
    })
    ->onFailure(function () { Log::error('log:rotate FAILED.'); });

Schedule::command('report:generate-daily')
    ->dailyAt('06:00')
    ->withoutOverlapping()
    ->onSuccess(function () {
        Log::info('report:generate-daily completed.');
        trackTask('report:generate-daily', 'daily', fn() => null);
    })
    ->onFailure(function () { Log::error('report:generate-daily FAILED.'); });

Schedule::command('notification:prune')
    ->weekly()
    ->withoutOverlapping()
    ->onSuccess(function () {
        Log::info('notification:prune completed.');
        trackTask('notification:prune', 'weekly', fn() => null);
    });

Schedule::command('audit:archive')
    ->monthly()
    ->withoutOverlapping()
    ->onSuccess(function () {
        Log::info('audit:archive completed.');
        trackTask('audit:archive', 'monthly', fn() => null);
    })
    ->onFailure(function () { Log::error('audit:archive FAILED.'); });

// ── Session Cleanup (daily) ──────────────────────────────────
Schedule::command('session:cleanup')
    ->daily()
    ->withoutOverlapping()
    ->onSuccess(function () {
        Log::info('session:cleanup completed.');
        trackTask('session:cleanup', 'daily', fn() => null);
    })
    ->onFailure(function () { Log::error('session:cleanup FAILED.'); });
