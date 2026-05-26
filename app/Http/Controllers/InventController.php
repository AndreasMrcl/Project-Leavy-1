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
        $invents = Invent::all();

        return view('invent', compact('invents'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'unit' => 'required',
            'min_stock' => 'nullable|integer|min:0',
            'initial_stock' => 'nullable|integer|min:0',
        ]);

        $newInvent = DB::transaction(function () use ($data) {
            $invent = Invent::create([
                'name' => $data['name'],
                'unit' => $data['unit'],
                'min_stock' => $data['min_stock'] ?? 0,
                'stock' => 0,
            ]);

            $initial = (int) ($data['initial_stock'] ?? 0);
            if ($initial > 0) {
                $invent->increment('stock', $initial);
                StockMovement::create([
                    'store_id' => $invent->store_id,
                    'invent_id' => $invent->id,
                    'user_id' => Auth::id(),
                    'quantity' => $initial,
                    'stock_before' => 0,
                    'type' => 'receive',
                    'notes' => 'Initial stock',
                ]);
            }

            return $invent;
        });

        $this->logActivity(
            'Create Invent',
            "Adding new ingredient: {$newInvent->name} (Initial stock: ".(int) ($data['initial_stock'] ?? 0)." {$newInvent->unit})",
            $newInvent->store_id
        );

        $this->clearCache($newInvent->store_id);

        return redirect(route('invent'))->with('success', 'Ingredient successfully added!');
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'unit' => 'required',
            'min_stock' => 'nullable|integer|min:0',
        ]);

        $invent = Invent::findOrFail($id);

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

        $changes = [];
        foreach ($new as $field => $value) {
            if ($old[$field] != $value) {
                $label = ucfirst(str_replace('_', ' ', $field));
                $changes[] = "{$label} changed from '{$old[$field]}' to '{$value}'";
            }
        }

        if ($changes) {
            $desc = "Update Invent '{$invent->name}': ".implode(', ', $changes);
            $this->logActivity('Update Invent', $desc, $invent->store_id);
        }

        $this->clearCache($invent->store_id);

        return redirect(route('invent'))->with('success', 'Ingredient successfully updated!');
    }

    public function destroy($id)
    {
        $invent = Invent::find($id);

        if (! $invent) {
            return redirect(route('invent'))->withErrors(['msg' => 'Bahan tidak ditemukan.']);
        }

        $name = $invent->name;
        $storeId = $invent->store_id;

        $invent->delete();

        $this->logActivity('Delete Invent', "Deleting ingredient: {$name}", $storeId);
        $this->clearCache($storeId);

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
