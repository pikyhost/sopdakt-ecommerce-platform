<?php

namespace App\Mail;

use App\Models\Order;
use App\Enums\OrderStatus;
use Illuminate\Bus\Queueable;
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
        return $this->subject(__('Order') . ' #' . $this->order->id . ' - ' . __($this->status->getLabel()))
            ->view('emails.order-status')
            ->with([
                'order' => $this->order,
                'orderItems' => $this->order->items,
                'statusMessage' => __('messages.order_status_' . $this->status->value),
            ]);
    }

}
