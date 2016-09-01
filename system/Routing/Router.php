<?php
/**
 * Router - routing urls to closures and controllers.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Routing;

use Config\Config;
use App\Core\Controller;
use Events\Dispatcher;

use Container\Container;
use Helpers\Inflector;
use Http\Request;
use Http\Response;
use Routing\ControllerDispatcher;
use Routing\ControllerInspector;
use Routing\FileDispatcher;
use Routing\RouteCollection;
use Routing\RouteFiltererInterface;
use Routing\Route;

use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesser;
use Symfony\Component\HttpKernel\HttpKernelInterface;

use Closure;
use BadMethodCallException;


/**
 * Router class will load requested Controller / Closure based on URL.
 */
class Router implements HttpKernelInterface, RouteFiltererInterface
{
    /**
     * Indicates if the router is running filters.
     *
     * @var bool
     */
    protected $filtering = true;

    /**
     * The route collection instance.
     *
     * @var \Routing\RouteCollection
     */
    protected $routes;

    /**
     * Matched Route, the current found Route, if any.
     *
     * @var Route|null $currentRoute
     */
    protected $currentRoute = null;

    /**
     * The event dispatcher instance.
     *
     * @var \Events\Dispatcher
     */
    protected $events;

    /**
     * The IoC container instance.
     *
     * @var \Container\Container
     */
    protected $container;

    /**
     * The controller inspector instance.
     *
     * @var \Routing\ControllerInspector
     */
    protected $inspector;

    /**
     * The controller dispatcher instance.
     *
     * @var \Routing\ControllerDispatcher
     */
    protected $controllerDispatcher;

    /**
     * The asset file dispatcher instance.
     *
     * @var \Routing\AssetFileDispatcher
     */
    protected $fileDispatcher;

    /**
     * The request currently being dispatched.
     *
     * @var \Http\Request
     */
    protected $currentRequest;

    /**
     * Array of Route Groups
     *
     * @var array $groupStack
     */
    private $groupStack = array();

    /**
     * An array of HTTP request Methods.
     *
     * @var array $methods
     */
    public static $methods = array('GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS');

    /**
     * The default actions for a resourceful controller.
     *
     * @var array
     */
    protected $resourceDefaults = array('index', 'create', 'store', 'show', 'edit', 'update', 'destroy');

    /**
     * Boolean indicating the use of Named Parameters on not.
     *
     * @var bool $namedParams
     */
    protected $namedParams = true;


    /**
     * Router constructor.
     *
     * @codeCoverageIgnore
     */
    public function __construct(Dispatcher $events = null, Container $container = null)
    {
        $this->events = $events;

        $this->routes = new RouteCollection();

        $this->container = $container ?: new Container();

        // Wheter or not are used the Named Parameters.
        if ('unnamed' == Config::get('routing.parameters', 'named')) {
            $this->namedParams = false;
        }
    }

    /**
     * Get the response for a given request.
     *
     * @param  \Symfony\Component\HttpFoundation\Request  $request
     * @param  int   $type
     * @param  bool  $catch
     * @return \Http\Response
     */
    public function handle(SymfonyRequest $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true)
    {
        return $this->dispatch(Request::createFromBase($request));
    }

    /**
     * Register a new GET route with the router.
     *
     * @param  string  $uri
     * @param  \Closure|array|string  $action
     * @return \Routing\Route
     */
    public function get($route, $action)
    {
        return $this->addRoute(array('GET', 'HEAD'), $route, $action);
    }

    /**
     * Register a new POST route with the router.
     *
     * @param  string  $uri
     * @param  \Closure|array|string  $action
     * @return \Routing\Route
     */
    public function post($route, $action)
    {
        return $this->addRoute('POST', $route, $action);
    }

    /**
     * Register a new PUT route with the router.
     *
     * @param  string  $uri
     * @param  \Closure|array|string  $action
     * @return \Routing\Route
     */
    public function put($route, $action)
    {
        return $this->addRoute('PUT', $route, $action);
    }

