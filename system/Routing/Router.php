<?php
/**
 * Router - routing urls to closures and controllers.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Routing;

use Core\Config;
use Core\Controller;
use Events\Dispatcher;

use Helpers\Inflector;
use Http\Request;
use Routing\AssetFileDispatcher;
use Routing\ControllerDispatcher;
use Routing\ControllerInspector;
use Routing\RouteCollection;
use Routing\RouteFiltererInterface;
use Routing\Route;

use Illuminate\Container\Container;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesser;

use Language;
use Response;


/**
 * Router class will load requested Controller / Closure based on URL.
 */
class Router implements RouteFiltererInterface
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
     * @var \Illuminate\Container\Container
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
    protected $assetDispatcher;

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
     * Default Route, usually the Catch-All one.
     *
     * @var Route $defaultRoute
     */
    private $defaultRoute = null;

    /**
     * An array of HTTP request Methods.
     *
     * @var array $methods
     */
    public static $methods = array('GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS');

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
     * Register catchAll route.
     *
     * @param $callback
     */
    public function catchAll($action)
    {
        if (! is_array($action)) $action = array('uses' => $action);

        if ($this->routingToController($action)) {
            $action = $this->getControllerAction($action);
        }

        //
        $methods = array('GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE');

        $this->defaultRoute = new Route($methods, '(:all)', $action);
    }

    /**
     * Register many request URIs to a single Callback.
     *
     * <code>
     *      // Register a group of URIs for a Callback
     *      Router::share(array(array('GET', '/'), array('POST', '/home')), 'App\Controllers\Home@index');
     * </code>
     *
     * @param  array  $routes
     * @param  mixed  $callback
     * @return void
     */
    public function share($routes, $callback)
    {
        foreach ($routes as $entry) {
            $method = array_shift($entry);
            $route  = array_shift($entry);

            // Register the route.
            $this->addRoute($method, $route, $callback);
        }
    }

    /**
     * Defines a Route Group.
     *
     * @param string $group The scope of the current Routes Group
     * @param callback $callback Callback object called to define the Routes.
     */
    public function group($group, $callback)
    {
        if (! is_array($group)) {
            $group = array('prefix' => $group);
        }

        // Add the Route Group to the array.
        array_push($this->groupStack, $group);

        // Call the Callback, to define the Routes on the current Group.
        call_user_func($callback);

        // Removes the last Route Group from the array.
        array_pop($this->groupStack);
    }

    /* The Resourceful Routes in the Laravel Style.

    Method     |  Path                 |  Action   |
    ------------------------------------------------
    GET        |  /photo               |  index    |
    GET        |  /photo/create        |  create   |
    POST       |  /photo               |  store    |
    GET        |  /photo/{photo}       |  show     |
    GET        |  /photo/{photo}/edit  |  edit     |
    PUT/PATCH  |  /photo/{photo}       |  update   |
    DELETE     |  /photo/{photo}       |  destroy  |

    */

    /**
     * Defines a Resourceful Routes Group to a target Controller.
     *
     * @param string $basePath The base path of the resourceful routes group
     * @param string $controller The target Resourceful Controller's name.
     */
    public function resource($basePath, $controller)
    {
        $this->addRoute('GET',                 $basePath,                 $controller .'@index');
        $this->addRoute('GET',                 $basePath .'/create',      $controller .'@create');
        $this->addRoute('POST',                $basePath,                 $controller .'@store');
        $this->addRoute('GET',                 $basePath .'/(:any)',      $controller .'@show');
        $this->addRoute('GET',                 $basePath .'/(:any)/edit', $controller .'@edit');
        $this->addRoute(array('PUT', 'PATCH'), $basePath .'/(:any)',      $controller .'@update');
        $this->addRoute('DELETE',              $basePath .'/(:any)',      $controller .'@delete');
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
     * @return void
     */
    public function controller($uri, $controller)
    {
        $prepended = $controller;

        if (! empty($this->groupStack)) {
            $prepended = $this->prependGroupUses($controller);
        }

        // Retrieve the Controller routable methods and associated information.
        $routable = $this->getInspector()->getRoutable($prepended, $uri);

        foreach ($routable as $method => $routes) {
            foreach ($routes as $route) {
                $action = array('uses' => $controller .'@' .$method);

                $this->addRoute($route['verb'], $route['uri'], $action);
            }
        }

        $this->addRoute('ANY', $uri .'/(:all)', $controller .'@missingMethod');
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
        // Add the current Route instance to the known Routes list.
        return $this->routes->add($this->createRoute($methods, $route, $action));
    }

    /**
     * Create a new route instance.
     *
     * @param  array|string  $methods
     * @param  string  $route
     * @param  mixed   $action
     * @return \Routing\Route
     */
    protected function createRoute($methods, $route, $action)
    {
        // Prepare the route Methods.
        if (is_string($methods) && (strtolower($methods) == 'any')) {
            $methods = static::$methods;
        } else {
            $methods = array_map('strtoupper', is_array($methods) ? $methods : array($methods));

            // Ensure the requested Methods are valid ones.
            $methods = array_intersect($methods, static::$methods);
        }

        if (empty($methods)) {
            // If there are no valid Methods defined, fallback to ANY.
            $methods = static::$methods;
        }

        // Prepare the Route PATTERN.
        $pattern = ltrim($route, '/');

        // Pre-process the Action information.
        if (! is_array($action)) $action = array('uses' => $action);

        if (! empty($this->groupStack)) {
            $parts = array();

            foreach ($this->groupStack as $group) {
                // Add the current prefix to the prefix list.
                array_push($parts, trim($group['prefix'], '/'));
            }

            // Adjust the Route PATTERN, if it is needed.
            $parts = array_filter($parts, function($value)
            {
                return ! empty($value);
            });

            if (! empty($parts)) {
                $prefix = implode('/', $parts);

                $action['prefix'] = $prefix;
            }
        }

        if ($this->routingToController($action)) {
            $action = $this->getControllerAction($action);
        }

        // Create a Route instance.
        return $this->newRoute($methods, $pattern, $action);
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
        return new Route($methods, $uri, $action);
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
     * Call the given Route's before filters.
     *
     * @param  \Routing\Route  $route
     * @param  \Http\Request  $request
     * @return mixed
     */
    protected function callRouteFilters(Route $route, Request $request)
    {
        foreach ($route->getFilters() as $filter => $parameters) {
            $response = $this->callRouteFilter($filter, $parameters, $route, $request);

            if (! is_null($response)) return $response;
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
    public function callRouteFilter($filter, $parameters, Route $route, Request $request)
    {
        if ( ! $this->filtering) return null;

        $data = array_merge(array($route, $request), $parameters);

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

        // First, we will supose that URI is associated with an Asset File.
        $response = $this->dispatchAssetFile($request);

        if (is_null($response)) {
            $response = $this->dispatchToRoute($request);
        }

        return $this->prepareResponse($request, $response);
    }

    /**
     * Dispatch the request to a asset file and return the response.
     *
     * @param  \Http\Request  $request
     * @return mixed
     */
    public function dispatchAssetFile(Request $request)
    {
        $assetDispatcher = $this->getAssetFileDispatcher();

        return $assetDispatcher->dispatch($request);
    }

    /**
     * Dispatch the request to a route and return the response.
     *
     * @param  \Http\Request  $request
     * @return mixed
     */
    public function dispatchToRoute(Request $request)
    {
        // If there exists a Catch-All Route, firstly we add it to Routes list.
        if (isset($this->defaultRoute)) {
            $this->routes->add($this->defaultRoute);
        }

        // Execute the Routes matching.
        $route = $this->findRoute($request);

        $this->events->fire('router.matched', array($route, $request));

        // Call the Route's associated Filters.
        $response = $this->callRouteFilters($route, $request);

        if(! $response instanceof SymfonyResponse) {
            $response = $route->run();
        }

        return $response;
    }

    /**
     * Find the route matching a given request.
     *
     * @param  \Http\Request  $request
     * @return \Routing\Route
     */
    protected function findRoute($request)
    {
        return $this->currentRoute = $this->routes->match($request);
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
    public function getAssetFileDispatcher()
    {
        if (is_null($this->assetDispatcher)) {
            $this->assetDispatcher = new AssetFileDispatcher();
        }

        return $this->assetDispatcher;
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
     * Get a route parameter for the current route.
     *
     * @param  string  $key
     * @param  string  $default
     * @return mixed
     */
    public function input($key, $default = null)
    {
        return $this->current()->parameter($key, $default);
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

    /**
     * Return the current Matched Language, if there are any.
     *
     * @return null|string
     */
    public function getLanguage()
    {
        $route = $this->getCurrentRoute();

        if(! is_null($route)) {
            return $route->getLanguage();
        }

        return Language::code();
    }
}
