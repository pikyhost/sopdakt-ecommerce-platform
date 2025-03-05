<ul class="checkout-progress-bar d-flex justify-content-center flex-wrap">
    <li class="{{ $currentRoute === 'cart.index' ? 'active' : '' }}">
        <a href="{{ route('cart.index') }}">Shopping Cart</a>
    </li>
    <li class="{{ $currentRoute === 'checkout.index' ? 'active' : '' }}">
        <a href="{{ route('checkout.index') }}">Checkout</a>
    </li>
    <li class="{{ $currentRoute === 'order.complete' ? 'active' : 'disabled' }}">
        <a href="{{ route('order.complete') }}">Order Complete</a>
    </li>
</ul>
