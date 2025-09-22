<?php

namespace App\Http\Middleware;

use App\Enum\RoleEnum;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class HomeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        $currentRouteName = $request->route()->getName();

        if ($user->role === RoleEnum::User->value && $currentRouteName === 'dashboard.admin') {
            return redirect()->route('dashboard.pengguna');
        }

        if (
            ($user->role === RoleEnum::Superadmin->value || $user->role === RoleEnum::Administrator->value) &&
            $currentRouteName === 'dashboard.pengguna'
        ) {
            return redirect()->route('dashboard.admin');
        }

        return $next($request);
    }
}
