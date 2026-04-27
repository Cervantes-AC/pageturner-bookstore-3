<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class OrdersExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    protected array $filters;
    protected ?int $userId;

    public function __construct(array $filters = [], ?int $userId = null)
    {
        $this->filters = $filters;
        $this->userId  = $userId;
    }

    public function query()
    {
        $query = Order::with(['user', 'orderItems.book']);

        if ($this->userId) {
            $query->where('user_id', $this->userId);
        }
        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }
        if (!empty($this->filters['date_from'])) {
            $query->whereDate('created_at', '>=', $this->filters['date_from']);
        }
        if (!empty($this->filters['date_to'])) {
            $query->whereDate('created_at', '<=', $this->filters['date_to']);
        }

        return $query->latest();
    }

    public function headings(): array
    {
        return ['Order ID', 'Customer', 'Email', 'Status', 'Total', 'Items', 'Shipping Name', 'Date'];
    }

    public function map($order): array
    {
        return [
            $order->id,
            $order->user?->name,
            $order->user?->email,
            ucfirst($order->status),
            '₱' . number_format($order->total_amount, 2),
            $order->orderItems->count(),
            $order->shipping_name,
            $order->created_at?->format('Y-m-d H:i'),
        ];
    }
}
