<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Orders Export</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #333; }
        h1 { font-size: 18px; margin-bottom: 4px; }
        .subtitle { font-size: 11px; color: #666; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        th { background: #f3f4f6; padding: 8px 6px; text-align: left; font-size: 9px; font-weight: 600; border-bottom: 2px solid #d1d5db; }
        td { padding: 6px; border-bottom: 1px solid #e5e7eb; font-size: 9px; }
        .total-row td { font-weight: bold; border-top: 2px solid #d1d5db; }
        .badge { display: inline-block; padding: 1px 6px; border-radius: 4px; font-size: 8px; }
        .badge-pending { background: #fef3c7; color: #92400e; }
        .badge-completed { background: #d1fae5; color: #065f46; }
        .badge-cancelled { background: #fee2e2; color: #991b1b; }
        .footer { font-size: 8px; color: #999; text-align: center; margin-top: 20px; border-top: 1px solid #e5e7eb; padding-top: 8px; }
    </style>
</head>
<body>
    <h1>Orders Export</h1>
    <p class="subtitle">
        Generated: {{ now()->format('F d, Y H:i') }}
        @if(!empty($filters))
            @if(!empty($filters['status'])) · Status: {{ $filters['status'] }} @endif
            @if(!empty($filters['date_from'])) · From: {{ $filters['date_from'] }} @endif
            @if(!empty($filters['date_to'])) · To: {{ $filters['date_to'] }} @endif
        @endif
    </p>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Customer</th>
                <th>Email</th>
                <th>Status</th>
                <th>Items</th>
                <th>Total</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
            <tr>
                <td>{{ $order->id }}</td>
                <td>{{ $order->user?->name ?? 'N/A' }}</td>
                <td>{{ $order->user?->email ?? 'N/A' }}</td>
                <td><span class="badge badge-{{ $order->status }}">{{ ucfirst($order->status) }}</span></td>
                <td>{{ $order->orderItems->count() }}</td>
                <td>₱{{ number_format($order->total_amount, 2) }}</td>
                <td>{{ $order->created_at?->format('M d, Y') }}</td>
            </tr>
            @empty
            <tr><td colspan="7" style="text-align:center;color:#999;">No orders found</td></tr>
            @endforelse
        </tbody>
        @if($orders->count() > 0)
        <tfoot>
            <tr class="total-row">
                <td colspan="5" style="text-align:right;">Total Revenue</td>
                <td colspan="2">₱{{ number_format(isset($revenue) ? $revenue : $orders->sum('total_amount'), 2) }}</td>
            </tr>
        </tfoot>
        @endif
    </table>

    <div class="footer">
        PageTurner Bookstore · Orders Export · {{ now()->format('Y-m-d H:i:s') }}
    </div>
</body>
</html>
