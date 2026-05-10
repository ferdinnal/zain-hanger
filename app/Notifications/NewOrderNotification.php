<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Notifications\Notification;

class NewOrderNotification extends Notification
{
    public function __construct(public Order $order) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'order_id'      => $this->order->id,
            'order_code'    => $this->order->order_code,
            'customer_name' => $this->order->customer_name,
            'total'         => $this->order->total_formatted,
            'status'        => $this->order->status,
            'items_count'   => $this->order->items->count(),
            'message'       => "Pesanan baru #{$this->order->order_code} dari {$this->order->customer_name}",
            'url'           => '/admin/orders/' . $this->order->id,
        ];
    }
}
