@php
    $contact = \App\Models\Setting::getContactDetails();
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f8f9fa; padding: 20px; text-align: left;">

<div style="max-width: 600px; margin: auto; background: #ffffff; padding: 15px; border: 1px solid #ddd;">
    <h2 style="color: #333;">Thank You for Your Order!</h2>

    <p><strong>Customer Name:</strong> {{ $order->user->name ?? $order->contact->name }}</p>
    <p><strong>Order ID:</strong> {{ $order->id }}</p>
    <p><strong>Total Amount:</strong> {{ number_format($order->total, 2) }}</p>
    <p><strong>Payment Method:</strong> {{ $order->paymentMethod->name ?? 'N/A' }}</p>

    <h3>Order Details:</h3>
    <table width="100%" border="1" cellpadding="5" cellspacing="0">
        <thead>
        <tr>
            <th>Product</th>
            <th>Qty</th>
            <th>Price</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($order->items as $item)
            <tr>
                <td>{{ $item->product->getTranslation('name', app()->getLocale()) }}</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ number_format($item->subtotal, 2) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <p><strong>Order Status:</strong> {{ $statusMessage }}</p>

    <p>For any inquiries, contact us at <a href="mailto:{{ $contact['email'] }}">{{ $contact['email'] }}</a></p>
</div>

</body>
</html>
