<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(private OrderService $orderService) {}

    public function index()
    {
        $orders = auth()->user()->orders()
            ->with('items')
            ->latest()
            ->paginate(10);

        return view('public.orders.index', compact('orders'));
    }

    public function success(string $orderCode)
    {
        $order = Order::where('order_code', $orderCode)
            ->where('user_id', auth()->id())
            ->with('items')
            ->firstOrFail();

        $waUrl = $order->getWaUrl();

        return view('public.orders.success', compact('order', 'waUrl'));
    }

    public function quickOrder(Request $request)
    {
        $request->validate([
            'product_id'        => 'required|exists:products,id',
            'qty'               => 'required|integer|min:1',
            'price_per_unit'    => 'required|numeric',
            'total'             => 'required|numeric',
            'recipient_name'    => 'required|string|max:255',
            'recipient_phone'   => 'required|string|max:20',
            'recipient_address' => 'required|string',
            'recipient_note'    => 'nullable|string',
        ]);

        if (!auth()->check()) {
            return response()->json(['redirect' => route('login')]);
        }

        $order = $this->orderService->createQuick(auth()->user(), $request->all());

        return response()->json([
            'success'    => true,
            'order_code' => $order->order_code,
            'wa_url'     => $order->getWaUrl(),
        ]);
    }
}
