<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Invent;
use App\Models\InventMenu;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class IngredientController extends Controller
{

    public function index()
    {
        $userStore = Auth::user()->store;

        $cacheKey = "ingridient_{$userStore->id}";

        $menus = Cache::remember($cacheKey, 180, function () use ($userStore) {

            return $userStore->menus()->with(['invents'])->get();
        });

        return view('ingridient', compact('menus'));
    }

    public function create()
    {
        $userStore = Auth::user()->store;

        $menus = Menu::where('store_id', $userStore->id)->get();

        $invents = Invent::where('store_id', $userStore->id)->get();

        return view('addingridient', compact('menus', 'invents'));
    }

    public function store(Request $request)
    {
        $userStore = Auth::user()->store;

        $request->validate([
            'menu_id' => 'required|exists:menus,id',
            'ingredients' => 'required|array|min:1',
            'ingredients.*' => 'required|array|min:1',
            'ingredients.*.*.invent_id' => 'required|exists:invents,id',
            'ingredients.*.*.quantity_used' => 'required|numeric|min:0.01',
        ]);

        $menu = Menu::where('store_id', $userStore->id)->findOrFail($request->menu_id);

        $allowedVarieties = $menu->has_variety ? ($menu->varieties ?? ['normal']) : ['normal'];

        foreach ($request->ingredients as $variety => $rows) {
            if (! in_array($variety, $allowedVarieties, true)) {
                continue;
            }
            foreach ($rows as $ingredient) {
                InventMenu::create([
                    'store_id' => $userStore->id,
                    'menu_id' => $menu->id,
                    'invent_id' => $ingredient['invent_id'],
                    'variety' => $variety,
                    'quantity_used' => $ingredient['quantity_used'],
                ]);
            }
        }

        $this->logActivity(
            'Create Ingredient',
            "Adding ingredient recipe for product: {$menu->name}",
            $userStore->id
        );

        $this->clearCache($userStore->id);

        return redirect(route('ingridient'))->with('success', 'Ingredients successfully added!');
    }

    public function edit($id)
    {
        $userStore = Auth::user()->store;

        $menu = Menu::with('invents')->where('store_id', $userStore->id)->findOrFail($id);

        $invents = Invent::where('store_id', $userStore->id)->get();

        return view('editingridient', compact('menu', 'invents'));
    }

    public function update(Request $request, $id)
    {
        $userStore = Auth::user()->store;

        $request->validate([
            'ingredients' => 'required|array|min:1',
            'ingredients.*' => 'required|array|min:1',
            'ingredients.*.*.invent_id' => 'required|exists:invents,id',
            'ingredients.*.*.quantity_used' => 'required|numeric|min:0.01',
        ]);

        $menu = Menu::where('store_id', $userStore->id)->findOrFail($id);

        $allowedVarieties = $menu->has_variety ? ($menu->varieties ?? ['normal']) : ['normal'];

        InventMenu::where('menu_id', $menu->id)->delete();

        $storeId = $userStore->id;

        foreach ($request->ingredients as $variety => $rows) {
            if (! in_array($variety, $allowedVarieties, true)) {
                continue;
            }
            foreach ($rows as $ingredient) {
                InventMenu::create([
                    'store_id' => $storeId,
                    'menu_id' => $menu->id,
                    'invent_id' => $ingredient['invent_id'],
                    'variety' => $variety,
                    'quantity_used' => $ingredient['quantity_used'],
                ]);
            }
        }

        $this->logActivity(
            'Update Ingredient',
            "Updating ingredient recipe for product: {$menu->name}",
            $storeId
        );

        $this->clearCache($userStore->id);

        return redirect(route('ingridient'))->with('success', 'Ingredients successfully updated!');
    }

    public function destroy($id)
    {
        $userStore = Auth::user()->store;

         $menu = Menu::where('id', $id)
            ->where('store_id', $userStore->id)
            ->firstOrFail();

        // Hapus semua relasi ingredients untuk menu ini
        InventMenu::where('menu_id', $menu->id)->delete();

        $this->clearCache($userStore->id);

        return redirect(route('ingridient'))->with('success', 'Ingredients successfully deleted!');
    }

    private function clearCache(int $storeId): void
    {
        Cache::forget("ingridient_{$storeId}");
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
