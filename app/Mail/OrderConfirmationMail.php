<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderConfirmationMail extends Mailable
{
    public $order;

    /**
     * Create a new message instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function build()
    {
        $locale = $this->order->user->preferred_language ?? config('app.locale');
        app()->setLocale($locale);

        return $this->subject((string) __('Order Confirmation') . ' - ' . $this->order->id)
            ->view('emails.order-confirmation')
            ->with([
                'order' => $this->order,
                'locale' => $locale // Pass locale explicitly
            ]);
    }

}
