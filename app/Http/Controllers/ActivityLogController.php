<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ActivityLogController extends Controller
{
    public function index()
    {
        if (! Auth::check()) {
            return redirect('/');
        }

        $userStore = Auth::user()->store;

        if (! $userStore) {
            return redirect()->route('addstore');
        }

        $cacheKey = "activities_{$userStore->id}";

        $logs = Cache::remember($cacheKey, 180, function () use ($userStore) {
            return $userStore->activityLogs()->with('user')->latest()->get();
        });

        return view('activityLog', compact('logs'));
    }
}
