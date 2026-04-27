<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class GenerateDailyReport extends Command
{
    protected $signature   = 'report:generate-daily';
    protected $description = 'Generate and email daily sales report to admins';

    public function handle(): int
    {
        $today = now()->toDateString();

        $orders = Order::whereDate('created_at', $today)
            ->with('orderItems')
            ->get();

        $revenue    = $orders->sum('total_amount');
        $orderCount = $orders->count();
        $itemCount  = $orders->sum(fn($o) => $o->orderItems->count());

        $report = [
            'date'        => $today,
            'orders'      => $orderCount,
            'items_sold'  => $itemCount,
            'revenue'     => $revenue,
            'generated_at'=> now()->toDateTimeString(),
        ];

        Log::info('Daily report generated', $report);

        // Email admins
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            try {
                Mail::raw(
                    "Daily Sales Report - {$today}\n\n" .
                    "Orders: {$orderCount}\n" .
                    "Items Sold: {$itemCount}\n" .
                    "Revenue: ₱" . number_format($revenue, 2) . "\n\n" .
                    "Generated at: " . now()->toDateTimeString(),
                    fn($m) => $m->to($admin->email)->subject("Daily Sales Report - {$today}")
                );
            } catch (\Exception $e) {
                Log::warning("Could not email report to {$admin->email}: " . $e->getMessage());
            }
        }

        $this->info("Daily report generated: {$orderCount} orders, ₱" . number_format($revenue, 2) . " revenue.");

        return self::SUCCESS;
    }
}
