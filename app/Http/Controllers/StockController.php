<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
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

        $this->logActivity(
            'Stock Receive',
            "Receiving stock {$invent->name}: +{$data['quantity']} {$invent->unit}",
            $userStore->id
        );

        $this->clearCache($userStore->id);

        return redirect(route('stock'))->with('success', "Receiving {$data['quantity']} {$invent->unit} {$invent->name} successful!");
    }

    public function opnameForm()
    {
        $userStore = Auth::user()->store;

        $cacheKey = "stock_{$userStore->id}";

        $invents = Cache::remember($cacheKey, 180, function () use ($userStore) {
            return $userStore->invents()->orderBy('name')->get();
        });

        return view('opname', compact('invents'));
    }

    public function opname(Request $request)
    {
        $userStore = Auth::user()->store;

        $data = $request->validate([
            'reason' => 'required|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.invent_id' => 'required|exists:invents,id',
            'items.*.actual_stock' => 'nullable|integer|min:0',
        ]);

        $invents = Invent::where('store_id', $userStore->id)
            ->whereIn('id', collect($data['items'])->pluck('invent_id'))
            ->get()
            ->keyBy('id');

        $changes = [];
        foreach ($data['items'] as $row) {
            if (! isset($row['actual_stock']) || $row['actual_stock'] === null || $row['actual_stock'] === '') {
                continue;
            }

            $invent = $invents->get($row['invent_id']);
            if (! $invent) {
                continue;
            }

            $delta = (int) $row['actual_stock'] - $invent->stock;
            if ($delta === 0) {
                continue;
            }

            $changes[] = [
                'invent' => $invent,
                'actual_stock' => (int) $row['actual_stock'],
                'delta' => $delta,
            ];
        }

        if (empty($changes)) {
            return redirect(route('stock'))->with('info', 'No stock changes detected, nothing to adjust.');
        }

        DB::transaction(function () use ($changes, $data, $userStore) {
            foreach ($changes as $change) {
                $change['invent']->update(['stock' => $change['actual_stock']]);

                StockMovement::create([
                    'store_id' => $userStore->id,
                    'invent_id' => $change['invent']->id,
                    'user_id' => Auth::id(),
                    'quantity' => $change['delta'],
                    'type' => 'manual_adjust',
                    'notes' => $data['reason'],
                ]);
            }
        });

        $totalUp = collect($changes)->where('delta', '>', 0)->sum('delta');
        $totalDown = collect($changes)->where('delta', '<', 0)->sum('delta');

        $this->logActivity(
            'Stock Opname',
            'Stock opname: '.count($changes)." item(s) adjusted (+{$totalUp} / {$totalDown}). Reason: {$data['reason']}",
            $userStore->id
        );

        $this->clearCache($userStore->id);

        return redirect(route('stock'))->with('success', 'Stock opname successful: '.count($changes).' item(s) adjusted.');
    }

    private function clearCache($storeId)
    {
        Cache::forget("stock_{$storeId}");
        
        Cache::forget("invents_{$storeId}");
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