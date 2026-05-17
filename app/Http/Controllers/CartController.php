<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
   
    public function index()
    {
        $cartItems = auth()->user()->cartItems()
            ->with(['product.priceTiers', 'product.category'])
            ->get();

        $total          = $cartItems->sum->subtotal;
        $totalFormatted = 'Rp ' . number_format($total, 0, ',', '.');

        return view('public.cart.index', compact('cartItems', 'totalFormatted'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'qty'        => 'required|integer|min:1',
        ]);

        $existing = Cart::where('user_id', auth()->id())
            ->where('product_id', $request->product_id)
            ->first();

        if ($existing) {
            $existing->increment('qty', $request->qty);
        } else {
            Cart::create([
                'user_id'    => auth()->id(),
                'product_id' => $request->product_id,
                'qty'        => $request->qty,
            ]);
        }

        $cartCount = auth()->user()->cartItems()->count();

        return response()->json(['success' => true, 'cart_count' => $cartCount]);
    }

    public function update(Request $request, Cart $cart)
    {
        abort_if($cart->user_id !== auth()->id(), 403);

        $request->validate(['qty' => 'required|integer|min:1']);
        $cart->update(['qty' => $request->qty]);

        return response()->json(['success' => true]);
    }

    public function remove(Cart $cart)
    {
        abort_if($cart->user_id !== auth()->id(), 403);
        $cart->delete();

        return back()->with('success', 'Produk dihapus dari keranjang.');
    }
}
