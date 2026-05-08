<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ExpenseController extends Controller
{
    public function index()
    {
        $userStore = Auth::user()->store;

        $cacheKey = "expense_{$userStore->id}";

        $expenses = Cache::remember($cacheKey, 180, function () use ($userStore) {
            return $userStore->expenses()->get();
        });

        return view('expense', compact('expenses'));
    }

    public function store(Request $request)
    {
        $userStore = Auth::user()->store;

        $data = $request->validate([
            'name' => 'required|string',
            'nominal' => 'required|numeric|min:0',
        ]);

        $expense = Expense::create([
            'name' => $data['name'],
            'nominal' => $data['nominal'],
            'store_id' => $userStore->id,
        ]);

        $this->logActivity(
            'Create Expense',
            "Adding new expense: {$expense->name} (Rp ".number_format($expense->nominal, 0, ',', '.').')',
            $userStore->id
        );

        $this->clearCache($userStore->id);

        return redirect(route('expense'))->with('success', 'Expense successfully created!');
    }

    public function update(Request $request, $id)
    {
        $userStore = Auth::user()->store;

        $data = $request->validate([
            'name' => 'required|string',
            'nominal' => 'required|numeric|min:0',
        ]);

        $expense = Expense::where('id', $id)
            ->where('store_id', $userStore->id)
            ->firstOrFail();

        $old = [
            'name' => $expense->name,
            'nominal' => $expense->nominal,
        ];

        $expense->update([
            'name' => $data['name'],
            'nominal' => $data['nominal'],
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
            $desc = "Update Expense '{$expense->name}': ".implode(', ', $changes);
            $this->logActivity('Update Expense', $desc, $userStore->id);
        }

        $this->clearCache($userStore->id);

        return redirect(route('expense'))->with('success', 'Expense Sukses Diupdate !');
    }

    public function destroy($id)
    {
        $userStore = Auth::user()->store;

        $expense = Expense::where('id', $id)
            ->where('store_id', $userStore->id)
            ->first();

        if (! $expense) {
            return redirect(route('expense'))->withErrors(['msg' => 'Expense not found.']);
        }

        $name = $expense->name;

        $expense->delete();

        $this->logActivity(
            'Delete Expense',
            "Deleting expense: {$name}",
            $userStore->id
        );

        $this->clearCache($userStore->id);

        return redirect(route('expense'))->with('success', 'Expense successfully deleted!');
    }

    private function clearCache(int $storeId): void
    {
        Cache::forget("expense_{$storeId}");
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