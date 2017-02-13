<?php

namespace App\Http\Middleware;

use Nova\Foundation\Application;
use Nova\Support\Facades\Redirect;
use Nova\Support\Str;

use Closure;


class CheckForHttpReferrer
{
    /**
     * The Application instance.
     *
     * @var \Nova\Foundation\Application
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

        // Check if the visitor come to this Route from another site.
        $referrer = $request->header('referer');

        if(! Str::startsWith($referrer, $config->get('app.url'))) {
            // When Referrer is invalid, redirect back.
            return Redirect::back();
        }

        return $next($request);
    }
}
