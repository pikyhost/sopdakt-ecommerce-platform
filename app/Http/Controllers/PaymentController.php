<?php

namespace App\Http\Controllers;

use App\Interfaces\PaymentGatewayInterface;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected PaymentGatewayInterface $paymentGateway;

    public function __construct(PaymentGatewayInterface $paymentGateway)
    {

        $this->paymentGateway = $paymentGateway;
    }


    public function paymentProcess(Request $request)
    {

        return $this->paymentGateway->sendPayment($request);
    }

    public function callback(Request $request)
    {
        $checkout = session('pending_checkout');

        if (!$checkout || $request->input('success') != 'true') {
            return redirect()->route('cart.index')->with('error', __('Payment failed or canceled.'));
        }

        $cart = \App\Models\Cart::with('items')->find($checkout['cart_id']);
        $contact = $checkout['contact_id'] ? \App\Models\Contact::find($checkout['contact_id']) : null;

        app()->call([$this, 'createOrderManually'], [
            'cart' => $cart,
            'contact' => $contact,
        ]);

        session()->forget('pending_checkout');

        return redirect()->route('order.complete')->with('order_success', true);
    }

    public function success()
    {

        return view('payment-success');
    }
    public function failed()
    {

        return view('payment-failed');
    }
}
