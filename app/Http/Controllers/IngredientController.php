<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Invent;
use App\Models\InventMenu;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;

class IngredientController extends Controller
{
    public function index()
    {
        $menus = Menu::with(['invents'])->get();

        $invents = Invent::orderBy('name')->get();

        return view('ingridient', compact('menus', 'invents'));
    }

    public function upsert(Request $request, $id)
    {
        $storeId = Auth::user()->store->id;

        $request->validate([
            'ingredients' => 'required|array|min:1',
            'ingredients.*' => 'required|array|min:1',
            'ingredients.*.*.invent_id' => [
                'required',
                Rule::exists('invents', 'id')->where('store_id', $storeId),
            ],
            'ingredients.*.*.quantity_used' => 'required|numeric|min:0.01',
        ]);

        $menu = Menu::findOrFail($id);

        $allowedVarieties = $menu->has_variety ? ($menu->varieties ?? ['normal']) : ['normal'];

        $existed = InventMenu::where('menu_id', $menu->id)->exists();

        InventMenu::where('menu_id', $menu->id)->delete();

        foreach ($request->ingredients as $variety => $rows) {
            if (! in_array($variety, $allowedVarieties, true)) {
                continue;
            }
            foreach ($rows as $ingredient) {
                InventMenu::create([
                    'menu_id' => $menu->id,
                    'invent_id' => $ingredient['invent_id'],
                    'variety' => $variety,
                    'quantity_used' => $ingredient['quantity_used'],
                ]);
            }
        }

        $this->logActivity(
            $existed ? 'Update Ingredient' : 'Create Ingredient',
            ($existed ? 'Updating' : 'Adding')." ingredient recipe for product: {$menu->name}",
            $menu->store_id
        );

        $this->clearCache($menu->store_id);

        return redirect(route('ingridient'))->with(
            'success',
            $existed ? 'Ingredients successfully updated!' : 'Ingredients successfully added!'
        );
    }

    public function destroy($id)
    {
        $menu = Menu::findOrFail($id);

        InventMenu::where('menu_id', $menu->id)->delete();

        $this->logActivity(
            'Delete Ingredient',
            "Deleting ingredient recipe for product: {$menu->name}",
            $menu->store_id
        );

        $this->clearCache($menu->store_id);

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
