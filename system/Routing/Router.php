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
use Routing\ControllerInspector;
use Routing\RouteCollection;
use Routing\Route;

use Illuminate\Container\Container;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesser;

use Language;
use Response;


/**
 * Router class will load requested Controller / Closure based on URL.
 */
class Router
{
    /**
     * Array of routes
     *
     * @var Route[] $routes
     */
    protected $routes = array();

    /**
     * @var array All available Filters
     */
    private $filters = array();

    /**
     * Matched Route, the current found Route, if any.
     *
     * @var Route|null $matchedRoute
     */
    protected $matchedRoute = null;

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

        $this->container = $container ?: new Container();
    }

    /**
     * Defines a route with or without Callback and Method.
     *
     * @param string $method
     * @param array @params
     */
    public function __call($method, $params)
    {
        $method = strtoupper($method);

        if (($method != 'ANY') && ! in_array($method, static::$methods)) {
            throw new \Exception('Invalid method');
        } else if (empty($params)) {
            throw new \Exception('Invalid parameters');
        }

        // Get the Route.
        $route = array_shift($params);

        // Get the Callback, if any.
        $callback = ! empty($params) ? array_shift($params) : null;

        // Register the Route.
        $this->register($method, $route, $callback);
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
     * Return the available Filters.
     *
     * @return array
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * Return the available Routes.
     *
     * @return Route[]
     */
    public function routes()
    {
        return $this->routes;
    }

    /**
     * Defines a multi-method Route Match.
     *
     * @param array|string $method The Route's method(s).
     * @param string $route The Route definition.
     * @param callback $callback Callback object called to define the Routes.
     */
    public function match($method, $route, $callback = null)
    {
        $this->register($method, $route, $callback);
    }

    /**
     * Register catchAll route.
     *
     * @param $callback
     */
    public function catchAll($callback)
    {
        $this->defaultRoute = new Route('any', '(:all)', $callback);
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
            $this->register($method, $route, $callback);
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
        $this->register('get',                 $basePath,                 $controller .'@index');
        $this->register('get',                 $basePath .'/create',      $controller .'@create');
        $this->register('post',                $basePath,                 $controller .'@store');
        $this->register('get',                 $basePath .'/(:any)',      $controller .'@show');
        $this->register('get',                 $basePath .'/(:any)/edit', $controller .'@edit');
        $this->register(array('put', 'patch'), $basePath .'/(:any)',      $controller .'@update');
        $this->register('delete',              $basePath .'/(:any)',      $controller .'@delete');
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

        // Adjust the Controller's namespace if we are on a Group.
        if (! empty($this->groupStack)) {
            $lastGroup = end($this->groupStack);

            $namespace = array_get($lastGroup, 'namespace');

            if (! empty($namespace)) {
                $prepended = $namespace .'\\' .ltrim($controller, '\\');
            }
        }

        // Retrieve the Controller routable methods and associated information.
        $routable = $this->getInspector()->getRoutable($prepended, $uri);

        foreach ($routable as $method => $routes) {
            foreach ($routes as $route) {
                $action = array('uses' => $controller .'@' .$method);

                $this->register($route['verb'], $route['uri'], $action);
            }
        }

        $this->register('ANY', $uri .'/(:all)', $controller .'@missingMethod');
    }

    /**
     * Maps a Method and URL pattern to a Callback.
     *
     * @param string|array $method HTTP metod(s) to match
     * @param string       $route URL pattern to match
     * @param callback     $action Callback object
     */
    protected function register($method, $route, $action = null)
    {
        // Prepare the route Methods.
        if (is_string($method) && (strtolower($method) == 'any')) {
            $methods = static::$methods;
        } else {
            $methods = array_map('strtoupper', is_array($method) ? $method : array($method));

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
        if (! is_array($action)) {
            $action = array('uses' => $action);
        }

        if (! empty($this->groupStack)) {
            $parts = array();

            $namespace = null;

            foreach ($this->groupStack as $group) {
                // Add the current prefix to the prefix list.
                array_push($parts, trim($group['prefix'], '/'));

                // Always update to the last Controller namespace.
                $namespace = array_get($group, 'namespace');
            }

            // Adjust the Route PATTERN, if it is needed.
            $parts = array_filter($parts, function($value)
            {
                return ($value != '');
            });

            if (! empty($parts)) {
                $prefix = implode('/', $parts);

                $action['prefix'] = $prefix;
            }

            // Adjust the Route CALLBACK, if it is needed.
            $namespace = rtrim($namespace, '\\');

            if (! empty($namespace)) {
                $callback = array_get($action, 'uses');

                if (is_string($callback) && ! empty($callback)) {
                    $action['uses'] = $namespace .'\\' .ltrim($callback, '\\');
                }
            }
        }

        // Create a Route instance.
        $route = new Route($methods, $pattern, $action);

        // Add the current Route instance to the known Routes list.
        array_push($this->routes, $route);
    }

    /**
     * Define a Route Filter.
     *
     * @param string $name
     * @param callback $callback
     */
    public function filter($name, $callback)
    {
        if (array_key_exists($name, $this->filters)) {
            throw new \Exception('Filter already exists: ' .$name);
        }

        $this->filters[$name] = $callback;
    }

    protected function applyFiltersToRoute(Route $route)
    {
        $result = null;

        foreach ($route->getFilters() as $filter => $params) {
            if(empty($filter)) {
                continue;
            } else if (! array_key_exists($filter, $this->filters)) {
                throw new \Exception('Invalid Filter specified: ' .$filter);
            }

            // Get the current Filter Callback.
            $callback = $this->filters[$filter];

            // If the Callback returns a Response instance, the Filtering will be stopped.
            if (is_callable($callback)) {
                $result = call_user_func($callback, $route, $params);
            }

            if ($result instanceof SymfonyResponse) {
                break;
            }
        }

        return $result;
    }

    /**
     * Return the current Matched Route, if there are any.
     *
     * @return null|Route
     */
    public function getMatchedRoute()
    {
        return $this->matchedRoute;
    }

    /**
     * Return the current Matched Language, if there are any.
     *
     * @return null|string
     */
    public function getLanguage()
    {
        $route = $this->getMatchedRoute();

        if(! is_null($route)) {
            return $route->getLanguage();
        }

        return Language::code();
    }

    /**
     * Invoke the callback with its associated parameters.
     *
     * @param  callable $callback
     * @param  array $params array of matched parameters
     * @return bool
     */
    protected function invokeCallback($callback, $params = array())
    {
        $result = call_user_func_array($callback, $params);

        if($result instanceof SymfonyResponse) {
            return $result;
        }

        return Response::make($result);
    }

    /**
     * Invoke the Controller's Method with its associated parameters.
     *
     * @param  string $className to be instantiated
     * @param  string $method method to be invoked
     * @param  array $params parameters passed to method
     * @return bool
     */
    protected function invokeController($className, $method, $params)
    {
        // The Controller's the Execution Flow Methods cannot be called via Router.
        if (($method == 'execute')) {
            return Response::error(403);
        }

        // Initialize the Controller.
        /** @var Controller $controller */
        $controller = new $className();

        // Obtain the available methods into the requested Controller.
        $methods = array_map('strtolower', get_class_methods($controller));

        // The called Method should be defined right on the called Controller to be executed.
        if (! in_array(strtolower($method), $methods)) {
            return Response::error(403);
        }

        // Execute the Controller's Method with the given arguments.
        return $controller->execute($method, $params);
    }

    /**
     * Invoke the callback with its associated parameters.
     *
     * @param  callable $callback
     * @param  array $params array of matched parameters
     * @return bool
     */
    protected function invokeObject($callback, $params = array())
    {
        if (is_object($callback)) {
            // Call the Closure function with the given arguments.
            return $this->invokeCallback($callback, $params);
        }

        // Call the object Controller and its Method.
        $segments = explode('@', $callback);

        $controller = $segments[0];
        $method     = $segments[1];

        // The Method shouldn't be called 'execute' or starting with '_'; also check if the Controller's class exists.
        if (($method[0] === '_') || ! class_exists($controller)) {
            return Response::error(403);
        }

        // Invoke the Controller's Method with the given arguments.
        return $this->invokeController($controller, $method, $params);
    }

    /**
     * Dispatch route
     * @return bool
     */
    public function dispatch(Request $request)
    {
        $this->currentRequest = $request;

        // Get the Method and Path.
        $method = $request->method();

        // First, we will supose that URI is associated with an Asset File.
        if ($method == 'GET') {
            $response = $this->dispatchAssetFile($request);

            if($response instanceof SymfonyResponse) {
                return $response;
            }
        }

        return $this->dispatchToRoute($request);
    }

    /**
     * Dispatch the request to a route and return the response.
     *
     * @param  \Nova\Http\Request  $request
     * @return mixed
     */
    public function dispatchToRoute(Request $request)
    {
        // Get the Method and Path.
        $method = $request->method();

        $uri = $request->path();

        // If there exists a Catch-All Route, firstly we add it to Routes list.
        if ($this->defaultRoute !== null) {
            array_push($this->routes, $this->defaultRoute);
        }

        // Execute the Routes matching loop.
        foreach ($this->routes as $route) {
            if ($route->matches($request)) {
                // Found a valid Route; process it.
                $this->matchedRoute = $route;

                // Apply the (specified) Filters on matched Route.
                $response = $this->applyFiltersToRoute($route);

                if($response instanceof SymfonyResponse) {
                    return $response;
                }

                // Get the matched Route callback.
                $callback = $route->getCallback();

                if ($callback !== null) {
                    // Invoke the Route's Callback with the associated parameters.
                    return $this->invokeObject($callback, $route->getParams());
                }

                // There is no Callback; no content to send back.
                return Response::make('');
            }
        }

        // No valid Route found; send an Error 404 Response.
        $data = array('error' => htmlspecialchars($uri, ENT_COMPAT, 'ISO-8859-1', true));

        return Response::error(404, $data);
    }

    /**
     * Dispatch the request to a asset file and return the response.
     *
     * @param  \Nova\Http\Request  $request
     * @return mixed
     */
    public function dispatchAssetFile(Request $request)
    {
        $assetDispatcher = $this->getAssetFileDispatcher();

        return $assetDispatcher->dispatch($request);
    }
}
