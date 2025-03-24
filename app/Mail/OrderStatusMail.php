<?php

namespace App\Mail;

use App\Models\Order;
use App\Enums\OrderStatus;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public Order $order;
    public OrderStatus $status;

    /**
     * Create a new message instance.
     */
    public function __construct(Order $order, OrderStatus $status)
    {
        $this->order = $order;
        $this->status = $status;
    }

    public function build()
    {
        $locale = $this->order->user->preferred_language ?? config('app.locale');
        app()->setLocale($locale);

        // Get a more user-friendly order status message
        $statusMessage = __('messages.order_status_' . $this->status->value);

        return $this->subject(__('Order') . ' #' . $this->order->id . ' - ' . __($this->status->getLabel()))
            ->view('emails.order-status')
            ->with([
                'order' => $this->order,
                'statusMessage' => $statusMessage,
                'locale' => $locale,
            ]);
    }
}
