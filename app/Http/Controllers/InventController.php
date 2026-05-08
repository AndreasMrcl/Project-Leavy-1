<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
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

        $newInvent = DB::transaction(function () use ($data, $userStore) {
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

            return $invent;
        });

        $this->logActivity(
            'Create Invent',
            "Adding new ingredient: {$newInvent->name} (Initial stock: ".(int) ($data['initial_stock'] ?? 0)." {$newInvent->unit})",
            $userStore->id
        );

        $this->clearCache($userStore->id);

        return redirect(route('invent'))->with('success', 'Ingredient successfully added!');
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

        $old = [
            'name' => $invent->name,
            'unit' => $invent->unit,
            'min_stock' => $invent->min_stock,
        ];

        $new = [
            'name' => $data['name'],
            'unit' => $data['unit'],
            'min_stock' => $data['min_stock'] ?? 0,
        ];

        $invent->update($new);

        // Detect what changed
        $changes = [];
        foreach ($new as $field => $value) {
            if ($old[$field] != $value) {
                $label = ucfirst(str_replace('_', ' ', $field));
                $changes[] = "{$label} changed from '{$old[$field]}' to '{$value}'";
            }
        }

        if ($changes) {
            $desc = "Update Invent '{$invent->name}': ".implode(', ', $changes);
            $this->logActivity('Update Invent', $desc, $userStore->id);
        }

        $this->clearCache($userStore->id);

        return redirect(route('invent'))->with('success', 'Ingredient successfully updated!');
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

        $name = $invent->name;

        $invent->delete();

        $this->logActivity(
            'Delete Invent',
            "Deleting ingredient: {$name}",
            $userStore->id
        );

        $this->clearCache($userStore->id);

        return redirect(route('invent'))->with('success', 'Ingredient successfully deleted!');
    }

    private function clearCache($storeId)
    {
        Cache::forget("invents_{$storeId}");
        Cache::forget("stock_{$storeId}");
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