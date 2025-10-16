<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::user();

                // Redirect sesuai role
                if ($user->hasRole('superadmin')) {
                    return redirect()->route('admin.dashboard');
                } elseif ($user->hasRole('asesor')) {
                    return redirect()->route('asesor.dashboard');
                } elseif ($user->hasRole('lembagaPelatihan')) {
                    return redirect()->route('lembaga-pelatihan.dashboard');
                } elseif ($user->hasRole('admin')) {
                    return redirect()->route('admin.dashboard');
                } else {
                    // Default ke asesi dashboard
                    return redirect()->route('asesi.dashboard');
                }
            }
        }

        return $next($request);
    }
}
