<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderConfirmationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $order;

    /**
     * Create a new message instance.
     */
    public function __construct(Order $order)
    {
        // Ensure relationships are loaded before serialization
        $this->order = $order->load(['items.product', 'paymentMethod']);
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Order Confirmation - ' . $this->order->id)
            ->view('emails.order-confirmation')
            ->with([
                'order' => $this->order
            ]);
    }
}
