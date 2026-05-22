<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Discount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class DiscountController extends Controller
{
    public function index()
    {
        $storeId = Auth::user()->store->id;
        
        $cacheKey = "discount_{$storeId}";

        $discounts = Cache::remember($cacheKey, 180, fn () => Discount::all());

        return view('discount', compact('discounts'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'percentage' => 'required',
        ]);

        $discount = Discount::create($data);

        $this->logActivity(
            'Create Discount',
            "Adding new discount: {$discount->name} ({$discount->percentage}%)",
            $discount->store_id
        );

        $this->clearCache($discount->store_id);

        return redirect(route('discount'))->with('success', 'Discount successfully created!');
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'required',
            'percentage' => 'required',
        ]);

        $discount = Discount::findOrFail($id);

        $old = [
            'name' => $discount->name,
            'percentage' => $discount->percentage,
        ];

        $discount->update($data);

        $changes = [];
        foreach ($data as $field => $value) {
            if ($old[$field] != $value) {
                $label = ucfirst(str_replace('_', ' ', $field));
                $changes[] = "{$label} diubah dari '{$old[$field]}' menjadi '{$value}'";
            }
        }

        if ($changes) {
            $desc = "Update Discount '{$discount->name}': ".implode(', ', $changes);
            $this->logActivity('Update Discount', $desc, $discount->store_id);
        }

        $this->clearCache($discount->store_id);

        return redirect(route('discount'))->with('success', 'Discount successfully updated!');
    }

    public function destroy($id)
    {
        $discount = Discount::find($id);

        if (! $discount) {
            return redirect(route('discount'))->withErrors(['msg' => 'Discount tidak ditemukan.']);
        }

        $name = $discount->name;
        $storeId = $discount->store_id;

        $discount->delete();

        $this->logActivity('Delete Discount', "Deleting discount: {$name}", $storeId);
        $this->clearCache($storeId);

        return redirect(route('discount'))->with('success', 'Discount successfully deleted!');
    }

    private function clearCache(int $storeId): void
    {
        Cache::forget("discount_{$storeId}");
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
