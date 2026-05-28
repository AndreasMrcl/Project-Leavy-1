<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ActivityLogController extends Controller
{
    public function index()
    {
        $storeId = Auth::user()->store->id;
        $cacheKey = "activities_{$storeId}";

        $logs = Cache::remember(
            $cacheKey,
            180,
            fn () => ActivityLog::with(['user', 'staff'])->latest()->get()
        );

        return view('activityLog', compact('logs'));
    }
}
