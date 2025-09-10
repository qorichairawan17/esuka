<?php

namespace App\Http\Middleware\Auth;

use Closure;
use App\Enum\RoleEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class NonAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $userRole = Auth::user()->role;
            if ($userRole == RoleEnum::Superadmin->value || $userRole == RoleEnum::Administrator->value) {
                return redirect()->route('dashboard.admin');
            } else if ($userRole == RoleEnum::User->value) {
                return redirect()->route('dashboard.pengguna');
            } else {
                abort(403, 'Unauthorized action.');
            }
        }
        return $next($request);
    }
}