    /**
     * Register a new PATCH route with the router.
     *
     * @param  string  $uri
     * @param  \Closure|array|string  $action
     * @return \Routing\Route
     */
    public function patch($route, $action)
    {
        return $this->addRoute('PATCH', $route, $action);
    }

    /**
     * Register a new DELETE route with the router.
     *
     * @param  string  $uri
     * @param  \Closure|array|string  $action
     * @return \Routing\Route
     */
    public function delete($route, $action)
    {
        return $this->addRoute('DELETE', $route, $action);
    }

    /**
     * Register a new OPTIONS route with the router.
     *
     * @param  string  $uri
     * @param  \Closure|array|string  $action
     * @return \Routing\Route
     */
    public function options($route, $action)
    {
        return $this->addRoute('OPTIONS', $route, $action);
    }

    /**
     * Register a new route responding to all verbs.
     *
     * @param  string  $uri
     * @param  \Closure|array|string  $action
     * @return \Routing\Route
     */
    public function any($route, $action)
    {
        $methods = array('GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE');

        return $this->addRoute($methods, $route, $action);
    }

    /**
     * Register a new route with the given verbs.
     *
     * @param  array|string  $methods
     * @param  string  $uri
     * @param  \Closure|array|string  $action
     * @return \Routing\Route
     */
    public function match($methods, $route, $action)
    {
        $methods = array_map('strtoupper', (array) $methods);

        return $this->addRoute($methods, $route, $action);
    }

    /**
     * Register an array of controllers with wildcard routing.
     *
     * @param  array  $controllers
     * @return void
     */
    public function controllers(array $controllers)
    {
        foreach ($controllers as $uri => $name) {
            $this->controller($uri, $name);
        }
    }

    /**
     * Route a Controller to a URI with wildcard routing.
     *
     * @param  string  $uri
     * @param  string  $controller
     * @param  array   $names
     * @return void
     * @throw  \BadMethodCallException
     */
    public function controller($uri, $controller, $names = array())
    {
        if (! $this->namedParams) {
            throw new BadMethodCallException("Not available while using Unnamed Parameters.");
        }

        $inspector = $this->getInspector();

        //
        $prepended = $controller;

        if (! empty($this->groupStack)) {
            $prepended = $this->prependGroupUses($controller);
        }

        // Retrieve the Controller routable methods and associated information.
        $routable = $inspector->getRoutable($prepended, $uri);

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
        $action['as'] = array_get($names, $method);

        $this->{$route['verb']}($route['uri'], $action);
    }

    /**
     * Add a fallthrough route for a controller.
     *
     * @param  string  $controller
     * @param  string  $uri
     * @return void
     * @throw  \BadMethodCallException
     */
    protected function addFallthroughRoute($controller, $uri)
    {
        $route = $this->any($uri .'/{_missing}', $controller .'@missingMethod');

        $route->where('_missing', '(.*)');
    }

    /**
     * Route a resource to a controller.
     *
     * @param  string  $name
     * @param  string  $controller
     * @param  array   $options
     * @return void
     */
    public function resource($name, $controller, array $options = array())
    {
        if (! $this->namedParams) {
            throw new BadMethodCallException("Not available while using Unnamed Parameters.");
        }

        if (str_contains($name, '/')) {
            $this->prefixedResource($name, $controller, $options);

            return;
        }

        $base = $this->getResourceWildcard(last(explode('.', $name)));

        $defaults = $this->resourceDefaults;

        foreach ($this->getResourceMethods($defaults, $options) as $method) {
            $this->{'addResource'.ucfirst($method)}($name, $base, $controller, $options);
        }
    }

    /**
     * Build a set of prefixed resource routes.
     *
     * @param  string  $name
     * @param  string  $controller
     * @param  array   $options
     * @return void
     */
    protected function prefixedResource($name, $controller, array $options)
    {
        list($name, $prefix) = $this->getResourcePrefix($name);

        $callback = function($me) use ($name, $controller, $options)
        {
            $me->resource($name, $controller, $options);
        };

        return $this->group(compact('prefix'), $callback);
    }

