<?php

namespace App\Http\Middleware;

use Nova\Support\Facades\Auth;
use Nova\Support\Facades\Config;
use Nova\Support\Facades\Redirect;

use Closure;


class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Nova\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (is_null($guard)) {
            $guard = Config::get('auth.default', 'web');
        }

        if (Auth::guard($guard)->check()) {
            $uri = Config::get("auth.guards.{$guard}.paths.dashboard", 'admin/dashboard');

            return Redirect::to($uri);
        }

        return $next($request);
    }
}
