<?php

namespace App\Exports;

use App\Models\Order;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class OrdersExport implements FromQuery, WithHeadings, WithMapping, WithChunkReading
{
    protected $filters;
    protected $userCache = [];
    protected $userNames = [];

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
        // Pre-load all users into memory once
        $this->userCache = User::pluck('email', 'id')->toArray();
        $this->userNames = User::pluck('name', 'id')->toArray();
    }

    public function query()
    {
        $query = Order::select(['id', 'user_id', 'total_amount', 'status', 'created_at']);

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
        $itemsCount = $order->orderItems()->sum('quantity');
        
        return [
            $order->id,
            $this->userNames[$order->user_id] ?? 'N/A',
            $this->userCache[$order->user_id] ?? 'N/A',
            $order->total_amount,
            $order->status,
            $itemsCount,
            $order->created_at->format('Y-m-d H:i:s'),
        ];
    }

    public function chunkSize(): int
    {
        return 250;
    }
}
