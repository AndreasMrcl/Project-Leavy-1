<?php

namespace App\Http\Controllers;

use App\Models\CartMenu;
use App\Models\Category;
use App\Models\Discount;
use App\Models\InventMenu;
use App\Models\Menu;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index()
    {
        $storeId = Auth::user()->store->id;
        $cacheKey = "menu_{$storeId}";

        $category = Category::all();
        $menuAll = Cache::remember($cacheKey, 180, fn () => Menu::with('category')->get());

        return view('product', compact('category', 'menuAll'));
    }

    public function store(Request $request)
    {
        $storeId = Auth::user()->store->id;

        $data = $request->validate([
            'name' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'img' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'description' => 'required|string|max:500',
            'category_id' => 'required|exists:categories,id,store_id,'.$storeId,
        ] + $this->varietyRules());

        $hasVariety = $request->boolean('has_variety');
        $varieties = $this->resolveVarieties($request);

        $uploadedImage = $request->file('img');
        $imageName = $uploadedImage->getClientOriginalName();
        $uploadedImage->storeAs('img', $imageName, 'public');

        $menu = Menu::create([
            'name' => $data['name'],
            'price' => $data['price'],
            'img' => 'img/'.$imageName,
            'description' => $data['description'],
            'category_id' => $data['category_id'],
            'has_variety' => $hasVariety,
            'varieties' => $varieties,
        ]);

        $this->logActivity(
            'Create Product',
            "Adding new product: {$menu->name} (Rp ".number_format($menu->price, 0, ',', '.').')'
                .($hasVariety ? ' with varieties: '.implode(', ', $varieties) : ''),
            $menu->store_id
        );

        $this->clearCache($menu->store_id);

        return redirect(route('product'))->with('success', 'Product successfully created!');
    }

    public function show($id)
    {
        $menu = Cache::remember("menu_{$id}", now()->addMinutes(60), fn () => Menu::findOrFail($id));
        $discount = Cache::remember('discounts', now()->addMinutes(60), fn () => Discount::all());

        return view('showproduct', compact('menu', 'discount'));
    }

    public function update(Request $request, $id)
    {
        $storeId = Auth::user()->store->id;

        $data = $request->validate([
            'name' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'description' => 'required|string|max:500',
            'category_id' => 'required|exists:categories,id,store_id,'.$storeId,
            'img' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ] + $this->varietyRules());

        $menu = Menu::findOrFail($id);

        $hasVariety = $request->boolean('has_variety');
        $newVarieties = $this->resolveVarieties($request);

        $oldVarieties = $menu->varieties ?? [];
        $oldHasVariety = (bool) $menu->has_variety;

        $payload = [
            'name' => $data['name'],
            'price' => $data['price'],
            'description' => $data['description'],
            'category_id' => $data['category_id'],
            'has_variety' => $hasVariety,
            'varieties' => $newVarieties,
        ];

        if ($request->hasFile('img')) {
            if ($menu->img && Storage::disk('public')->exists($menu->img)) {
                Storage::disk('public')->delete($menu->img);
            }
            $uploadedImage = $request->file('img');
            $imageName = $uploadedImage->getClientOriginalName();
            $uploadedImage->storeAs('img', $imageName, 'public');
            $payload['img'] = 'img/'.$imageName;
        }

        $removedVarieties = [];
        if ($oldHasVariety && ! $hasVariety) {
            $removedVarieties = $oldVarieties;
        } elseif ($oldHasVariety && $hasVariety) {
            $removedVarieties = array_values(array_diff($oldVarieties, $newVarieties));
        }

        $deletedRecipeRows = 0;
        if (! empty($removedVarieties)) {
            $deletedRecipeRows = InventMenu::where('menu_id', $menu->id)
                ->whereIn('variety', $removedVarieties)
                ->delete();
        }

        $diff = $this->diffPayload($menu, $payload);
        $menu->update($payload);

        if ($diff) {
            $this->logActivity('Update Product', "Update Product '{$menu->name}': ".implode(', ', $diff), $menu->store_id);
        }

        $this->clearCache($menu->store_id);

        $message = 'Product successfully updated!';
        if ($deletedRecipeRows > 0) {
            $list = implode(', ', array_map(fn ($v) => Str::title(str_replace('_', ' ', $v)), $removedVarieties));
            $message .= " Resep untuk variety yang dihapus ({$list}) ikut terhapus.";
        }

        return redirect(route('product'))->with('success', $message);
    }

    public function destroy($id)
    {
        $menu = Menu::find($id);

        if (! $menu) {
            return redirect(route('product'))->withErrors(['msg' => 'Product not found.']);
        }

        CartMenu::where('menu_id', $menu->id)->delete();

        if ($menu->img && Storage::disk('public')->exists($menu->img)) {
            Storage::disk('public')->delete($menu->img);
        }

        $name = $menu->name;
        $storeId = $menu->store_id;

        $menu->delete();

        $this->logActivity('Delete Product', "Deleting product: {$name}", $storeId);
        $this->clearCache($storeId);

        return redirect()->route('product')->with('success', 'Product successfully deleted!');
    }

    private function varietyRules(): array
    {
        return [
            'has_variety' => 'sometimes|boolean',
            'varieties' => 'nullable|array|min:2',
            'varieties.*' => 'required|string|max:50|distinct',
        ];
    }

    private function resolveVarieties(Request $request): ?array
    {
        if (! $request->boolean('has_variety')) {
            return null;
        }

        $varieties = array_values(array_unique(array_map(
            fn ($v) => Str::snake(trim($v)),
            $request->input('varieties', [])
        )));

        if (count($varieties) < 2) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'varieties' => 'Minimal 2 variety jika opsi variety diaktifkan.',
            ]);
        }

        return $varieties;
    }

    private function diffPayload(Menu $menu, array $payload): array
    {
        $diff = [];
        foreach ($payload as $field => $value) {
            $old = $menu->getAttribute($field);
            $oldNorm = is_array($old) ? json_encode($old) : (string) $old;
            $newNorm = is_array($value) ? json_encode($value) : (string) $value;
            if ($oldNorm !== $newNorm) {
                $label = Str::headline($field);
                $diff[] = "{$label}: '{$oldNorm}' → '{$newNorm}'";
            }
        }
        return $diff;
    }

    private function clearCache(int $storeId): void
    {
        Cache::forget("menu_{$storeId}");
        Cache::forget("ingridient_{$storeId}");
    }

    private function logActivity($type, $description, $storeId)
    {
        ActivityLogger::log($type, $description, $storeId);
    }
}
