<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ActivityLogger
{
    public static function log(string $type, string $description, ?int $storeId = null): void
    {
        $storeId = $storeId ?? Auth::user()?->store?->id;

        if (! $storeId) {
            return;
        }

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
