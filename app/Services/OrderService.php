<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Notifications\NewOrderNotification;
use Illuminate\Support\Collection;

class OrderService
{
    public function createFromCart(User $user, Collection $cartItems, array $data): Order
    {
        $total = $cartItems->sum->subtotal;

        $order = Order::create([
            'user_id'          => $user->id,
            'customer_name'    => $data['customer_name'],
            'customer_email'   => $user->email,
            'customer_phone'   => $data['customer_phone'],
            'shipping_address' => $data['shipping_address'] ?? null,
            'notes'            => $data['notes'] ?? null,
            'total_amount'     => $total,
            'status'           => 'pending',
            'source'           => 'cart',
        ]);

        foreach ($cartItems as $cartItem) {
            $tier         = $cartItem->product->getPriceForQty($cartItem->qty);
            $pricePerUnit = $tier?->price ?? 0;

            OrderItem::create([
                'order_id'         => $order->id,
                'product_id'       => $cartItem->product_id,
                'product_snapshot' => $cartItem->product->toSnapshot(),
                'qty'              => $cartItem->qty,
                'price_per_unit'   => $pricePerUnit,
                'subtotal'         => $pricePerUnit * $cartItem->qty,
            ]);
        }

        $this->notifyAdmins($order->load('items'));
        $this->markWaSent($order);

        return $order;
    }

    public function createQuick(User $user, array $data): Order
    {
        $product = Product::with('priceTiers', 'category')->findOrFail($data['product_id']);
        $qty     = (int) $data['qty'];
        $tier    = $product->getPriceForQty($qty);
        $price   = $tier?->price ?? $data['price_per_unit'];

        $order = Order::create([
            'user_id'        => $user->id,
            'customer_name'  => $user->name,
            'customer_email' => $user->email,
            'customer_phone' => $user->phone ?? '',
            'total_amount'   => $price * $qty,
            'status'         => 'pending',
            'source'         => 'direct',
        ]);

        OrderItem::create([
            'order_id'         => $order->id,
            'product_id'       => $product->id,
            'product_snapshot' => $product->toSnapshot(),
            'qty'              => $qty,
            'price_per_unit'   => $price,
            'subtotal'         => $price * $qty,
        ]);

        $this->notifyAdmins($order->load('items'));
        $this->markWaSent($order);

        return $order;
    }

    public function updateStatus(Order $order, string $newStatus): Order
    {
        $order->update(['status' => $newStatus]);

        if ($order->user) {
            $order->user->notify(new \App\Notifications\OrderStatusUpdated($order));
        }

        return $order;
    }

    private function notifyAdmins(Order $order): void
    {
        User::whereIn('role', ['admin', 'superadmin'])->get()
            ->each(fn ($admin) => $admin->notify(new NewOrderNotification($order)));
    }

    private function markWaSent(Order $order): void
    {
        $order->update([
            'wa_sent_at'         => now(),
            'wa_message_preview' => substr($order->buildWaMessage(), 0, 200),
        ]);
    }
}
