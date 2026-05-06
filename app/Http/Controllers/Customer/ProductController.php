<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Discount;
use App\Models\Menu;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{
    public function product()
    {
        $category = Cache::remember('categories_with_menus', now()->addMinutes(60), function () {
            return Category::with(['menus'])->get();
        });

        $chair = auth()->user();
        $cart = Cart::getActiveOrCreateForChair($chair);

        return view('user.product', compact('category', 'cart'));
    }

    public function show($id)
    {
        $menu = Cache::remember("menu_{$id}", now()->addMinutes(60), function () use ($id) {
            return Menu::find($id);
        });
        $discount = Cache::remember('discounts', now()->addMinutes(60), function () {
            return Discount::all();
        });

        return view('user.show', compact('menu', 'discount'));
    }
}
