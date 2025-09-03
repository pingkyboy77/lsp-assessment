<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RoleOrPermissionMiddleware
{
    public function handle($request, Closure $next, $roleOrPermission)
    {
        if (!Auth::check()) {
            abort(403, 'Unauthorized (Role or Permission)');
        }

        $user = $request->user();

        if (!$user->hasRole($roleOrPermission) && !$user->can($roleOrPermission)) {
            abort(403, 'Unauthorized (Role or Permission)');
        }

        return $next($request);
    }
}