    /**
     * Extract the resource and prefix from a resource name.
     *
     * @param  string  $name
     * @return array
     */
    protected function getResourcePrefix($name)
    {
        $segments = explode('/', $name);

        $prefix = implode('/', array_slice($segments, 0, -1));

        $name = end($segments);

        return array($name, $prefix);
    }

    /**
     * Get the applicable resource methods.
     *
     * @param  array  $defaults
     * @param  array  $options
     * @return array
     */
    protected function getResourceMethods($defaults, $options)
    {
        if (isset($options['only'])) {
            return array_intersect($defaults, (array) $options['only']);
        } else if (isset($options['except'])) {
            return array_diff($defaults, (array) $options['except']);
        }

        return $defaults;
    }

    /**
     * Get the base resource URI for a given resource.
     *
     * @param  string  $resource
     * @return string
     */
    public function getResourceUri($resource)
    {
        if (! str_contains($resource, '.')) return $resource;

        $segments = explode('.', $resource);

        $uri = $this->getNestedResourceUri($segments);

        return str_replace('/{'.$this->getResourceWildcard(last($segments)).'}', '', $uri);
    }

    /**
     * Get the URI for a nested resource segment array.
     *
     * @param  array   $segments
     * @return string
     */
    protected function getNestedResourceUri(array $segments)
    {
        return implode('/', array_map(function($segment)
        {
            return $segment .'/{'.$this->getResourceWildcard($segment).'}';

        }, $segments));
    }

    /**
     * Get the action array for a resource route.
     *
     * @param  string  $resource
     * @param  string  $controller
     * @param  string  $method
     * @param  array   $options
     * @return array
     */
    protected function getResourceAction($resource, $controller, $method, $options)
    {
        $name = $this->getResourceName($resource, $method, $options);

        return array('as' => $name, 'uses' => $controller .'@' .$method);
    }

    /**
     * Get the name for a given resource.
     *
     * @param  string  $resource
     * @param  string  $method
     * @param  array   $options
     * @return string
     */
    protected function getResourceName($resource, $method, $options)
    {
        if (isset($options['names'][$method])) return $options['names'][$method];

        $prefix = isset($options['as']) ? $options['as'] .'.' : '';

        if (empty($this->groupStack)) {
            return $prefix .$resource .'.' .$method;
        }

        return $this->getGroupResourceName($prefix, $resource, $method);
    }

    /**
     * Get the resource name for a grouped resource.
     *
     * @param  string  $prefix
     * @param  string  $resource
     * @param  string  $method
     * @return string
     */
    protected function getGroupResourceName($prefix, $resource, $method)
    {
        $group = str_replace('/', '.', $this->getLastGroupPrefix());

        if (empty($group)) {
            return trim("{$prefix}{$resource}.{$method}", '.');
        }

        return trim("{$prefix}{$group}.{$resource}.{$method}", '.');
    }

    /**
     * Format a resource wildcard for usage.
     *
     * @param  string  $value
     * @return string
     */
    public function getResourceWildcard($value)
    {
        return str_replace('-', '_', $value);
    }

    /**
     * Add the index method for a resourceful route.
     *
     * @param  string  $name
     * @param  string  $base
     * @param  string  $controller
     * @param  array   $options
     * @return \Routing\Route
     */
    protected function addResourceIndex($name, $base, $controller, $options)
    {
        $uri = $this->getResourceUri($name);

        $action = $this->getResourceAction($name, $controller, 'index', $options);

        return $this->get($uri, $action);
    }

    /**
     * Add the create method for a resourceful route.
     *
     * @param  string  $name
     * @param  string  $base
     * @param  string  $controller
     * @param  array   $options
     * @return \Routing\Route
     */
    protected function addResourceCreate($name, $base, $controller, $options)
    {
        $uri = $this->getResourceUri($name).'/create';

        $action = $this->getResourceAction($name, $controller, 'create', $options);

        return $this->get($uri, $action);
    }

    /**
     * Add the store method for a resourceful route.
     *
     * @param  string  $name
     * @param  string  $base
     * @param  string  $controller
     * @param  array   $options
     * @return \Routing\Route
     */
    protected function addResourceStore($name, $base, $controller, $options)
    {
        $uri = $this->getResourceUri($name);

        $action = $this->getResourceAction($name, $controller, 'store', $options);

        return $this->post($uri, $action);
    }

