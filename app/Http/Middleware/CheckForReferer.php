<?php

namespace App\Http\Middleware;

use Nova\Foundation\Application;
use Nova\Support\Facades\Redirect;
use Nova\Support\Str;

use Closure;


class CheckForReferer
{
    /**
     * The Application URL.
     *
     * @var string
     */
    protected $url;

    /**
     * Create a new middleware instance.
     *
     * @param  Guard  $auth
     * @return void
     */
    public function __construct(Application $app)
    {
        $this->url = $app['config']->get('app.url');
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
        // Check if the visitor come to this Route from another site.
        $referrer = $request->header('referer');

        if (! Str::startsWith($referrer, $this->url)) {
            // When Referrer is invalid, redirect back.
            return Redirect::back();
        }

        return $next($request);
    }
}
