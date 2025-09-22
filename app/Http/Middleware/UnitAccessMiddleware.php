<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UnitAccessMiddleware
{
    /**
     * Handle an incoming request for unit-specific access
     * Admin can access all units, regular users only their own unit
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $user = Auth::user();

        // Admin can access everything
        if ($user->role === 'admin') {
            return $next($request);
        }

        // Regular users: additional checks can be added here if needed
        // For now, just ensure they're authenticated
        
        return $next($request);
    }
}