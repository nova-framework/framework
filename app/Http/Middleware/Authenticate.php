<?php

namespace App\Http\Middleware;

use Nova\Foundation\Application;
use Nova\Support\Facades\Response;
use Nova\Support\Facades\Redirect;

use Closure;


class Authenticate
{
    /**
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $auth;

    /**
     * Create a new middleware instance.
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
     * @param  \Nova\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($this->auth->guest()) {
            if ($request->ajax()|| $request->wantsJson()) {
                return Response::make('Unauthorized.', 401);
            } else {
                return Redirect::guest('login');
            }
        }

        return $next($request);
    }
}
