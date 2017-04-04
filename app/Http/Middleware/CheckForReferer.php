<?php

namespace App\Http\Middleware;

use Nova\Support\Facades\Config;
use Nova\Support\Facades\Redirect;
use Nova\Support\Str;

use Closure;


class CheckForReferer
{

    /**
     * Handle an incoming request.
     *
     * @param  \Nova\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $referer = $request->header('referer');

        if (! Str::startsWith($referer, Config::get('app.url'))) {
            return Redirect::back();
        }

        return $next($request);
    }
}