    /**
     * Add the show method for a resourceful route.
     *
     * @param  string  $name
     * @param  string  $base
     * @param  string  $controller
     * @param  array   $options
     * @return \Routing\Route
     */
    protected function addResourceShow($name, $base, $controller, $options)
    {
        $uri = $this->getResourceUri($name).'/{'.$base.'}';

        $action = $this->getResourceAction($name, $controller, 'show', $options);

        return $this->get($uri, $action);
    }

    /**
     * Add the edit method for a resourceful route.
     *
     * @param  string  $name
     * @param  string  $base
     * @param  string  $controller
     * @param  array   $options
     * @return \Routing\Route
     */
    protected function addResourceEdit($name, $base, $controller, $options)
    {
        $uri = $this->getResourceUri($name).'/{'.$base.'}/edit';

        $action = $this->getResourceAction($name, $controller, 'edit', $options);

        return $this->get($uri, $action);
    }

    /**
     * Add the update method for a resourceful route.
     *
     * @param  string  $name
     * @param  string  $base
     * @param  string  $controller
     * @param  array   $options
     * @return void
     */
    protected function addResourceUpdate($name, $base, $controller, $options)
    {
        $this->addPutResourceUpdate($name, $base, $controller, $options);

        return $this->addPatchResourceUpdate($name, $base, $controller);
    }

    /**
     * Add the update method for a resourceful route.
     *
     * @param  string  $name
     * @param  string  $base
     * @param  string  $controller
     * @param  array   $options
     * @return \Routing\Route
     */
    protected function addPutResourceUpdate($name, $base, $controller, $options)
    {
        $uri = $this->getResourceUri($name).'/{'.$base.'}';

        $action = $this->getResourceAction($name, $controller, 'update', $options);

        return $this->put($uri, $action);
    }

    /**
     * Add the update method for a resourceful route.
     *
     * @param  string  $name
     * @param  string  $base
     * @param  string  $controller
     * @return void
     */
    protected function addPatchResourceUpdate($name, $base, $controller)
    {
        $uri = $this->getResourceUri($name).'/{'.$base.'}';

        $this->patch($uri, $controller.'@update');
    }

    /**
     * Add the destroy method for a resourceful route.
     *
     * @param  string  $name
     * @param  string  $base
     * @param  string  $controller
     * @param  array   $options
     * @return \Routing\Route
     */
    protected function addResourceDestroy($name, $base, $controller, $options)
    {
        $uri = $this->getResourceUri($name).'/{'.$base.'}';

        $action = $this->getResourceAction($name, $controller, 'destroy', $options);

        return $this->delete($uri, $action);
    }

    /**
     * Create a route group with shared attributes.
     *
     * @param  array     $attributes
     * @param  \Closure  $callback
     * @return void
     */
    public function group(array $attributes, Closure $callback)
    {
        $this->updateGroupStack($attributes);

        // Execute the group callback.
        call_user_func($callback, $this);

        array_pop($this->groupStack);
    }

    /**
     * Update the group stack with the given attributes.
     *
     * @param  array  $attributes
     * @return void
     */
    protected function updateGroupStack(array $attributes)
    {
        if (! empty($this->groupStack)) {
            $attributes = static::mergeGroup($attributes, last($this->groupStack));
        }

        $this->groupStack[] = $attributes;
    }

    /**
     * Merge the given array with the last group stack.
     *
     * @param  array  $new
     * @return array
     */
    public function mergeWithLastGroup($new)
    {
        $old = last($this->groupStack);

        return static::mergeGroup($new, $old);
    }

    /**
     * Merge the given group attributes.
     *
     * @param  array  $new
     * @param  array  $old
     * @return array
     */
    public static function mergeGroup($new, $old)
    {
        $new['namespace'] = static::formatUsesPrefix($new, $old);

        $new['prefix'] = static::formatGroupPrefix($new, $old);

        return array_merge_recursive(array_except($old, array('namespace', 'prefix')), $new);
    }

