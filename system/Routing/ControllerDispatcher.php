<?php

namespace Routing;

use Closure;
use Http\Request;
use Routing\RouteFiltererInterface;

use Container\Container;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;


class ControllerDispatcher
{
    /**
     * The routing filterer implementation.
     *
     * @var \Routing\RouteFiltererInterface  $filterer
     */
    protected $filterer;

    /**
     * The IoC container instance.
     *
     * @var \Container\Container
     */
    protected $container;

    /**
     * Create a new controller dispatcher instance.
     *
     * @param  \Routing\RouteFiltererInterface  $filterer
     * @param  \Container\Container  $container
     * @return void
     */
    public function __construct(RouteFiltererInterface $filterer, Container $container)
    {
        $this->filterer = $filterer;

        $this->container = $container;
    }

    /**
     * Dispatch a request to a given controller and method.
     *
     * @param  \Routing\Route  $route
     * @param  \Http\Request  $request
     * @param  string  $controller
     * @param  string  $method
     * @return mixed
     */
    public function dispatch(Route $route, Request $request, $controller, $method)
    {
        $instance = $this->makeController($controller);

        return $this->call($instance, $route, $method);
    }

    /**
     * Make a controller instance via the IoC container.
     *
     * @param  string  $controller
     * @return mixed
     */
    protected function makeController($controller)
    {
        return $this->container->make($controller);
    }

    /**
     * Call the given controller instance method.
     *
     * @param  \Routing\Controller  $instance
     * @param  \Routing\Route  $route
     * @param  string  $method
     * @return mixed
     */
    protected function call($instance, $route, $method)
    {
        $parameters = $route->parametersWithoutNulls();

        return $instance->execute($method, $parameters);
    }

}
