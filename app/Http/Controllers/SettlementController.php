<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Settlement;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class SettlementController extends Controller
{
    public function index()
    {
        $userStore = Auth::user()->store;

        $cacheKey = "settlement_{$userStore->id}";

        $settlements = Cache::remember($cacheKey, 180, function () use ($userStore) {
            return $userStore->settlements()->get();
        });

        return view('settlement', compact('settlements'));
    }

    public function poststart(Request $request)
    {
        $userStore = Auth::user()->store;

        $data = $request->validate([
            'start_amount' => 'nullable|numeric',
        ]);

        $user = auth()->user();

        $activeShift = $user->settlements()->active()->first();

        if ($activeShift) {
            return redirect(route('settlement'))->with('error', 'Shift sebelumnya belum ditutup. Tutup dulu sebelum membuka shift baru.');
        }

        $data['store_id'] = $userStore->id;
        $data['start_time'] = Carbon::now()->toDateTimeString();
        $data['expected'] = $data['start_amount'] ?? 0;

        $user->settlements()->create($data);

        $this->clearCache($userStore->id);

        return redirect(route('settlement'))->with('success', 'New settlement created successfully!');
    }

    public function posttotal(Request $request)
    {
        $userStore = Auth::user()->store;

        $data = $request->validate([
            'total_amount' => 'nullable|numeric',
        ]);

        $user = auth()->user();
        $activeShift = $user->settlements()->active()->first();

        if (! $activeShift) {
            return redirect(route('settlement'))->with('error', 'Tidak ada shift aktif yang bisa ditutup.');
        }

        $openBillCount = Cart::openBills()->where('store_id', $userStore->id)->count();
        if ($openBillCount > 0) {
            return redirect(route('settlement'))->with('error', "Tidak bisa tutup shift: masih ada {$openBillCount} open bill aktif. Tagih atau batalkan dulu di halaman Order.");
        }

        $data['end_time'] = Carbon::now()->toDateTimeString();
        $activeShift->update($data);

        $this->clearCache($userStore->id);

        Cache::forget("settlement_{$activeShift->id}");

        return redirect(route('settlement'))->with('success', 'Shift ended successfully!');
    }

    public function show($id)
    {
        $settlement = Cache::remember(
            "settlement_{$id}",
            now()->addMinutes(60),
            fn() => Settlement::with('history')->findOrFail($id)
        );

        return view('showsettlement', compact('settlement'));
    }

    public function destroy($id)
    {
        $userStore = Auth::user()->store;

        $settlement = Settlement::findOrFail($id);
        $settlement->delete();

        $this->clearCache($userStore->id);
        Cache::forget("settlement_{$id}");

        return redirect(route('settlement'))->with('success', 'Settlement deleted successfully!');
    }

    private function clearCache(int $storeId): void
    {
        Cache::forget("settlement_{$storeId}");
    }
}
