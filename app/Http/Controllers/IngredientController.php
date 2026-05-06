<?php

namespace App\Http\Controllers;

use App\Models\Invent;
use App\Models\InventMenu;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class IngredientController extends Controller
{
    private function clearCache()
    {
        Cache::forget('ingridients');
    }

    public function index()
    {
        $menus = Cache::remember(
            'ingridients',
            now()->addMinutes(60),
            fn () => Menu::with(['invents'])->get()
        );

        return view('ingridient', compact('menus'));
    }

    public function create()
    {
        $menus = Menu::all();
        $invents = Invent::all();

        return view('addingridient', compact('menus', 'invents'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'menu_id' => 'required|exists:menus,id',
            'ingredients' => 'required|array|min:1',
            'ingredients.*' => 'required|array|min:1',
            'ingredients.*.*.invent_id' => 'required|exists:invents,id',
            'ingredients.*.*.quantity_used' => 'required|numeric|min:0.01',
        ]);

        $menu = Menu::findOrFail($request->menu_id);
        $allowedVarieties = $menu->has_variety ? ($menu->varieties ?? ['normal']) : ['normal'];

        $storeId = auth()->user()->store->id;

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

        $this->clearCache();

        return redirect(route('ingridient'))->with('success', 'Ingredients successfully added!');
    }

    public function edit($id)
    {
        $menu = Menu::with('invents')->findOrFail($id);
        $invents = Invent::all();

        return view('editingridient', compact('menu', 'invents'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'ingredients' => 'required|array|min:1',
            'ingredients.*' => 'required|array|min:1',
            'ingredients.*.*.invent_id' => 'required|exists:invents,id',
            'ingredients.*.*.quantity_used' => 'required|numeric|min:0.01',
        ]);

        $menu = Menu::findOrFail($id);
        $allowedVarieties = $menu->has_variety ? ($menu->varieties ?? ['normal']) : ['normal'];

        InventMenu::where('menu_id', $menu->id)->delete();

        $storeId = auth()->user()->store->id;

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

        $this->clearCache();

        return redirect(route('ingridient'))->with('success', 'Ingredients successfully updated!');
    }

    public function destroy($id)
    {
        $menu = Menu::findOrFail($id);

        // Hapus semua relasi ingredients untuk menu ini
        InventMenu::where('menu_id', $menu->id)->delete();

        $this->clearCache();

        return redirect(route('ingridient'))->with('success', 'Ingredients successfully deleted!');
    }
}
