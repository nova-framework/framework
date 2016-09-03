<?php

namespace Routing;

use Http\Request;
use Http\Response;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

use Countable;
use ArrayIterator;
use IteratorAggregate;


class RouteCollection implements Countable, IteratorAggregate
{
    /**
     * An array of the routes keyed by method.
     *
     * @var array
     */
    protected $routes = array();

    /**
     * An flattened array of all of the routes.
     *
     * @var array
     */
    protected $allRoutes = array();


    /**
     * Add a Route instance to the collection.
     *
     * @param  \Routing\Route  $route
     * @return \Routing\Route
     */
    public function add(Route $route)
    {
        $uri = $route->getUri();

        foreach ($route->methods() as $method) {
            $this->routes[$method][$uri] = $route;
        }

        $this->allRoutes[$method .$uri] = $route;

        return $route;
    }

    /**
     * Find the first route matching a given request.
     *
     * @param  \Http\Request  $request
     * @return \Routing\Route
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function match(Request $request)
    {
        $routes = $this->get($request->getMethod());

        // Match the Request on the Routes registered for its Method.
        $route = $this->check($routes, $request);

        if (! is_null($route)) {
            return $route->bind($request);
        }

        // No Route match found; check for the alternate HTTP Methods.
        $others = $this->checkForAlternateMethods($request);

        if (count($others) > 0) {
            return $this->getOtherMethodsRoute($request, $others);
        }

        throw new NotFoundHttpException();
    }

    /**
     * Determine if any routes match on another HTTP verb.
     *
     * @param  \Http\Request  $request
     * @return array
     */
    protected function checkForAlternateMethods($request)
    {
        $methods = array_diff(Router::$methods, array($request->getMethod()));

        //
        $others = array();

        foreach ($methods as $method) {
            if (! is_null($this->check($this->get($method), $request, false))) {
                $others[] = $method;
            }
        }

        return $others;
    }

    /**
     * Get a route (if necessary) that responds when other available methods are present.
     *
     * @param  \Http\Request  $request
     * @param  array  $others
     * @return \Routing\Route
     *
     * @throws \Symfony\Component\Routing\Exception\MethodNotAllowedHttpException
     */
    protected function getOtherMethodsRoute($request, array $others)
    {
        if ($request->method() == 'OPTIONS') {
            return (new Route('OPTIONS', $request->path(), function() use ($others)
            {
                return new Response('', 200, array('Allow' => implode(',', $others)));

            }));
        }

        $this->methodNotAllowed($others);
    }

    /**
     * Throw a method not allowed HTTP exception.
     *
     * @param  array  $others
     * @return void
     *
     * @throws \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException
     */
    protected function methodNotAllowed(array $others)
    {
        throw new MethodNotAllowedHttpException($others);
    }

    /**
     * Determine if a route in the array matches the request.
     *
     * @param  array  $routes
     * @param  \http\Request  $request
     * @param  bool  $includingMethod
     * @return \Routing\Route|null
     */
    protected function check(array $routes, $request, $includingMethod = true)
    {
        return array_first($routes, function($key, $value) use ($request, $includingMethod)
        {
            return $value->matches($request, $includingMethod);
        });
    }

    /**
     * Get all of the routes in the collection.
     *
     * @param  string|null  $method
     * @return array
     */
    protected function get($method = null)
    {
        if (is_null($method)) return $this->getRoutes();

        return array_get($this->routes, $method, array());
    }

    /**
     * Get all of the routes in the collection.
     *
     * @return array
     */
    public function getRoutes()
    {
        return array_values($this->allRoutes);
    }

    /**
     * Get an iterator for the items.
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->getRoutes());
    }

    /**
     * Count the number of items in the collection.
     *
     * @return int
     */
    public function count()
    {
        return count($this->getRoutes());
    }

}
