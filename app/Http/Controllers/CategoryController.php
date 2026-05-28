<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class CategoryController extends Controller
{
    public function index()
    {
        $storeId = Auth::user()->store->id;
        $cacheKey = "category_{$storeId}";

        $category = Cache::remember($cacheKey, 180, fn () => Category::all());

        return view('category', compact('category'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'desc' => 'required|string',
        ]);

        $category = Category::create($data);

        $this->logActivity(
            'Create Category',
            "Adding new categories: {$category->name}",
            $category->store_id
        );

        $this->clearCache($category->store_id);

        return redirect(route('category'))->with('success', 'Category successfully created!');
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'desc' => 'required|string',
        ]);

        $category = Category::findOrFail($id);

        $old = [
            'name' => $category->name,
            'desc' => $category->desc,
        ];

        $category->update($data);

        $changes = [];
        foreach ($data as $field => $value) {
            if ($old[$field] != $value) {
                $label = ucfirst(str_replace('_', ' ', $field));
                $changes[] = "{$label} changed from '{$old[$field]}' to '{$value}'";
            }
        }

        if ($changes) {
            $desc = "Update Category '{$category->name}': ".implode(', ', $changes);
            $this->logActivity('Update Category', $desc, $category->store_id);
        }

        $this->clearCache($category->store_id);

        return redirect(route('category'))->with('success', 'Category successfully updated!');
    }

    public function destroy($id)
    {
        $category = Category::find($id);

        if (! $category) {
            return redirect(route('category'))->withErrors(['msg' => 'Category not found.']);
        }

        $name = $category->name;
        $storeId = $category->store_id;

        $category->delete();

        $this->logActivity('Delete Category', "Deleting category: {$name}", $storeId);
        $this->clearCache($storeId);

        return redirect(route('category'))->with('success', 'Category successfully deleted!');
    }

    private function clearCache(int $storeId): void
    {
        Cache::forget("category_{$storeId}");
    }

    private function logActivity($type, $description, $storeId)
    {
        ActivityLogger::log($type, $description, $storeId);
    }
}
