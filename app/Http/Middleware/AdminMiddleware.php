<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();
            
            if ($user->role === 'superadmin') {
                return $next($request);
            }
            
            if ($user->role === 'partner') {
                if ($user->partner && $user->partner->is_approved) {
                    return $next($request);
                }
                
                // Allow them to access the pending page or logout
                if ($request->routeIs('admin.pending') || $request->routeIs('logout')) {
                    return $next($request);
                }

                return redirect()->route('admin.pending');
            }
        }

        return redirect('/');
    }
}
