<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class OrdersExport implements FromQuery, WithHeadings, WithMapping, WithChunkReading
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = Order::with('user', 'orderItems.book');

        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }
        if (!empty($this->filters['user_id'])) {
            $query->where('user_id', $this->filters['user_id']);
        }
        if (!empty($this->filters['date_from'])) {
            $query->whereDate('created_at', '>=', $this->filters['date_from']);
        }
        if (!empty($this->filters['date_to'])) {
            $query->whereDate('created_at', '<=', $this->filters['date_to']);
        }

        $query->orderBy('created_at', 'desc');
        return $query;
    }

    public function headings(): array
    {
        return [
            'Order ID',
            'Customer',
            'Email',
            'Total Amount',
            'Status',
            'Items Count',
            'Created At',
        ];
    }

    public function map($order): array
    {
        return [
            $order->id,
            $order->user->name ?? 'N/A',
            $order->user->email ?? 'N/A',
            $order->total_amount,
            $order->status,
            $order->orderItems->sum('quantity'),
            $order->created_at->format('Y-m-d H:i:s'),
        ];
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
