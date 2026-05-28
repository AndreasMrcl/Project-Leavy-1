<?php

namespace App\Http\Controllers;

use App\Models\Showcase;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ShowcaseController extends Controller
{
    public function index()
    {
        $storeId = Auth::user()->store->id;
        $cacheKey = "showcase_{$storeId}";

        $showcases = Cache::remember($cacheKey, 180, fn () => Showcase::all());

        return view('showcase', compact('showcases'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'img' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $uploadedImage = $request->file('img');
        $imageName = $uploadedImage->getClientOriginalName();
        $uploadedImage->storeAs('public/img', $imageName);
        $data['img'] = 'img/' . $imageName;

        $showcase = Showcase::create($data);

        $this->logActivity(
            'Create Showcase',
            "Adding new showcase: {$showcase->name}",
            $showcase->store_id
        );

        $this->clearCache($showcase->store_id);

        return redirect(route('showcase'))->with('success', 'Showcase successfully created!');
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'required',
            'img' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $showcase = Showcase::findOrFail($id);

        if ($request->hasFile('img')) {
            $uploadedImage = $request->file('img');
            $imageName = $uploadedImage->getClientOriginalName();
            $uploadedImage->storeAs('public/img', $imageName);
            $data['img'] = 'img/' . $imageName;
        }

        $oldName = $showcase->name;

        $showcase->update([
            'name' => $data['name'],
            'img' => $data['img'] ?? $showcase->img,
        ]);

        $this->logActivity(
            'Update Showcase',
            "Update Showcase '{$oldName}' menjadi '{$showcase->name}'",
            $showcase->store_id
        );

        $this->clearCache($showcase->store_id);

        return redirect(route('showcase'))->with('success', 'Showcase successfully updated!');
    }

    public function destroy($id)
    {
        $showcase = Showcase::find($id);

        if (! $showcase) {
            return redirect(route('showcase'))->withErrors(['msg' => 'Showcase tidak ditemukan.']);
        }

        $name = $showcase->name;
        $storeId = $showcase->store_id;

        $showcase->delete();

        $this->logActivity('Delete Showcase', "Deleting showcase: {$name}", $storeId);
        $this->clearCache($storeId);

        return redirect(route('showcase'))->with('success', 'Showcase successfully deleted!');
    }

    private function clearCache(int $storeId): void
    {
        Cache::forget("showcase_{$storeId}");
    }

    private function logActivity($type, $description, $storeId)
    {
        ActivityLogger::log($type, $description, $storeId);
    }
}
