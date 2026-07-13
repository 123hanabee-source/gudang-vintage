<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Only allow users with role 'Admin' through.
     * Anyone else gets redirected to dashboard or login.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check()) {
            return redirect()->route('login');
        }

        if (! auth()->user()->isAdmin()) {
            abort(403, 'Akses ditolak. Hanya Admin yang diizinkan.');
        }

        return $next($request);
    }
}