    /**
     * Format the uses prefix for the new group attributes.
     *
     * @param  array  $new
     * @param  array  $old
     * @return string
     */
    protected static function formatUsesPrefix($new, $old)
    {
        if (isset($new['namespace']) && isset($old['namespace'])) {
            return trim(array_get($old, 'namespace'), '\\') .'\\' .trim($new['namespace'], '\\');
        } else if (isset($new['namespace'])) {
            return trim($new['namespace'], '\\');
        }

        return array_get($old, 'namespace');
    }

    /**
     * Format the prefix for the new group attributes.
     *
     * @param  array  $new
     * @param  array  $old
     * @return string
     */
    protected static function formatGroupPrefix($new, $old)
    {
        if (isset($new['prefix'])) {
            return trim(array_get($old, 'prefix'), '/') .'/' .trim($new['prefix'], '/');
        }

        return array_get($old, 'prefix');
    }

    /**
     * Get the prefix from the last group on the stack.
     *
     * @return string
     */
    protected function getLastGroupPrefix()
    {
        if (! empty($this->groupStack)) {
            $last = end($this->groupStack);

            return isset($last['prefix']) ? $last['prefix'] : '';
        }

        return '';
    }

    /**
     * Add a route to the underlying route collection.
     *
     * @param  array|string  $methods
     * @param  string  $uri
     * @param  \Closure|array|string  $action
     * @return \Routing\Route
     */
    protected function addRoute($methods, $route, $action = null)
    {
        $route = $this->createRoute($methods, $route, $action);

        // Add the current Route instance to the known Routes list.
        return $this->routes->add($route);
    }

    /**
     * Create a new route instance.
     *
     * @param  array|string  $methods
     * @param  string  $uri
     * @param  mixed   $action
     * @return \Routing\Route
     */
    protected function createRoute($methods, $uri, $action)
    {
        if ($this->routingToController($action)) {
            $action = $this->getControllerAction($action);
        }

        // Prefix the current route pattern.
        $uri = $this->prefix($uri);

        $route = $this->newRoute($methods, $uri, $action);

        if (! empty($this->groupStack)) {
            $this->mergeController($route);
        }

        return $route;
    }

    /**
     * Create a new Route object.
     *
     * @param  array|string  $methods
     * @param  string  $uri
     * @param  mixed   $action
     * @return \Routing\Route
     */
    protected function newRoute($methods, $uri, $action)
    {
        return new Route($methods, $uri, $action, $this->namedParams);
    }

    /**
     * Prefix the given URI with the last prefix.
     *
     * @param  string  $uri
     * @return string
     */
    protected function prefix($uri)
    {
        $prefix = $this->getLastGroupPrefix();

        return trim(trim($prefix, '/') .'/' .trim($uri, '/'), '/') ?: '/';
    }

    /**
     * Merge the group stack with the controller action.
     *
     * @param  \Routing\Route  $route
     * @return void
     */
    protected function mergeController($route)
    {
        $action = $this->mergeWithLastGroup($route->getAction());

        $route->setAction($action);
    }

    /**
     * Create a response instance from the given value.
     *
     * @param  \Symfony\Component\HttpFoundation\Request  $request
     * @param  mixed  $response
     * @return \Http\Response
     */
    protected function prepareResponse($request, $response)
    {
        if (! $response instanceof SymfonyResponse) {
            $response = new Response($response);
        }

        return $response->prepare($request);
    }

    /**
     * Determine if the action is routing to a controller.
     *
     * @param  array  $action
     * @return bool
     */
    protected function routingToController($action)
    {
        if ($action instanceof Closure) return false;

        return is_string($action) || is_string(array_get($action, 'uses'));
    }

    /**
     * Add a controller based route action to the action array.
     *
     * @param  array|string  $action
     * @return array
     */
    protected function getControllerAction($action)
    {
        if (is_string($action)) $action = array('uses' => $action);

        if (! empty($this->groupStack)) {
            $action['uses'] = $this->prependGroupUses($action['uses']);
        }

        $action['controller'] = $action['uses'];

        $closure = $this->getClassClosure($action['uses']);

        return array_set($action, 'uses', $closure);
    }

