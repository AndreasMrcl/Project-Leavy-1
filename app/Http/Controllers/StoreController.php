<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class StoreController extends Controller
{
    public function create()
    {
        return view('addstore');
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'no_telpon' => 'required|string|max:15',
            'ktp' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'atas_nama' => 'required|string|max:255',
            'bank' => 'required|string|max:255',
            'no_rek' => 'required|string|max:50',
            'store' => 'required|string|max:255',
            'location' => 'required|string|max:255',
        ]);

        $data['user_id'] = $user->id;
        $data['status'] = 'Settlement';

        if ($request->hasFile('ktp')) {
            $uploadedKtp = $request->file('ktp');
            $ktpName = time().'_'.$uploadedKtp->getClientOriginalName();
            $ktpPath = $uploadedKtp->storeAs('ktp', $ktpName, 'public');
            $data['ktp'] = $ktpPath;
        }

        $store = Store::create($data);

        $this->logActivity(
            'Create Store',
            "Creating new store: {$store->name}",
            $store->id
        );

        return redirect(route('dashboard'))->with('success', 'Store successfully added!');
    }

    public function update(Request $request, $id)
    {
        $userStore = Auth::user()->store;

        $store = Store::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'no_telpon' => 'nullable|string|max:15',
            'atas_nama' => 'nullable|string|max:255',
            'bank' => 'nullable|string|max:255',
            'no_rek' => 'nullable|string|max:50',
            'ktp' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'location' => 'nullable|string|max:255',
        ]);

        $old = [
            'name' => $store->name,
            'no_telpon' => $store->no_telpon,
            'atas_nama' => $store->atas_nama,
            'bank' => $store->bank,
            'no_rek' => $store->no_rek,
            'location' => $store->location,
        ];

        // Upload KTP jika ada file baru
        if ($request->hasFile('ktp')) {
            $file = $request->file('ktp');
            $filename = time().'_'.$file->getClientOriginalName();
            $path = $file->storeAs('ktp', $filename, 'public');
            $validated['ktp'] = $path;
        } else {
            unset($validated['ktp']);
        }

        $store->update($validated);

        // Detect what changed (kecuali ktp — log terpisah)
        $changes = [];
        foreach ($old as $field => $value) {
            if (($validated[$field] ?? null) != $value) {
                $label = ucfirst(str_replace('_', ' ', $field));
                $changes[] = "{$label} updated from '{$value}' to '{$validated[$field]}'";
            }
        }

        if ($request->hasFile('ktp')) {
            $changes[] = 'KTP re-uploaded';
        }

        if ($changes) {
            $desc = "Update Store '{$store->name}': ".implode(', ', $changes);
            $this->logActivity('Update Store', $desc, $store->id);
        }

        return redirect()->back()->with('success', 'Store information successfully updated!');
    }

    private function logActivity($type, $description, $storeId)
    {
        ActivityLogger::log($type, $description, $storeId);
    }
}