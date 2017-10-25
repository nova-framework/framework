<?php

namespace App\Middleware;

use Nova\Http\Request;
use Nova\Support\Facades\Auth;
use Nova\Support\Facades\Config;
use Nova\Support\Facades\Redirect;
use Nova\Support\Facades\Response;

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
    public function handle(Request $request, Closure $next, $guard = null)
    {
        if (is_null($guard)) {
            $guard = Config::get('auth.default', 'web');
        }

        if (Auth::guard($guard)->guest()) {
            return $next($request);
        }

        // The User is authenticated.
        else if ($request->ajax() || $request->wantsJson() || $request->is('api/*')) {
            return Response::make('Unauthorized Access', 401);
        }

        $uri = Config::get("auth.guards.{$guard}.paths.dashboard", 'admin/dashboard');

        return Redirect::to($uri);
    }
}
