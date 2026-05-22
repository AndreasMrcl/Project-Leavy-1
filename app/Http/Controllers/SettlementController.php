<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
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
        $storeId = Auth::user()->store->id;
        $cacheKey = "settlement_{$storeId}";

        $settlements = Cache::remember($cacheKey, 180, fn () => Settlement::all());

        return view('settlement', compact('settlements'));
    }

    public function poststart(Request $request)
    {
        $data = $request->validate([
            'start_amount' => 'nullable|numeric',
        ]);

        $user = Auth::user();
        $activeShift = $user->settlements()->active()->first();

        if ($activeShift) {
            return redirect(route('settlement'))->with('error', "The previous shift hasn't closed yet. Please close it before opening a new shift.");
        }

        $data['start_time'] = Carbon::now()->toDateTimeString();
        $data['expected'] = $data['start_amount'] ?? 0;

        $settlement = $user->settlements()->create($data);

        $this->logActivity(
            'Open Shift',
            'Opening shift with initial cash: Rp '.number_format($data['expected'] ?? 0, 0, ',', '.'),
            $settlement->store_id
        );

        $this->clearCache($settlement->store_id);

        return redirect(route('settlement'))->with('success', 'New settlement created successfully!');
    }

    public function posttotal(Request $request)
    {
        $storeId = Auth::user()->store->id;

        $data = $request->validate([
            'total_amount' => 'nullable|numeric',
        ]);

        $user = Auth::user();
        $activeShift = $user->settlements()->active()->first();

        if (! $activeShift) {
            return redirect(route('settlement'))->with('error', 'There is no active shift that can be closed.');
        }

        $openBillCount = Cart::openBills()->where('store_id', $storeId)->count();
        if ($openBillCount > 0) {
            return redirect(route('settlement'))->with('error', "Cannot close shift: there are still {$openBillCount} open bills. Please settle or cancel them first on the Order page.");
        }

        $data['end_time'] = Carbon::now()->toDateTimeString();
        $activeShift->update($data);

        $this->logActivity(
            'Close Shift',
            'Closing shift with total cash: Rp '.number_format($data['total_amount'] ?? 0, 0, ',', '.'),
            $storeId
        );

        $this->clearCache($storeId);

        Cache::forget("settlement_{$activeShift->id}");

        return redirect(route('settlement'))->with('success', 'Shift ended successfully!');
    }

    public function show($id)
    {
        $settlement = Cache::remember(
            "settlement_{$id}",
            now()->addMinutes(60),
            fn () => Settlement::with('histories')->findOrFail($id)
        );

        return view('showsettlement', compact('settlement'));
    }

    public function destroy($id)
    {
        $settlement = Settlement::findOrFail($id);
        $storeId = $settlement->store_id;

        $settlement->delete();

        $this->logActivity('Delete Settlement', "Deleting settlement #{$id}", $storeId);

        $this->clearCache($storeId);
        Cache::forget("settlement_{$id}");

        return redirect(route('settlement'))->with('success', 'Settlement deleted successfully!');
    }

    private function clearCache(int $storeId): void
    {
        Cache::forget("settlement_{$storeId}");
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
