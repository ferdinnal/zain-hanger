<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CatalogController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::active()->get();

        $products = Product::active()
            ->with(['category', 'priceTiers', 'images'])
            ->filter($request->only(['q', 'category', 'jenis', 'kepala', 'type']))
            ->orderBy('sort_order')
            ->paginate(12);

        return view('public.catalog.index', compact('categories', 'products'));
    }

    public function show(string $slug)
    {
        $product = Product::active()
            ->with([
                'category',
                'priceTiers',
                'images' => fn($q) => $q->orderBy('sort_order'),
                'variantOptions.values',
                'variants.priceTiers',
            ])
            ->where('slug', $slug)
            ->firstOrFail();

        // Siapkan data gambar untuk Alpine.js
        $images = $product->images->map(fn($img) => [
            'url'        => $img->image ? Storage::url($img->image) : ($img->image_url ?? ''),
            'is_primary' => $img->is_primary,
        ]);

        // Fallback ke image_url lama jika belum ada images
        if ($images->isEmpty()) {
            $images = collect([[
                'url'        => $product->image_url,
                'is_primary' => true,
            ]]);
        }

        // Siapkan data variants dengan price_tiers untuk Alpine.js
        $variants = $product->variants->map(fn($v) => [
            'id'          => $v->id,
            'combination' => $v->combination,
            'sku'         => $v->sku,
            'price_tiers' => $v->priceTiers->map(fn($t) => [
                'min_qty' => $t->min_qty,
                'max_qty' => $t->max_qty,
                'price'   => $t->price,
            ])->values(),
        ])->values();

        $variantOptions = $product->variantOptions->map(fn($opt) => [
            'id'     => $opt->id,
            'name'   => $opt->name,
            'values' => $opt->values->map(fn($val) => [
                'id'    => $val->id,
                'value' => $val->value,
            ])->values(),
        ])->values();

        $related = Product::active()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->with(['priceTiers', 'images'])
            ->limit(4)
            ->get();

        return view('public.catalog.show', compact(
            'product', 'images', 'variants', 'variantOptions', 'related'
        ));
    }
}
