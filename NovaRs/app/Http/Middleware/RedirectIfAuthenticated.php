<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, ?string ...$guards)
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                /** @var \App\Models\User $user */
                $user = Auth::guard($guard)->user();

                return redirect()->to($this->dashboardUrlFor($user));
            }
        }

        return $next($request);
    }

    protected function dashboardUrlFor(User $user): string
    {
        return route($user->role->dashboardRoute(), [], false);
    }
}
