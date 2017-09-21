<?php

namespace Shared\Routing;

use Nova\Routing\Router;
use Nova\Support\Arr;
use Nova\Support\Str;

use ReflectionClass;
use ReflectionMethod;


class ControllerInspector
{
    /**
     * An array of HTTP verbs.
     *
     * @var array
     */
    protected $verbs = array('any', 'get', 'post', 'put', 'patch', 'delete', 'head', 'options');


    /**
     * Create a new controller inspector instance.
     *
     * @param  \Nova\Routing\Router  $router
     * @return void
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * Route a Controller to a URI with wildcard routing.
     *
     * @param  string  $uri
     * @param  string  $controller
     * @param  array   $names
     * @return void
     * @throws  \BadMethodCallException
     */
    public function register($uri, $controller, $names = array())
    {
        $prepended = $controller;

        // Compute the Controller's class name, including its namespace.
        $groupStack = $this->router->getGroupStack();

        if (! empty($groupStack)) {
            $prepended = $this->prependGroupUses($controller, last($groupStack));
        }

        // Retrieve the Controller routable methods and associated information.
        $routable = $this->getRoutable($prepended, $uri);

        foreach ($routable as $method => $routes) {
            foreach ($routes as $route) {
                $this->registerInspected($route, $controller, $method, $names);
            }
        }

        $this->addFallthroughRoute($controller, $uri);
    }

    /**
     * Register an inspected controller route.
     *
     * @param  array   $route
     * @param  string  $controller
     * @param  string  $method
     * @param  array   $names
     * @return void
     */
    protected function registerInspected($route, $controller, $method, &$names)
    {
        $action = array('uses' => $controller .'@' .$method);

        //
        $action['as'] = Arr::get($names, $method);

        call_user_func(array($this->router, $route['verb']), $route['uri'], $action);
    }

    /**
     * Add a fallthrough route for a controller.
     *
     * @param  string  $controller
     * @param  string  $uri
     * @return void
     * @throws  \BadMethodCallException
     */
    protected function addFallthroughRoute($controller, $uri)
    {
        $route = $this->router->any($uri .'/{_missing}', $controller .'@missingMethod');

        $route->where('_missing', '(.*)');
    }

    /**
     * Prepend the group uses onto the use clause.
     *
     * @param  string  $uses
     * @param  array   $group
     * @return string
     */
    protected function prependGroupUses($uses, array $group)
    {
        return isset($group['namespace']) ? $group['namespace'] .'\\' .$uses : $uses;
    }

    /**
     * Get the routable methods for a controller.
     *
     * @param  string  $controller
     * @param  string  $prefix
     * @return array
     */
    protected function getRoutable($controller, $prefix)
    {
        $routable = array();

        $reflection = new ReflectionClass($controller);

        $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);

        foreach ($methods as $method) {
            $name = $method->name;

            if (! $this->isRoutable($method, $controller)) {
                continue;
            }

            $routable[$name] = array();

            //
            $data = $this->getMethodData($method, $prefix);

            $routable[$name][] = $data;

            if ($data['plain'] == $prefix .'/index') {
                $routable[$name][] = $this->getIndexData($data, $prefix);
            }
        }

        return $routable;
    }

    /**
     * Determine if the given controller method is routable.
     *
     * @param  \ReflectionMethod  $method
     * @param  string  $controller
     * @return bool
     */
    protected function isRoutable(ReflectionMethod $method, $controller)
    {
        if ($method->class != $controller) {
            return false;
        }

        return Str::startsWith($method->name, $this->verbs);
    }

    /**
     * Get the method data for a given method.
     *
     * @param  \ReflectionMethod  $method
     * @param  string  $prefix
     * @return array
     */
    protected function getMethodData(ReflectionMethod $method, $prefix)
    {
        list ($verb, $plain) = $this->getMethodInfo($name, $prefix);

        $uri = $this->addUriWildcards($plain);

        return compact('verb', 'plain', 'uri');
    }

    /**
     * Get the routable data for an index method.
     *
     * @param  array   $data
     * @param  string  $prefix
     * @return array
     */
    protected function getIndexData($data, $prefix)
    {
        return array('verb' => $data['verb'], 'plain' => $prefix, 'uri' => $prefix);
    }

    /**
     * Determine the verb and URI from the given method name.
     *
     * @param  string  $name
     * @param  string  $prefix
     * @return string
     */
    protected function getMethodInfo($name, $prefix)
    {
        $parts = explode('_', Str::snake($name));

        return array(
            head($parts), $prefix .'/' .implode('-', array_slice($parts, 1))
        );
    }

    /**
     * Add wildcards to the given URI.
     *
     * @param  string  $uri
     * @return string
     */
    protected function addUriWildcards($uri)
    {
        return $uri .'/{one?}/{two?}/{three?}/{four?}/{five?}';
    }

}
