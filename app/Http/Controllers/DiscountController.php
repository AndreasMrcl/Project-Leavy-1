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
        $userStore = Auth::user()->store;

        $cacheKey = "discount_{$userStore->id}";

        $discounts = Cache::remember($cacheKey, 180, function () use ($userStore) {
            return $userStore->discounts()->get();
        });

        return view('discount', compact('discounts'));
    }

    public function store(Request $request)
    {
        $userStore = Auth::user()->store;

        $data = $request->validate([
            'name' => 'required',
            'percentage' => 'required',
        ]);

        $data['store_id'] = $userStore->id;

        $discount = Discount::create([
            'name' => $data['name'],
            'percentage' => $data['percentage'],
            'store_id' => $userStore->id,
        ]);

        $this->logActivity(
            'Create Discount',
            "Adding new discount: {$discount->name} ({$discount->percentage}%)",
            $userStore->id
        );

        $this->clearCache($userStore->id);

        return redirect(route('discount'))->with('success', 'Discount successfully created!');
    }

    public function update(Request $request, $id)
    {
        $userStore = Auth::user()->store;

        $data = $request->validate([
            'name' => 'required',
            'percentage' => 'required',
        ]);

        $discount = Discount::where('id', $id)
            ->where('store_id', $userStore->id)
            ->firstOrFail();

        $old = [
            'name' => $discount->name,
            'percentage' => $discount->percentage,
        ];

        $discount->update([
            'name' => $data['name'],
            'percentage' => $data['percentage'],
        ]);

        // Detect what changed
        $changes = [];
        foreach ($data as $field => $value) {
            if ($old[$field] != $value) {
                $label = ucfirst(str_replace('_', ' ', $field));
                $changes[] = "{$label} diubah dari '{$old[$field]}' menjadi '{$value}'";
            }
        }

        if ($changes) {
            $desc = "Update Discount '{$discount->name}': ".implode(', ', $changes);
            $this->logActivity('Update Discount', $desc, $userStore->id);
        }

        $this->clearCache($userStore->id);

        return redirect(route('discount'))->with('success', 'Discount Sukses Diupdate !');
    }

    public function destroy($id)
    {
        $userStore = Auth::user()->store;

        $discount = Discount::where('id', $id)
            ->where('store_id', $userStore->id)
            ->first();

        if (! $discount) {
            return redirect(route('discount'))->withErrors(['msg' => 'Discount tidak ditemukan.']);
        }

        $name = $discount->name;

        $discount->delete();

        $this->logActivity(
            'Delete Discount',
            "Deleting discount: {$name}",
            $userStore->id
        );

        $this->clearCache($userStore->id);

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