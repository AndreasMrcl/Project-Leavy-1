<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\Request;

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
            'no_telpon' => 'required|string|max:15', // Adjust the validation if needed
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
            $ktpName = time().'_'.$uploadedKtp->getClientOriginalName(); // Prefix with timestamp for uniqueness
            $ktpPath = $uploadedKtp->storeAs('ktp', $ktpName, 'public');
            $data['ktp'] = $ktpPath; // Path is relative to 'storage/app/public'
        }

        Store::create($data);

        return redirect(route('dashboard'))->with('success', 'Store berhasil ditambahkan!');
    }

    public function update(Request $request)
    {
        $userCompany = auth()->user()->compani;

        $validated = $request->validate([
            'store' => 'required|string|max:255',
            'bank' => 'nullable|string|max:255',
            'no_rek' => 'nullable|string|max:50',
            'location' => 'required|string|max:255',
            'ktp' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Upload KTP if exists
        if ($request->hasFile('ktp')) {
            $file = $request->file('ktp');
            $filename = time().'_'.$file->getClientOriginalName();
            $path = $file->storeAs('ktp', $filename, 'public');
            $validated['ktp'] = $path;
        }

        $userCompany->update($validated);

        return redirect()->back()
            ->with('success', 'Company successfully updated!');
    }
}
