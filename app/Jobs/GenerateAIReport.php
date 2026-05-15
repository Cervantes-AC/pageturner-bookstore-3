<?php

namespace App\Jobs;

use App\Models\AIReport;
use App\Services\AIReportGeneratorService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateAIReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 300;
    public int $tries = 3;
    public array $backoff = [30, 60, 120];

    protected AIReport $report;

    public function __construct(AIReport $report)
    {
        $this->report = $report;
    }

    public function handle(AIReportGeneratorService $generator): void
    {
        $generator->processReport($this->report);
    }

    public function failed(\Throwable $e): void
    {
        $this->report->update([
            'status' => 'failed',
            'error_message' => 'Queue processing failed after ' . $this->tries . ' attempts: ' . $e->getMessage(),
            'completed_at' => now(),
        ]);
    }
}
