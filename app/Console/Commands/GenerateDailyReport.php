<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\ExportLog;
use App\Exports\OrdersExport;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;

class GenerateDailyReport extends Command
{
    protected $signature = 'report:generate-daily';
    protected $description = 'Generate daily sales report';

    public function handle()
    {
        $dateFrom = now()->subDay()->startOfDay();
        $dateTo = now()->subDay()->endOfDay();

        $filters = [
            'date_from' => $dateFrom->format('Y-m-d'),
            'date_to' => $dateTo->format('Y-m-d'),
        ];

        $fileName = 'daily-sales-report-' . $dateFrom->format('Y-m-d') . '.xlsx';

        Excel::store(new OrdersExport($filters), $fileName, 'public');

        ExportLog::create([
            'user_id' => 1,
            'type' => 'order',
            'format' => 'xlsx',
            'status' => 'completed',
            'file_path' => $fileName,
            'filters' => $filters,
        ]);

        $totalSales = Order::whereBetween('created_at', [$dateFrom, $dateTo])
            ->where('status', 'completed')
            ->sum('total_amount');

        $orderCount = Order::whereBetween('created_at', [$dateFrom, $dateTo])->count();

        $this->info("Daily report generated: {$fileName}");
        $this->info("Orders: {$orderCount}, Total Sales: \${$totalSales}");

        return Command::SUCCESS;
    }
}
