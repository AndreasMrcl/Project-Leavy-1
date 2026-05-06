<?php

namespace App\Http\Controllers;

use App\Models\Discount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class DiscountController extends Controller
{
    public function index()
    {
        $userStore = Auth::user()->store;

        $cacheKey = "discount_{$userStore->id}";

        $discounts = Cache::remember($cacheKey, 180, function () use ($userStore) {
            return $userStore->discounts()->get();
        });

        return view('discount', compact('discounts'));
    }

    public function store(Request $request)
    {
        $userStore = Auth::user()->store;

        $data = $request->validate([
            'name' => 'required',
            'percentage' => 'required',
        ]);

        $data['store_id'] = $userStore->id;

        Discount::create([
            'name' => $data['name'],
            'percentage' => $data['percentage'],
            'store_id' => $userStore->id,
        ]);

        $this->clearCache($userStore->id);

        return redirect(route('discount'))->with('success', 'Discount successfully created!');
    }

    public function update(Request $request, $id)
    {
        $userStore = Auth::user()->store;

        $data = $request->validate([
            'name' => 'required',
            'percentage' => 'required',
        ]);

        $discount = Discount::where('id', $id)
            ->where('store_id', $userStore->id)
            ->firstOrFail();

        $discount->update([
            'name' => $data['name'],
            'percentage' => $data['percentage'],
        ]);

        $this->clearCache($userStore->id);

        return redirect(route('discount'))->with('success', 'Discount Sukses Diupdate !');
    }

    public function destroy($id)
    {
        $userStore = Auth::user()->store;

        $discount = Discount::where('id', $id)
            ->where('store_id', $userStore->id)
            ->first();

        if (! $discount) {
            return redirect(route('discount'))->withErrors(['msg' => 'Discount tidak ditemukan.']);
        }

        $discount->delete();

        $this->clearCache($userStore->id);

        return redirect(route('discount'))->with('success', 'Discount Berhasil Dihapus !');
    }

    private function clearCache(int $storeId): void
    {
        Cache::forget("discount_{$storeId}");
    }
}
