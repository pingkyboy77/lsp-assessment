<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Page;

class CheckPageAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login');
        }

        $routeName = $request->route()->getName();

        // Skip check untuk routes yang tidak perlu page access control
        $skipRoutes = [
            'login',
            'logout',
            'register',
            'password.*',
            'verification.*',
            'profile.*',
            'admin.dashboard',
            'admin.dashboard.dashboard',
            'asesor.dashboard',
            'lembaga-pelatihan.dashboard',
            'asesi.dashboard',
            'dashboard', // general dashboard
        ];

        foreach ($skipRoutes as $skipRoute) {
            if (str_contains($skipRoute, '*')) {
                $pattern = str_replace('*', '', $skipRoute);
                if (str_starts_with($routeName, $pattern) || str_ends_with($routeName, $pattern)) {
                    return $next($request);
                }
            } elseif ($routeName === $skipRoute) {
                return $next($request);
            }
        }

        // Check page access untuk halaman lainnya
        if (!Page::canAccessRoute($routeName, $user)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}
