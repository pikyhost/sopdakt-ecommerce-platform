<!DOCTYPE html>
<html>
<head>
    <title>Order Confirmation</title>
</head>
<body>
<h1>Thank you for your order!</h1>
<p>Order ID: {{ $order->id }}</p>
<p>Total Amount: ${{ number_format($order->total, 2) }}</p>
<p>Payment Method: {{ $order->paymentMethod->name ?? 'N/A' }}</p>
<h2>Order Details:</h2>
<ul>
    @foreach ($order->items as $item)
        <li>{{ $item->product->name }} - Quantity: {{ $item->quantity }} - ${{ number_format($item->subtotal, 2) }}</li>
    @endforeach
</ul>
<p>The order status is shipping now.</p>
</body>
</html>
