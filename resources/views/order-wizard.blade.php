<ul class="checkout-progress-bar d-flex justify-content-center flex-wrap {{ app()->getLocale() === 'ar' ? 'flex-row-reverse' : '' }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
    <li class="{{ $currentRoute === 'cart.index' ? 'active' : '' }}">
        <a href="{{ route('cart.index') }}">{{ __('shopping_cart') }}</a>
    </li>
    <li class="{{ $currentRoute === 'checkout.index' ? 'active' : '' }}">
        <a href="{{ route('checkout.index') }}">{{ __('checkout') }}</a>
    </li>
    <li class="{{ $currentRoute === 'order.complete' ? 'active' : 'disabled' }}">
        <a href="{{ route('order.complete') }}">{{ __('order_complete') }}</a>
    </li>
</ul>
