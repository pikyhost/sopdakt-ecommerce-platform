@php
    $contact = \App\Models\Setting::getContactDetails();
@endphp
    <!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('thank_you') }}</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f8f9fa; margin: 0; padding: 0; direction: {{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}; text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }};">

<div style="max-width: 600px; margin: 20px auto; background: #ffffff; padding: 20px; border-radius: 12px; box-shadow: 0px 4px 20px rgba(0, 0, 0, 0.1);">
    <h1 style="color: #d63384; text-align: center; margin-bottom: 5px;">
        {{ \App\Models\Setting::getSetting('site_name') }}
    </h1>

    <h3 style="color: #d63384;">{{ __('thank_you') }}</h3>
    @if($order->user_id)
        <p style="font-size: 16px; color: #333; line-height: 1.6; margin: 10px 0;"><strong style="color: #d63384;">{{ __('Customer Name') }}:</strong> {{ $order->user->name }}</p>
    @elseif($order->contact_id)
        <p style="font-size: 16px; color: #333; line-height: 1.6; margin: 10px 0;"><strong style="color: #d63384;">{{ __('Contact Name') }}:</strong> {{ $order->contact->name }}</p>
    @endif
    <p style="font-size: 16px; color: #333; line-height: 1.6; margin: 10px 0;"><strong style="color: #d63384;">{{ __('order_id') }}:</strong> {{ $order->id }}</p>
    <p style="font-size: 16px; color: #333; line-height: 1.6; margin: 10px 0;"><strong style="color: #d63384;">{{ __('total_amount') }}:</strong> {{ number_format($order->total, 2) }}</p>
    <p style="font-size: 16px; color: #333; line-height: 1.6; margin: 10px 0;"><strong style="color: #d63384;">{{ __('payment_method') }}:</strong> {{ $order->paymentMethod->name ?? 'N/A' }}</p>

    <h2 style="color: #d63384;">{{ __('order_details') }}:</h2>
    <table width="100%" border="0" cellspacing="0" cellpadding="10" style="border-collapse: collapse;">
        @foreach ($order->items as $item)
            <tr style="border-bottom: 1px solid #ddd;">
                <td width="100" style="padding: 10px; text-align: center;">
                    @if($item->product->getFeatureProductImageUrl())
                        <img src="{{ $item->product->getFeatureProductImageUrl() }}" alt="{{ $item->product->getTranslation('name', app()->getLocale()) }}" width="80" height="auto" style="border-radius: 5px; display: block;">
                    @endif
                </td>
                <td style="padding: 10px; font-size: 14px; color: #333;">
                    <p style="margin: 0;"><strong style="color: #d63384;">{{ __('product') }}:</strong> {{ $item->product->getTranslation('name', app()->getLocale()) }}</p>
                    <p style="margin: 0;"><strong style="color: #d63384;">{{ __('quantity') }}:</strong> {{ $item->quantity }}</p>
                    <p style="margin: 0;"><strong style="color: #d63384;">{{ __('price') }}:</strong> {{ number_format($item->subtotal, 2) }}</p>

                    @if($item->product->productColors->isNotEmpty())
                        @if($item->size_id)
                            <p style="margin: 0;"><strong style="color: #d63384;">{{ __('size') }}:</strong> {{ $item->size->name }}</p>
                        @endif

                        <p style="margin: 0;"><strong style="color: #d63384;">{{ __('color') }}:</strong>
                            @foreach($item->product->productColors as $productColor)
                                <span style="display: inline-block; width: 12px; height: 12px; border-radius: 50%; border: 1px solid #ddd; margin-right: 5px; background-color:{{ $productColor->color->code }};"></span>
                                {{ $productColor->color->name }}
                            @endforeach
                        </p>
                    @endif
                </td>
            </tr>
        @endforeach
    </table>
    <div style="padding: 15px; background: #f0f8ff; border-left: 5px solid #007bff; border-radius: 5px; margin-top: 20px;">
        <p style="font-size: 18px; font-weight: bold; color: #333; margin: 0 0 10px 0;">{{ __('order_status') }}</p>
        <p style="font-size: 16px; color: #555; margin: 0;">{{ $statusMessage }}</p>
    </div>

    <div style="text-align: center; font-size: 14px; color: #777; margin-top: 20px; padding-top: 15px; border-top: 1px solid #ddd;">
        <div>
            <p style="margin: 5px 0;">
                <span style="color: #E4405F;">✉</span>
                {{ app()->getLocale() === 'ar' ? 'للتواصل عبر البريد الإلكتروني:' : 'Contact us via Email:' }}
                <a href="mailto:{{ $contact['email'] }}" style="color: #1877F2; text-decoration: none;">
                    {{ $contact['email'] }}
                </a>
            </p>
            <p style="margin: 5px 0;">
                <span style="color: #E4405F;">☎</span>
                {{ app()->getLocale() === 'ar' ? 'للتواصل عبر الهاتف:' : 'Contact us via Phone:' }}
                <a href="tel:{{ $contact['phone'] }}" style="color: #1877F2; text-decoration: none;">
                    {{ $contact['phone'] }}
                </a>
            </p>
        </div>

        <div style="margin-top: 15px;">
            @foreach (\App\Models\Setting::getSocialMediaLinks() as $platform => $link)
                @if ($link)
                    <a href="{{ $link }}" target="_blank" style="margin: 0 8px; display: inline-block; text-decoration: none; color:
                        @if(strtolower($platform) === 'facebook') #1877F2
                        @elseif(strtolower($platform) === 'x' || strtolower($platform) === 'twitter') #000000
                        @elseif(strtolower($platform) === 'instagram') #E4405F
                        @elseif(strtolower($platform) === 'youtube') #FF0000
                        @elseif(strtolower($platform) === 'linkedin') #0077B5
                        @elseif(strtolower($platform) === 'whatsapp') #25D366
                        @elseif(strtolower($platform) === 'snapchat') #FFFC00
                        @elseif(strtolower($platform) === 'tiktok') #000000
                        @elseif(strtolower($platform) === 'telegram') #0088CC
                        @else #333333
                        @endif;">
                        {{ ucfirst($platform) }}
                    </a>
                @endif
            @endforeach
        </div>
    </div>
</div>
</body>
</html>
