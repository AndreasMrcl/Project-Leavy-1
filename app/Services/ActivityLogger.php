<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ActivityLogger
{
    public static function log(string $type, string $description, ?int $storeId = null): void
    {
        $userId  = null;
        $staffId = null;

        if (Auth::guard('staff')->check()) {
            $staffId = Auth::guard('staff')->id();
            $storeId = $storeId ?? Auth::guard('staff')->user()?->store?->id;
        } elseif (Auth::guard('web')->check()) {
            $userId  = Auth::guard('web')->id();
            $storeId = $storeId ?? Auth::guard('web')->user()?->store?->id;
        }

        if (! $storeId) {
            return;
        }

        ActivityLog::create([
            'user_id'       => $userId,
            'staff_id'      => $staffId,
            'store_id'      => $storeId,
            'activity_type' => $type,
            'description'   => $description,
            'created_at'    => now(),
        ]);

        Cache::forget("activities_{$storeId}");
    }
}
