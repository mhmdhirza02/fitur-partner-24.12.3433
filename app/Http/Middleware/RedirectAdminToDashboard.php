<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectAdminToDashboard
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();

            if (in_array($user->role, ['superadmin', 'partner'])) {
                // Izinkan akses ke rute admin (mulai dengan /admin), proses logout, dan callback
                if ($request->is('admin*') || $request->is('logout') || $request->is('midtrans/callback') || $request->routeIs('admin.*') || $request->routeIs('logout')) {
                    return $next($request);
                }

                // Jika admin atau partner mencoba mengakses rute publik/pembeli (seperti /, /events, /my-tickets, dll.),
                // langsung alihkan ke Dashboard Admin.
                return redirect()->route('admin.dashboard');
            }
        }

        return $next($request);
    }
}
