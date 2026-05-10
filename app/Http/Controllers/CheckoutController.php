<?php

namespace App\Http\Controllers;

use App\Services\OrderService;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function __construct(private OrderService $orderService)
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $cartItems = auth()->user()->cartItems()
            ->with(['product.priceTiers', 'product.category'])
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang masih kosong.');
        }

        $total          = $cartItems->sum->subtotal;
        $totalFormatted = 'Rp ' . number_format($total, 0, ',', '.');

        return view('public.checkout.index', compact('cartItems', 'total', 'totalFormatted'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name'    => 'required|string|max:255',
            'customer_phone'   => 'required|string|max:20',
            'shipping_address' => 'nullable|string',
            'notes'            => 'nullable|string',
        ]);

        $cartItems = auth()->user()->cartItems()
            ->with(['product.priceTiers'])
            ->get();

        if ($cartItems->isEmpty()) {
            return back()->with('error', 'Keranjang kosong.');
        }

        $order = $this->orderService->createFromCart(
            auth()->user(),
            $cartItems,
            $request->all()
        );

        // Kosongkan keranjang
        auth()->user()->cartItems()->delete();

        return redirect()->route('orders.success', $order->order_code);
    }
}
