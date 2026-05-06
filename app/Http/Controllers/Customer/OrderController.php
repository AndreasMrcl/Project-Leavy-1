<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;

class OrderController extends Controller
{
    public function postorder(Request $request)
    {
        $chair = auth()->user();
        $storeId = $chair->store_id;

        $request->validate([
            'no_telpon' => 'required|string|max:15',
            'atas_nama' => 'required|string|max:255',
        ]);

        $cart = $chair->carts()->with('cartMenus.menu')->latest()->first();

        if (! $cart || $cart->cartMenus->isEmpty()) {
            return redirect()->route('user-cart')->with('error', 'Your cart is empty.');
        }

        $order = Order::where('cart_id', $cart->id)->first();

        if (! $order) {
            return redirect()->route('user-home');
        }

        $orderId = 'ORDER-'.strtoupper(substr(Uuid::uuid4()->toString(), 0, 8));

        $order->update([
            'no_order' => $orderId,
            'atas_nama' => $request->atas_nama,
            'no_telpon' => $request->no_telpon,
            'store_id' => $storeId,
            'cabang' => $chair->store->store ?? $chair->store->name,
        ]);

        $snapToken = null;

        if (config('midtrans.server_key')) {
            \Midtrans\Config::$serverKey = config('midtrans.server_key');
            \Midtrans\Config::$isProduction = true;
            \Midtrans\Config::$isSanitized = true;
            \Midtrans\Config::$is3ds = true;

            $params = [
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => $cart->total_amount,
                ],
            ];

            $snapToken = \Midtrans\Snap::getSnapToken($params);
        }

        $chair->carts()->create([
            'store_id' => $storeId,
        ]);

        return view('user.checkout', compact('order', 'snapToken'));
    }

    public function payment(Request $request)
    {
        $chair = auth()->user();

        $cart = $chair->carts()->with('cartMenus.menu')->latest()->first();

        if (! $cart || $cart->cartMenus->isEmpty()) {
            return redirect()->route('user-cart')->with('error', 'Keranjang masih kosong.');
        }

        $order = Order::where('cart_id', $cart->id)->first();

        if (! $order) {
            $order = Order::create([
                'store_id' => $chair->store_id,
                'cart_id' => $cart->id,
            ]);
        }

        return view('user.payment', compact('order', 'cart'));
    }
}
