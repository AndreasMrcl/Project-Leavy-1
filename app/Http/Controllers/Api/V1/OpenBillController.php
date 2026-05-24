<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\CartResource;
use App\Models\Cart;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OpenBillController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $storeId = $request->user()->store->id;

        $openBills = Cart::openBills()
            ->where('store_id', $storeId)
            ->with(['chair', 'cartMenus.menu', 'cartMenus.discount'])
            ->latest('opened_at')
            ->get();

        return $this->ok(['open_bills' => CartResource::collection($openBills)]);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $userStore = $user->store;

        $data = $request->validate([
            'chair_id' => 'required|exists:chairs,id',
            'cart_id'  => 'nullable|integer',
        ]);

        $chair = $userStore->chairs()->where('id', $data['chair_id'])->first();
        if (! $chair) {
            return $this->error('chair', 'Chair tidak valid.', 422);
        }

        $existing = Cart::openBills()->where('chair_id', $chair->id)->exists();
        if ($existing) {
            return $this->error('chair', 'Chair '.$chair->name.' sudah memiliki open bill.', 409);
        }

        $cart = ! empty($data['cart_id'])
            ? Cart::where('id', $data['cart_id'])->where('store_id', $userStore->id)->first()
            : $user->carts()->where('is_open_bill', false)->latest()->first();

        if (! $cart || $cart->cartMenus()->count() === 0) {
            return $this->error('cart', 'Cart kosong, tidak bisa buka bill.', 422);
        }

        if ($cart->orders()->exists()) {
            return $this->error('cart', 'Cart ini sudah punya order, tidak bisa dibuka sebagai bill.', 409);
        }

        DB::transaction(function () use ($cart, $chair, $user, $userStore) {
            $cart->update([
                'is_open_bill' => true,
                'opened_at'    => now(),
                'chair_id'     => $chair->id,
                'expires_at'   => null,
            ]);

            $user->carts()->create([
                'store_id'     => $userStore->id,
                'total_amount' => 0,
            ]);
        });

        ActivityLogger::log('Open Bill', "Opening bill for chair: {$chair->name}", $userStore->id);

        $cart->load('cartMenus.menu', 'cartMenus.discount', 'chair');

        return $this->ok(['cart' => new CartResource($cart)]);
    }

    public function destroy(Request $request, int $cartId)
    {
        $userStore = $request->user()->store;

        $cart = Cart::where('id', $cartId)
            ->where('store_id', $userStore->id)
            ->where('is_open_bill', true)
            ->first();

        if (! $cart) {
            return $this->error('open_bill', 'Open bill tidak ditemukan.', 404);
        }

        $chairName = $cart->chair?->name ?? 'unknown';

        DB::transaction(function () use ($cart) {
            $cart->cartMenus()->delete();
            $cart->orders()->whereNull('status')->delete();
            $cart->delete();
        });

        ActivityLogger::log('Cancel Open Bill', "Canceling open bill for chair: {$chairName}", $userStore->id);

        return $this->ok(['message' => 'Open bill canceled.']);
    }
}
