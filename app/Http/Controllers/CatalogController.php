<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::active()->get();

        $products = Product::active()
            ->with(['category', 'priceTiers'])
            ->filter($request->only(['q', 'category', 'jenis', 'kepala', 'type']))
            ->orderBy('sort_order')
            ->paginate(12);

        return view('public.catalog.index', compact('categories', 'products'));
    }

    public function show(string $slug)
    {
        $product = Product::active()
            ->with(['category', 'priceTiers'])
            ->where('slug', $slug)
            ->firstOrFail();

        $related = Product::active()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->with('priceTiers')
            ->limit(4)
            ->get();

        return view('public.catalog.show', compact('product', 'related'));
    }
}
