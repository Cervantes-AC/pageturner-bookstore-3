<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CleanupPendingOrders extends Command
{
    protected $signature   = 'order:cleanup-pending';
    protected $description = 'Cancel pending orders older than 24 hours';

    public function handle(): int
    {
        $count = Order::where('status', 'pending')
            ->where('created_at', '<', now()->subHours(24))
            ->update(['status' => 'cancelled']);

        Log::info("order:cleanup-pending: cancelled {$count} stale orders.");
        $this->info("Cancelled {$count} pending orders older than 24 hours.");

        return self::SUCCESS;
    }
}
