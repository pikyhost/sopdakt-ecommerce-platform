<x-mail::message>
    # Thank You for Your Order!

    **Customer Name:** {{ $order->user->name ?? $order->contact->name }}
    **Order ID:** {{ $order->id }}
    **Total Amount:** {{ number_format($order->total, 2) }}
    **Payment Method:** {{ $order->paymentMethod->name ?? 'N/A' }}

    ## Order Details:
    <x-mail::table>
        | Product | Qty | Price |
        |---------|-----|------:|
        @foreach ($order->items as $item)
            | {{ $item->product->getTranslation('name', app()->getLocale()) }} | {{ $item->quantity }} | {{ number_format($item->subtotal, 2) }} |
        @endforeach
    </x-mail::table>

    **Order Status:** {{ $statusMessage }}

    <x-mail::panel>
        For any inquiries, contact us at
        [{{ config('app.email') }}](mailto:{{ config('app.email') }}).
    </x-mail::panel>

    Thanks,
    {{ config('app.name') }}
</x-mail::message>
