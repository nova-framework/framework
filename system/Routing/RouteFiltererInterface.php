<?php

namespace Routing;


interface RouteFiltererInterface
{
    /**
     * Register a new filter with the router.
     *
     * @param  string  $name
     * @param  mixed  $callback
     * @return void
     */
    public function filter($name, $callback);

    /**
     * Call the given route filter.
     *
     * @param  string  $filter
     * @param  array  $parameters
     * @param  \Nova\Routing\Route  $route
     * @param  \Nova\Http\Request  $request
     * @param  \Nova\Http\Response|null $response
     * @return mixed
     */
    public function callRouteFilter($filter, $parameters, $route, $request, $response = null);
}
