<?php

namespace App\Http\Controllers;

use App\Models\Invent;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
    public function index()
    {
        $userStore = Auth::user()->store;

        $cacheKey = "stock_{$userStore->id}";

        $invents = Cache::remember($cacheKey, 180, function () use ($userStore) {
            return $userStore->invents()->orderBy('name')->get();
        });

        return view('stok', compact('invents'));
    }

    public function receive(Request $request)
    {
        $userStore = Auth::user()->store;

        $data = $request->validate([
            'invent_id' => 'required|exists:invents,id',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:255',
        ]);

        $invent = Invent::where('id', $data['invent_id'])
            ->where('store_id', $userStore->id)
            ->firstOrFail();

        DB::transaction(function () use ($invent, $data, $userStore) {
            $invent->increment('stock', $data['quantity']);

            StockMovement::create([
                'store_id' => $userStore->id,
                'invent_id' => $invent->id,
                'user_id' => Auth::id(),
                'quantity' => $data['quantity'],
                'type' => 'receive',
                'notes' => $data['notes'] ?? "Penerimaan {$invent->name}",
            ]);
        });

        $this->clearCache($userStore->id);

        return redirect(route('stock'))->with('success', "Penerimaan {$data['quantity']} {$invent->unit} {$invent->name} berhasil dicatat!");
    }

    public function opname(Request $request)
    {
        $userStore = Auth::user()->store;

        $data = $request->validate([
            'invent_id' => 'required|exists:invents,id',
            'actual_stock' => 'required|integer|min:0',
            'reason' => 'required|string|max:255',
        ]);

        $invent = Invent::where('id', $data['invent_id'])
            ->where('store_id', $userStore->id)
            ->firstOrFail();

        $delta = $data['actual_stock'] - $invent->stock;

        if ($delta === 0) {
            return redirect(route('stock'))->with('info', "Stok {$invent->name} sudah sesuai, tidak ada perubahan.");
        }

        DB::transaction(function () use ($invent, $data, $delta, $userStore) {
            $invent->update(['stock' => $data['actual_stock']]);

            StockMovement::create([
                'store_id' => $userStore->id,
                'invent_id' => $invent->id,
                'user_id' => Auth::id(),
                'quantity' => $delta,
                'type' => 'manual_adjust',
                'notes' => $data['reason'],
            ]);
        });

        $this->clearCache($userStore->id);

        $message = $delta > 0
            ? "Stock opname {$invent->name}: +{$delta} {$invent->unit} (penyesuaian naik)."
            : "Stock opname {$invent->name}: {$delta} {$invent->unit} (penyesuaian turun).";

        return redirect(route('stock'))->with('success', $message);
    }

    private function clearCache($storeId)
    {
        Cache::forget("stock_{$storeId}");
        Cache::forget("invents_{$storeId}");
    }
}
