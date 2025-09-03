<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class PermissionMiddleware
{
    public function handle($request, Closure $next, $permission)
    {
        if (!Auth::check() || !$request->user()->can($permission)) {
            abort(403, 'Unauthorized (Permission)');
        }

        return $next($request);
    }
}
