<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TwoFactorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->two_factor_enabled && session('2fa_required')) {
            // Allow access to 2FA routes
            if ($request->routeIs('two-factor.*') || $request->routeIs('logout')) {
                return $next($request);
            }

            return redirect()->route('two-factor.show');
        }

        return $next($request);
    }
}