<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Showcase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ShowcaseController extends Controller
{
    public function index()
    {
        $userStore = Auth::user()->store;

        $cacheKey = "showcase_{$userStore->id}";

        $showcases = Cache::remember($cacheKey, 180, function () use ($userStore) {
            return $userStore->showcases()->get();
        });

        return view('showcase', compact('showcases'));
    }

    public function store(Request $request)
    {
        $userStore = Auth::user()->store;

        $data = $request->validate([
            'name' => 'required',
            'img' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('img')) {
            $uploadedImage = $request->file('img');
            $imageName = $uploadedImage->getClientOriginalName();
            $uploadedImage->storeAs('public/img', $imageName);
            $data['img'] = 'img/' . $imageName;
        }

        $data['store_id'] = $userStore->id;

        $showcase = Showcase::create([
            'name' => $data['name'],
            'img' => $data['img'],
            'store_id' => $userStore->id,
        ]);

        $this->logActivity(
            'Create Showcase',
            "Adding new showcase: {$showcase->name}",
            $userStore->id
        );

        $this->clearCache($userStore->id);

        return redirect(route('showcase'))->with('success', 'Showcase successfully created!');
    }

    public function update(Request $request, $id)
    {
        $userStore = Auth::user()->store;

        $data =  $request->validate([
            'name' => 'required',
            'img' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $showcase = Showcase::where('id', $id)
            ->where('store_id', $userStore->id)
            ->firstOrFail();

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
            $userStore->id
        );

        $this->clearCache($userStore->id);

        return redirect(route('showcase'))->with('success', 'Showcase successfully updated!');
    }

    public function destroy($id)
    {
        $userStore = Auth::user()->store;

        $showcase = Showcase::where('id', $id)
            ->where('store_id', $userStore->id)
            ->first();

        if (! $showcase) {
            return redirect(route('showcase'))->withErrors(['msg' => 'Showcase tidak ditemukan.']);
        }

        $name = $showcase->name;

        $showcase->delete();

        $this->logActivity(
            'Delete Showcase',
            "Deleting showcase: {$name}",
            $userStore->id
        );

        $this->clearCache($userStore->id);

        return redirect(route('showcase'))->with('success', 'Showcase successfully deleted!');
    }

    private function clearCache(int $storeId): void
    {
        Cache::forget("showcase_{$storeId}");
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