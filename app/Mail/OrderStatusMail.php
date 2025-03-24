<?php

namespace App\Mail;

use App\Models\Order;
use App\Enums\OrderStatus;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Content;

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

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.order-status',
            with: [
                'order' => $this->order,
                'statusMessage' => __('messages.order_status_' . $this->status->value),
            ],
        );
    }
}
