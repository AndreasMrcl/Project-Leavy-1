<?php

namespace App\Http\Controllers;

use App\Models\CartMenu;
use App\Models\Category;
use App\Models\Discount;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{

    public function index()
    {
        $userStore = Auth::user()->store;

        $cacheKey = "menu_{$userStore->id}";

        $category = Category::where('store_id', $userStore->id)->get();

        $menuAll = Cache::remember($cacheKey, 180, function () use ($userStore) {
            return $userStore->menus()->with('category')->get();
        });

        return view('product', compact('category', 'menuAll'));
    }

    public function store(Request $request)
    {
        $userStore = Auth::user()->store;

        $data = $request->validate([
            'name' => 'required',
            'price' => 'required',
            'img' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'description' => 'required',
            'category_id' => 'required|exists:categories,id,store_id,' . $userStore->id,
        ]);

        if ($request->hasFile('img')) {
            $uploadedImage = $request->file('img');
            $imageName = $uploadedImage->getClientOriginalName();
            $imagePath = $uploadedImage->storeAs('img', $imageName, 'public');
            $data['img'] = 'img/' . $imageName;
        }

        $data['store_id'] = $userStore->id;

        Menu::create([
            'name' => $data['name'],
            'price' => $data['price'],
            'img' => $data['img'],
            'description' => $data['description'],
            'category_id' => $data['category_id'],
            'store_id' => $userStore->id,
        ]);

        $this->clearCache($userStore->id);

        return redirect(route('product'))->with('success', 'Product Sukses Dibuat !');
    }

    public function show($id)
    {
        $menu = Cache::remember("menu_{$id}", now()->addMinutes(60), function () use ($id) {
            return Menu::find($id);
        });
        $discount = Cache::remember('discounts', now()->addMinutes(60), function () {
            return Discount::all();
        });

        return view('showproduct', compact('menu', 'discount'));
    }

    public function update(Request $request, $id)
    {
        $userStore = Auth::user()->store;

        $data = $request->validate([
            'name' => 'required',
            'desc' => 'required',
        ]);

        $menu = Menu::where('id', $id)
            ->where('store_id', $userStore->id)
            ->firstOrFail();

        $menu->update([
            'name' => $data['name'],
            'description' => $data['desc'],
        ]);

        $this->clearCache($userStore->id);

        return redirect(route('product'))->with('success', 'Product Sukses Diupdate !');
    }

    public function destroy($id)
    {
        $userStore = Auth::user()->store;

        $menu = Menu::where('id', $id)
            ->where('store_id', $userStore->id)
            ->first();

        if (! $menu) {
            return redirect(route('product'))->withErrors(['msg' => 'Product tidak ditemukan.']);
        }

        // hapus relasi cart_menu
        CartMenu::where('menu_id', $id)->delete();

        // hapus file img dari storage
        if ($menu->img && Storage::disk('public')->exists($menu->img)) {
            Storage::disk('public')->delete($menu->img);
        }

        $menu->delete();

        $this->clearCache($userStore->id);

        return redirect()->route('product')->with('success', 'Product berhasil dihapus!');
    }

    private function clearCache(int $storeId): void
    {
        Cache::forget("menu_{$storeId}");
    }
}