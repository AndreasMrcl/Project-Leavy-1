<?php

namespace App\Models\Concerns;

use App\Models\Store;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

/**
 * Auto-scope every query to the authenticated user's store and auto-fill
 * store_id on create. Apply to models that have a `store_id` column and
 * are only accessed via the admin (sanctum) guard.
 *
 * Do NOT apply to models reachable via the chair guard
 * (Cart, CartMenu, Order, History) — those need multi-guard handling.
 */
trait BelongsToStore
{
    protected static function bootBelongsToStore(): void
    {
        static::addGlobalScope('store', function (Builder $query) {
            if (Auth::check() && ($store = Auth::user()->store)) {
                $query->where(
                    $query->getModel()->getTable().'.store_id',
                    $store->id
                );
            }
        });

        static::creating(function ($model) {
            if (! $model->store_id && Auth::check() && ($store = Auth::user()->store)) {
                $model->store_id = $store->id;
            }
        });
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * Bypass the store scope (e.g. for super-admin / cross-store reporting).
     */
    public function scopeAllStores(Builder $query): Builder
    {
        return $query->withoutGlobalScope('store');
    }
}
