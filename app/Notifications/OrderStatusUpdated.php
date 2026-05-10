<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Notifications\Notification;

class OrderStatusUpdated extends Notification
{
    public function __construct(public Order $order) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'order_code' => $this->order->order_code,
            'status'     => $this->order->status,
            'label'      => $this->order->status_label,
            'message'    => "Status pesanan #{$this->order->order_code} diperbarui: {$this->order->status_label}",
        ];
    }
}
