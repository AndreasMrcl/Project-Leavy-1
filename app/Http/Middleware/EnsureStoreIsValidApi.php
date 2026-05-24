<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureStoreIsValidApi
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (! $user) {
            return response()->json([
                'errors' => ['auth' => ['Unauthenticated.']],
            ], 401);
        }

        $store = $user->store;

        if (! $store) {
            return response()->json([
                'errors' => ['store' => ['User belum memiliki store.']],
            ], 403);
        }

        if ($store->status !== 'Settlement') {
            return response()->json([
                'errors' => ['store' => ['Store belum aktif.']],
            ], 403);
        }

        return $next($request);
    }
}
