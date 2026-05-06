<?php

namespace App\Http\Controllers;

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

        Expense::create([
            'name' => $data['name'],
            'nominal' => $data['nominal'],
            'store_id' => $userStore->id,
        ]);

        $this->clearCache($userStore->id);

        return redirect(route('expense'))->with('success', 'Expense Sukses Dibuat !');
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

        $expense->update([
            'name' => $data['name'],
            'nominal' => $data['nominal'],
        ]);

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
            return redirect(route('expense'))->withErrors(['msg' => 'Expense tidak ditemukan.']);
        }

        $expense->delete();

        $this->clearCache($userStore->id);

        return redirect(route('expense'))->with('success', 'Expense Berhasil Dihapus !');
    }

    private function clearCache(int $storeId): void
    {
        Cache::forget("expense_{$storeId}");
    }
}
