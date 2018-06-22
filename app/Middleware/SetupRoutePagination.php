<?php

namespace App\Middleware;

use Nova\Foundation\Application;
use Nova\Http\Request;
use Nova\Pagination\Paginator;
use Nova\Routing\Route;
use Nova\Routing\UrlGenerator;
use Nova\Support\Arr;

use Closure;


class SetupRoutePagination
{
    /**
     * The application implementation.
     *
     * @var \Nova\Foundation\Application
     */
    protected $app;


    /**
     * Create a new middleware instance.
     *
     * @param  \Nova\Foundation\Application  $app
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
    public function handle(Request $request, Closure $next)
    {
        $route = $request->route();

        // Setup the pagination for the route mode.
        Paginator::enableRouteMode();

        Paginator::currentPathResolver(function ($pageName = 'page') use ($route)
        {
            $path = $route->uri();

            if (preg_match('#^(.*)/' .$pageName .'/\{page\}$#s', $path, $matches) === 1) {
                $path = $matches[1];
            }

            $parameters = $route->parameters();

            $path = preg_replace_callback('#\{(.*?)\??\}#', function ($matches) use ($parameters)
            {
                return Arr::get($parameters, $matches[1], $matches[0]);

            }, $path);

            return $this->app['url']->to($path);
        });

        Paginator::currentPageResolver(function ($pageName = 'page') use ($route)
        {
            return $route->parameter($pageName, 1);
        });

        return $next($request);
    }
}
