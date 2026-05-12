<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;

class CleanupPendingOrders extends Command
{
    protected $signature = 'order:cleanup-pending';
    protected $description = 'Cancel pending orders older than 24 hours';

    public function handle()
    {
        $count = Order::where('status', 'pending')
            ->where('created_at', '<', now()->subHours(24))
            ->update(['status' => 'cancelled']);

        $this->info("Cancelled {$count} pending orders older than 24 hours.");
        return Command::SUCCESS;
    }
}
