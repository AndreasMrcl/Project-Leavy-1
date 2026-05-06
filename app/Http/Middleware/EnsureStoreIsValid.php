<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureStoreIsValid
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (! $user) {
            return redirect('/');
        }

        $store = $user->store;

        if (! $store) {
            if ($request->routeIs('addstore') || $request->routeIs('poststore')) {
                return $next($request);
            }
            return redirect()->route('addstore');
        }

        if ($store->status !== 'Settlement') {
            if ($request->routeIs('addstore') || $request->routeIs('poststore')) {
                return $next($request);
            }
            return redirect()->route('login');
        }

        return $next($request);
    }
}
