<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
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
        $storeId = Auth::user()->store->id;
        $cacheKey = "chair_{$storeId}";

        $chairs = Cache::remember($cacheKey, 180, fn () => Chair::all());

        return view('chair', compact('chairs'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $emailSlug = Str::slug($data['name']);
        $uniqueEmail = $emailSlug . '-' . Str::random(6) . '@chair.local';

        $chair = Chair::create([
            'name' => $data['name'],
            'email' => $uniqueEmail,
            'password' => bcrypt('123456'),
            'qr_token' => Str::random(32),
        ]);

        $token = $chair->createToken('auth_token')->plainTextToken;

        $this->logActivity('Create Chair', "Adding new chair: {$chair->name}", $chair->store_id);
        $this->clearCache($chair->store_id);

        return redirect()
            ->route('chair')
            ->with('success', 'Chair successfully created!')
            ->with('access_token', $token);
    }

    public function qr(int $id)
    {
        $chair = Chair::findOrFail($id);

        $signinUrl = route('signin', ['qrToken' => $chair->qr_token]);

        return view('chairqr', compact('chair', 'signinUrl'));
    }

    public function destroy(int $id)
    {
        $chair = Chair::findOrFail($id);

        Order::whereHas(
            'cart',
            fn ($query) => $query->where('chair_id', $chair->id)
        )->delete();

        Cart::where('chair_id', $chair->id)->delete();

        $name = $chair->name;
        $storeId = $chair->store_id;

        $chair->delete();

        $this->logActivity('Delete Chair', "Deleting chair: {$name}", $storeId);
        $this->clearCache($storeId);

        return redirect()
            ->route('chair')
            ->with('toast_success', 'Chair successfully deleted!');
    }

    private function clearCache(int $storeId): void
    {
        Cache::forget("chair_{$storeId}");
    }

    private function logActivity($type, $description, $storeId)
    {
        ActivityLog::create([
            'user_id'       => Auth::id(),
            'store_id'      => $storeId,
            'activity_type' => $type,
            'description'   => $description,
            'created_at'    => now(),
        ]);

        Cache::forget("activities_{$storeId}");
    }
}
