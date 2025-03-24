<?php

namespace App\Notifications;

use App\Models\Order;
use App\Enums\OrderStatus;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusNotification extends Notification
{
    public Order $order;
    public OrderStatus $status;

    /**
     * Create a new notification instance.
     */
    public function __construct(Order $order, OrderStatus $status)
    {
        $this->order = $order;
        $this->status = $status;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $statusMessage = __('messages.order_status_' . $this->status->value);

        return (new MailMessage)
            ->subject(__('Order') . ' #' . $this->order->id . ' - ' . __($this->status->getLabel()))
            ->view('emails.order-status', [
                'order' => $this->order,
                'statusMessage' => $statusMessage,
            ]);
    }
}
