<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ForbiddenForUserMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        if ($user->role === \App\Enum\RoleEnum::User->value) {
            return redirect()->route('dashboard.pengguna')->with('error', 'Kamu tidak memiliki akses ke halaman ini.');
        }
        return $next($request);
    }
}
