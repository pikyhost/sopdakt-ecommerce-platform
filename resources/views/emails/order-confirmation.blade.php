<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('thank_you') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
            direction: {{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }};
            text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }};
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }
        h1, h2 {
            color: #d63384;
            text-align: center;
        }
        p {
            font-size: 16px;
            color: #333;
            line-height: 1.6;
        }
        .order-details {
            background: #fce8f2;
            padding: 15px;
            border-radius: 8px;
            margin: 10px 0;
        }
        .order-item {
            background: #fff;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
            border-left: 5px solid #d63384;
        }
        .footer {
            text-align: center;
            font-size: 14px;
            color: #777;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
        }
        strong {
            color: #d63384;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>{{ __('thank_you') }}</h1>
    <p><strong>{{ __('order_id') }}:</strong> {{ $order->id }}</p>
    <p><strong>{{ __('total_amount') }}:</strong> ${{ number_format($order->total, 2) }}</p>
    <p><strong>{{ __('payment_method') }}:</strong> {{ $order->paymentMethod->name ?? 'N/A' }}</p>

    <h2>{{ __('order_details') }}:</h2>
    <div class="order-details">
        @foreach ($order->items as $item)
            <div class="order-item">
                <p><strong>{{ __('product') }}:</strong> {{ $item->product->name }}</p>
                <p><strong>{{ __('quantity') }}:</strong> {{ $item->quantity }}</p>
                <p><strong>{{ __('price') }}:</strong> ${{ number_format($item->subtotal, 2) }}</p>

                @if($item->size)
                    <p><strong>{{ __('size') }}:</strong> {{ $item->size->name }}</p>
                @endif

                @if($item->color)
                    <p><strong>{{ __('color') }}:</strong>
                        <span style="display:inline-block; width:16px; height:16px; border-radius:50%; background-color:{{ $item->color->hex_code ?? '#000' }};"></span> {{ $item->color->name }}
                    </p>
                @endif
            </div>
        @endforeach
    </div>

    <p><strong>{{ __('order_status') }}:</strong> {{ __('shipping_now') }}</p>

    <div class="footer">
        <p>{{ __('footer_message') }}</p>
        <p>{{ __('customer_service') }}</p>
    </div>
</div>

</body>
</html>
