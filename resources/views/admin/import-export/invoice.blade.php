<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $order->id }}</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { color: #059669; margin: 0; font-size: 24px; }
        .header p { color: #6b7280; margin: 5px 0 0; }
        .details { margin-bottom: 20px; }
        .details table { width: 100%; }
        .details td { padding: 4px 0; }
        .items { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .items th { background: #f3f4f6; padding: 8px; text-align: left; font-size: 11px; }
        .items td { padding: 8px; border-bottom: 1px solid #e5e7eb; }
        .total { text-align: right; font-size: 16px; font-weight: bold; color: #059669; }
        .footer { text-align: center; color: #9ca3af; font-size: 10px; margin-top: 30px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>PageTurner Bookstore</h1>
        <p>Invoice #{{ $order->id }}</p>
    </div>

    <div class="details">
        <table>
            <tr><td><strong>Order Date:</strong></td><td>{{ $order->created_at->format('F d, Y') }}</td></tr>
            <tr><td><strong>Status:</strong></td><td>{{ ucfirst($order->status) }}</td></tr>
            <tr><td><strong>Customer:</strong></td><td>{{ $order->user->name }}</td></tr>
            <tr><td><strong>Email:</strong></td><td>{{ $order->user->email }}</td></tr>
            <tr><td><strong>Shipping:</strong></td><td>{{ $order->shipping_address }}</td></tr>
        </table>
    </div>

    <table class="items">
        <thead>
            <tr>
                <th>Book</th>
                <th>Qty</th>
                <th>Price</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->orderItems as $item)
            <tr>
                <td>{{ $item->book->title }}</td>
                <td>{{ $item->quantity }}</td>
                <td>${{ number_format($item->unit_price, 2) }}</td>
                <td>${{ number_format($item->unit_price * $item->quantity, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total">
        Total: ${{ number_format($order->total_amount, 2) }}
    </div>

    <div class="footer">
        <p>Thank you for shopping at PageTurner Bookstore!</p>
        <p>For questions, contact support@pageturner.com</p>
    </div>
</body>
</html>
