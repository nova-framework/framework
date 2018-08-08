<?php

namespace Shared\Pagination\Middleware;

use Nova\Foundation\Application;
use Nova\Http\Request;
use Nova\Pagination\AbstractPaginator;
use Nova\Pagination\Paginator;
use Nova\Routing\Route;
use Nova\Support\Arr;

use Shared\Pagination\UrlGenerator;

use Closure;


class SetupRoutePagination
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
            $path = $this->resolveRoutePath($route);

            return $this->app['url']->to($path);
        });

        Paginator::currentPageResolver(function ($pageName = 'page') use ($route)
        {
            $page = (int) str_replace(
                $pageName .'/', '', $route->parameter('pageQuery', 1)
            );

            return $page;
        });

        Paginator::urlGeneratorResolver(function (AbstractPaginator $paginator)
        {
            return new UrlGenerator($paginator);
        });

        return $next($request);
    }

    /**
     * Resolve the parameters of a Route path.
     *
     * @param  \Nova\Routing\Route  $route
     * @return string
     */
    protected function resolveRoutePath(Route $route)
    {
        $parameters = $route->parameters();

        return preg_replace_callback('#/\{(.*?)\??\}#', function ($matches) use ($parameters)
        {
            $value = Arr::get($parameters, $name = $matches[1], trim($matches[0], '/'));

            return ($name !== 'pageQuery') ? '/' .$value : '';

        }, $route->uri());
    }
}
