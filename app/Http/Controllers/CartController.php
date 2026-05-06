<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartMenu;
use App\Models\Discount;
use App\Models\Menu;
use App\Services\InventoryService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'menu_id' => 'required|exists:menus,id',
            'quantity' => 'required|integer|min:1',
            'variety' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:255',
            'discount_id' => 'nullable|exists:discounts,id',
            'cart_id' => 'nullable|integer',
        ]);

        $user = auth()->user();
        $storeId = $user->store->id;

        $cart = null;
        if ($request->filled('cart_id')) {
            $cart = Cart::where('id', $request->cart_id)
                ->where('store_id', $storeId)
                ->where('is_open_bill', true)
                ->first();
        }

        if (! $cart) {
            $cart = $user->carts()->where('is_open_bill', false)->latest()->first()
                ?? $user->carts()->create([
                    'store_id' => $storeId,
                    'total_amount' => 0,
                ]);
        }

        $menu = Menu::findOrFail($request->menu_id);
        $quantity = (int) $request->quantity;

        $variety = $menu->has_variety
            ? ($request->variety && in_array($request->variety, $menu->varieties ?? [], true)
                ? $request->variety
                : ($menu->varieties[0] ?? 'normal'))
            : 'normal';

        $insufficient = app(InventoryService::class)->canFulfillCart($cart, $menu, $quantity, $variety);

        if (! empty($insufficient)) {
            return redirect()->back()->with('error', 'Stok bahan tidak cukup: '.implode(', ', $insufficient));
        }

        $subtotal = $menu->price * $quantity;

        $discount = $request->discount_id
            ? Discount::find($request->discount_id)
            : null;

        if ($discount) {
            $discountAmount = $subtotal * ($discount->percentage / 100);
            $subtotal -= $discountAmount;
        }

        // Merge same key (cart + menu + variety + notes + discount)
        $existingCartMenu = CartMenu::where([
            'cart_id' => $cart->id,
            'menu_id' => $menu->id,
            'variety' => $variety,
            'notes' => $request->notes,
            'discount_id' => $discount?->id,
        ])->first();

        if ($existingCartMenu) {
            $existingCartMenu->increment('quantity', $quantity);
            $existingCartMenu->increment('subtotal', $subtotal);
        } else {
            CartMenu::create([
                'store_id' => $storeId,
                'cart_id' => $cart->id,
                'menu_id' => $menu->id,
                'quantity' => $quantity,
                'variety' => $variety,
                'notes' => $request->notes,
                'subtotal' => $subtotal,
                'discount_id' => $discount?->id,
            ]);
        }

        $cart->increment('total_amount', $subtotal);

        if ($cart->is_open_bill) {
            return redirect()->route('addorder', ['cart_id' => $cart->id]);
        }

        return redirect()->route('addorder');
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $cart = $user->carts()->latest()->first();

        $cartMenu = CartMenu::where('id', $id)
            ->where('cart_id', $cart->id)
            ->firstOrFail();

        $subtotal = $cartMenu->subtotal;
        $cartMenu->delete();

        $cart->decrement('total_amount', $subtotal);

        return redirect()->route('addorder');
    }
}