    /**
     * Get the Closure for a controller based action.
     *
     * @param  string  $controller
     * @return \Closure
     */
    protected function getClassClosure($controller)
    {
        $dispatcher = $this->getControllerDispatcher();

        return function() use ($dispatcher, $controller)
        {
            $route = $this->getCurrentRoute();

            $request = $this->getCurrentRequest();

            //
            list($class, $method) = explode('@', $controller);

            return $dispatcher->dispatch($route, $request, $class, $method);
        };
    }

    /**
     * Prepend the last group uses onto the use clause.
     *
     * @param  string  $uses
     * @return string
     */
    protected function prependGroupUses($uses)
    {
        $group = last($this->groupStack);

        return isset($group['namespace']) ? $group['namespace'] .'\\' .$uses : $uses;
    }

    /**
     * Dispatch route
     * @return bool
     */
    public function dispatch(Request $request)
    {
        $this->currentRequest = $request;

        // Asset Files Dispatching.
        $response = $this->dispatchToFile($request);

        if (! is_null($response)) {
            return $this->prepareResponse($request, $response);
        }

        // Request Dispatching to Routes.
        $response = $this->callFilter('before', $request);

        if (is_null($response)) {
            $response = $this->dispatchToRoute($request);
        }

        $response = $this->prepareResponse($request, $response);

        $this->callFilter('after', $request, $response);

        return $response;
    }

    /**
     * Dispatch the request to a route and return the response.
     *
     * @param  \Http\Request  $request
     * @return mixed
     */
    public function dispatchToRoute(Request $request)
    {
        // Execute the Routes matching.
        $route = $this->findRoute($request);

        $this->events->fire('router.matched', array($route, $request));

        // Call the Route's Before Filters.
        $response = $this->callRouteBefore($route, $request);

        if (is_null($response)) {
            // Run the Route Callback.
            $response = $route->run();
        }

        // Prepare the Reesponse.
        $response = $this->prepareResponse($request, $response);

        // Call the Route's After Filters.
        $this->callRouteAfter($route, $request, $response);

        return $response;
    }

    /**
     * Dispatch the request to a asset file and return the response.
     *
     * @param  \Http\Request  $request
     * @return mixed
     */
    public function dispatchToFile(Request $request)
    {
        $fileDispatcher = $this->getFileDispatcher();

        return $fileDispatcher->dispatch($request);
    }

    /**
     * Find the route matching a given request.
     *
     * @param  \Http\Request  $request
     * @return \Routing\Route
     */
    protected function findRoute(Request $request)
    {
        return $this->currentRoute = $this->routes->match($request);
    }

    /**
     * Register a route matched event listener.
     *
     * @param  string|callable  $callback
     * @return void
     */
    public function matched($callback)
    {
        $this->events->listen('router.matched', $callback);
    }

    /**
     * Register a new "before" filter with the router.
     *
     * @param  string|callable  $callback
     * @return void
     */
    public function before($callback)
    {
        $this->addGlobalFilter('before', $callback);
    }

    /**
     * Register a new "after" filter with the router.
     *
     * @param  string|callable  $callback
     * @return void
     */
    public function after($callback)
    {
        $this->addGlobalFilter('after', $callback);
    }

    /**
     * Register a new global filter with the router.
     *
     * @param  string  $filter
     * @param  string|callable   $callback
     * @return void
     */
    protected function addGlobalFilter($filter, $callback)
    {
        $this->events->listen('router.'.$filter, $this->parseFilter($callback));
    }

    /**
     * Register a new Filter with the Router.
     *
     * @param  string  $name
     * @param  string|callable  $callback
     * @return void
     */
    public function filter($name, $callback)
    {
        $this->events->listen('router.filter: '.$name, $this->parseFilter($callback));
    }

    /**
     * Parse the registered Filter.
     *
     * @param  callable|string  $callback
     * @return mixed
     */
    protected function parseFilter($callback)
    {
        if (is_string($callback) && ! str_contains($callback, '@')) {
            return $callback .'@filter';
        }

        return $callback;
    }

