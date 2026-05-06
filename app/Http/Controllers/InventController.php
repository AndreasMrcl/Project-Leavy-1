<?php

namespace App\Http\Controllers;

use App\Models\Invent;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class InventController extends Controller
{
    public function index()
    {
        $userStore = Auth::user()->store;

        $cacheKey = "invents_{$userStore->id}";

        $invents = Cache::remember($cacheKey, 180, function () use ($userStore) {
            return $userStore->invents()->get();
        });

        return view('invent', compact('invents'));
    }

    public function store(Request $request)
    {
        $userStore = Auth::user()->store;

        $data = $request->validate([
            'name' => 'required|string',
            'unit' => 'required',
            'min_stock' => 'nullable|integer|min:0',
            'initial_stock' => 'nullable|integer|min:0',
        ]);

        DB::transaction(function () use ($data, $userStore) {
            $invent = Invent::create([
                'store_id' => $userStore->id,
                'name' => $data['name'],
                'unit' => $data['unit'],
                'min_stock' => $data['min_stock'] ?? 0,
                'stock' => 0,
            ]);

            $initial = (int) ($data['initial_stock'] ?? 0);
            if ($initial > 0) {
                $invent->increment('stock', $initial);
                StockMovement::create([
                    'store_id' => $userStore->id,
                    'invent_id' => $invent->id,
                    'user_id' => Auth::id(),
                    'quantity' => $initial,
                    'type' => 'receive',
                    'notes' => 'Initial stock',
                ]);
            }
        });

        $this->clearCache($userStore->id);

        return redirect(route('invent'))->with('success', 'Bahan berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $userStore = Auth::user()->store;

        $data = $request->validate([
            'name' => 'required|string',
            'unit' => 'required',
            'min_stock' => 'nullable|integer|min:0',
        ]);

        $invent = Invent::where('id', $id)
            ->where('store_id', $userStore->id)
            ->firstOrFail();

        $invent->update([
            'name' => $data['name'],
            'unit' => $data['unit'],
            'min_stock' => $data['min_stock'] ?? 0,
        ]);

        $this->clearCache($userStore->id);

        return redirect(route('invent'))->with('success', 'Bahan berhasil diupdate!');
    }

    public function destroy($id)
    {
        $userStore = Auth::user()->store;

        $invent = Invent::where('id', $id)
            ->where('store_id', $userStore->id)
            ->first();

        if (! $invent) {
            return redirect(route('invent'))->withErrors(['msg' => 'Bahan tidak ditemukan.']);
        }

        $invent->delete();

        $this->clearCache($userStore->id);

        return redirect(route('invent'))->with('success', 'Bahan berhasil dihapus!');
    }

    private function clearCache($storeId)
    {
        Cache::forget("invents_{$storeId}");
        Cache::forget("stock_{$storeId}");
    }
}
