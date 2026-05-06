<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Chair;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ChairController extends Controller
{

    public function index()
    {
        $userStore = Auth::user()->store;

        $cacheKey = "chair_{$userStore->id}";

        $chairs = Cache::remember($cacheKey, 180, function () use ($userStore) {
            return $userStore->chairs()->get();
        });

        return view('chair', compact('chairs'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $userStore = Auth::user()->store;

        $emailSlug = Str::slug($validatedData['name']);
        $uniqueEmail = $emailSlug . '-' . Str::random(6) . '@chair.local';

        $chair = Chair::create([
            'store_id' => $userStore->id,
            'name' => $validatedData['name'],
            'email' => $uniqueEmail,
            'password' => bcrypt('123456'),
            'qr_token' => Str::random(32),
        ]);

        $token = $chair->createToken('auth_token')->plainTextToken;

        $this->clearCache($userStore->id);

        return redirect()
            ->route('chair')
            ->with('success', 'Chair successfully created!')
            ->with('access_token', $token);
    }

    public function qr(int $id)
    {
        $userStore = Auth::user()->store;

        $chair = Chair::where('id', $id)
            ->where('store_id', $userStore->id)
            ->firstOrFail();

        $signinUrl = route('signin', ['qrToken' => $chair->qr_token]);

        return view('chairqr', compact('chair', 'signinUrl'));
    }

    public function destroy(int $id)
    {
        $userStore = Auth::user()->store;

        $chair = Chair::where('id', $id)
            ->firstOrFail();

        Order::whereHas(
            'cart',
            fn($query) => $query->where('chair_id', $chair->id)
        )->delete();

        Cart::where('chair_id', $chair->id)->delete();

        $chair->delete();

        $this->clearCache($userStore->id);

        return redirect()
            ->route('chair')
            ->with('toast_success', 'Chair successfully deleted!');
    }

    private function clearCache(int $storeId): void
    {
        Cache::forget("chair_{$storeId}");
    }
}
