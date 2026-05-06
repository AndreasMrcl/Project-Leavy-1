<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class CategoryController extends Controller
{
    public function index()
    {
        $userStore = Auth::user()->store;

        $cacheKey = "category_{$userStore->id}";

        $category = Cache::remember($cacheKey, 180, function () use ($userStore) {
            return $userStore->categories()->get();
        });

        return view('category', compact('category'));
    }

    public function store(Request $request)
    {
        $userStore = Auth::user()->store;

        $data = $request->validate([
            'name' => 'required|string',
            'desc' => 'required|string',
        ]);

        $data['store_id'] = $userStore->id;

        Category::create([
            'name' => $data['name'],
            'desc' => $data['desc'],
            'store_id' => $userStore->id,
        ]);

        $this->clearCache($userStore->id);

        return redirect(route('category'))->with('success', 'Category successfully created!');
    }

    public function update(Request $request, $id)
    {
        $userStore = Auth::user()->store;

        $data = $request->validate([
            'name' => 'required|string',
            'desc' => 'required|string',
        ]);

        $category = Category::where('id', $id)
            ->where('store_id', $userStore->id)
            ->firstOrFail();

        $category->update([
            'name' => $data['name'],
            'desc' => $data['desc'],
        ]);

        $this->clearCache($userStore->id);

        return redirect(route('category'))->with('success', 'Category successfully updated!');
    }

    public function destroy($id)
    {
        $userStore = Auth::user()->store;

        $category = Category::where('id', $id)
            ->where('store_id', $userStore->id)
            ->first();

        if (! $category) {
            return redirect(route('category'))->withErrors(['msg' => 'Kategori tidak ditemukan.']);
        }

        $category->delete();

        $this->clearCache($userStore->id);

        return redirect(route('category'))->with('success', 'Kategori berhasil dihapus!');
    }

    private function clearCache(int $storeId): void
    {
        Cache::forget("category_{$storeId}");
    }
}

