<?php

namespace App\Notifications;

use App\Models\Order\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class MemberOrderPlaced extends Notification
{
    use Queueable;

    public function __construct(
        private readonly Order $order,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Order placed',
            'body' => 'Your order '.$this->order->order_number.' is awaiting payment confirmation.',
            'order_number' => $this->order->order_number,
        ];
    }
}
