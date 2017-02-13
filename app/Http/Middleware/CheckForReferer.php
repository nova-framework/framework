<?php

namespace App\Http\Middleware;

use Nova\Foundation\Application;
use Nova\Support\Facades\Redirect;
use Nova\Support\Str;

use Closure;


class CheckForReferer
{
    /**
     * The Application instance.
     *
     * @var string
     */
    protected $app;

    /**
     * Create a new middleware instance.
     *
     * @param  Guard  $auth
     * @return void
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
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
        $config = $this->app['config'];

        //
        $url = $config->get('app.url');

        if (! Str::startsWith($request->header('referer'), $url)) {
            return Redirect::back();
        }

        return $next($request);
    }
}
