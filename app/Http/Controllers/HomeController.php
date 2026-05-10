<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::active()->get();

        $featuredProducts = Product::active()
            ->featured()
            ->with(['category', 'priceTiers'])
            ->limit(8)
            ->get();

        return view('public.home.index', compact('categories', 'featuredProducts'));
    }
}
