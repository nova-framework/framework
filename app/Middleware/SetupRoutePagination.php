<?php

namespace App\Middleware;

use Nova\Foundation\Application;
use Nova\Http\Request;
use Nova\Pagination\Paginator;
use Nova\Routing\Route;
use Nova\Routing\UrlGenerator;
use Nova\Support\Arr;
use Nova\Support\Str;

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

        Paginator::currentPathResolver(function ($pageName = 'page') use ($route)
        {
            $parameters = $route->parameters();

            $path = preg_replace_callback('#/\{(.*?)\??\}#', function ($matches) use ($parameters)
            {
                $value = Arr::get($parameters, $name = $matches[1], trim($matches[0], '/'));

                return ($name !== 'pageQuery') ? '/' .$value : '';

            }, $route->uri());

            return $this->app['url']->to($path);
        });

        Paginator::currentPageResolver(function ($pageName = 'page') use ($route)
        {
            if (! empty($value = $route->parameter('pageQuery'))) {
                return (int) str_replace($pageName .'/', '', $value);
            }

            return 1;
        });

        Paginator::pageUrlResolver(function ($page, array $query, $path, $pageName = 'page')
        {
            if ($page > 1) {
                $path .= '/' .$pageName .'/' .$page;
            }

            return Paginator::buildPageUrl($path, $query);
        });

        return $next($request);
    }
}