    /**
     * Call the given filter with the request and response.
     *
     * @param  string  $filter
     * @param  \Http\Request   $request
     * @param  \Http\Response  $response
     * @return mixed
     */
    protected function callFilter($filter, $request, $response = null)
    {
        if (! $this->filtering) return null;

        return $this->events->until('router.'.$filter, array($request, $response));
    }

    /**
     * Call the given route's before filters.
     *
     * @param  \Routing\Route  $route
     * @param  \Http\Request  $request
     * @return mixed
     */
    public function callRouteBefore($route, $request)
    {
        foreach ($route->beforeFilters() as $filter => $parameters) {
            $response = $this->callRouteFilter($filter, $parameters, $route, $request);

            if (! is_null($response)) return $response;
        }
    }

    /**
     * Call the given route's before filters.
     *
     * @param  \Routing\Route  $route
     * @param  \Http\Request  $request
     * @param  \Http\Response  $response
     * @return mixed
     */
    public function callRouteAfter($route, $request, $response)
    {
        foreach ($route->afterFilters() as $filter => $parameters) {
            $this->callRouteFilter($filter, $parameters, $route, $request, $response);
        }
    }

    /**
     * Call the given Route Filter.
     *
     * @param  string  $filter
     * @param  array  $parameters
     * @param  \Routing\Route  $route
     * @param  \Http\Request  $request
     * @return mixed
     */
    public function callRouteFilter($filter, $parameters, $route, $request, $response = null)
    {
        if (! $this->filtering) return null;

        $data = array_merge(array($route, $request, $response), $parameters);

        return $this->events->until('router.filter: '.$filter, $this->cleanFilterParameters($data));
    }

    /**
     * Clean the parameters being passed to a filter callback.
     *
     * @param  array  $parameters
     * @return array
     */
    protected function cleanFilterParameters(array $parameters)
    {
        return array_filter($parameters, function($p)
        {
            return ! is_null($p) && ($p !== '');
        });
    }

    /**
     * Get the controller dispatcher instance.
     *
     * @return \Routing\ControllerDispatcher
     */
    public function getControllerDispatcher()
    {
        if (is_null($this->controllerDispatcher)) {
            $this->controllerDispatcher = new ControllerDispatcher($this, $this->container);
        }

        return $this->controllerDispatcher;
    }

    /**
     * Set the controller dispatcher instance.
     *
     * @param  \Routing\ControllerDispatcher  $dispatcher
     * @return void
     */
    public function setControllerDispatcher(ControllerDispatcher $dispatcher)
    {
        $this->controllerDispatcher = $dispatcher;
    }

    /**
     * Get the controller dispatcher instance.
     *
     * @return \Routing\ControllerDispatcher
     */
    public function getFileDispatcher()
    {
        return $this->fileDispatcher ?: new FileDispatcher();
    }

    /**
     * Get a Controller Inspector instance.
     *
     * @return \Routing\ControllerInspector
     */
    public function getInspector()
    {
        return $this->inspector ?: $this->inspector = new ControllerInspector();
    }

    /**
     * Run a callback with filters disable on the router.
     *
     * @param  callable  $callback
     * @return void
     */
    public function withoutFilters(callable $callback)
    {
        $this->disableFilters();

        call_user_func($callback);

        $this->enableFilters();
    }

    /**
     * Enable route filtering on the router.
     *
     * @return void
     */
    public function enableFilters()
    {
        $this->filtering = true;
    }

    /**
     * Disable route filtering on the router.
     *
     * @return void
     */
    public function disableFilters()
    {
        $this->filtering = false;
    }

    /**
     * Return the available Filters.
     *
     * @return array
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * Return the current Matched Route, if there are any.
     *
     * @return null|Route
     */
    public function getCurrentRoute()
    {
        return $this->current();
    }

    /**
     * Get the currently dispatched route instance.
     *
     * @return \Routing\Route
     */
    public function current()
    {
        return $this->currentRoute;
    }

    /**
     * Return the available Routes.
     *
     * @return \Routing\RouteCollection
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * Get the request currently being dispatched.
     *
     * @return \Http\Request
     */
    public function getCurrentRequest()
    {
        return $this->currentRequest;
    }

}
