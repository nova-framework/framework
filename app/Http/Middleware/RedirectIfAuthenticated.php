<?php

namespace App\Http\Middleware;

use Nova\Support\Facades\Auth;
use Nova\Support\Facades\Redirect;

use Closure;


class RedirectIfAuthenticated
{
    /**
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $auth;

    /**
     * Create a new filter instance.
     *
     * @param  Guard  $auth
     * @return void
     */
    public function __construct(Application $app)
    {
        $this->auth = $app['auth'];
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->check()) {
            return Redirect::to('admin/dashboard');
        }

        return $next($request);
    }
}
