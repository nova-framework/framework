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
     * The route collection instance.
     *
     * @var \Routing\RouteCollection
     */
    protected $routes;

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
     * Defines a route with or without Callback and Method.
     *
     * @param string $method
     * @param array @params
     */
    public function __call($method, $params)
    {
        $method = strtoupper($method);

        if (($method != 'ANY') && ! in_array($method, static::$methods)) {
            throw new \Exception('Invalid method: ' .$method);
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

        if ( ! empty($this->groupStack)) {
            $prepended = $this->prependGroupUses($controller);
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
        $route = new Route($methods, $pattern, $action);

        // Add the current Route instance to the known Routes list.
        $this->routes->add($route);
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
        $d = $this->getControllerDispatcher();

        return function() use ($d, $controller)
        {
            $route = $this->getMatchedRoute();

            $request = $this->getCurrentRequest();

            //
            list($class, $method) = explode('@', $controller);

            return $d->dispatch($route, $request, $class, $method);
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
     * Dispatch the request to a route and return the response.
     *
     * @param  \Nova\Http\Request  $request
     * @return mixed
     */
    public function dispatchToRoute(Request $request)
    {
        // If there exists a Catch-All Route, firstly we add it to Routes list.
        if ($this->defaultRoute !== null) {
            $this->routes->add($this->defaultRoute);
        }

        // Execute the Routes matching.
        $route = $this->findRoute($request);

        $this->events->fire('router.matched', array($route, $request));

        // Apply the (specified) Filters on matched Route.
        $response = $this->applyFiltersToRoute($route);

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
        return $this->matchedRoute = $this->routes->match($request);
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
     * Create a response instance from the given value.
     *
     * @param  \Symfony\Component\HttpFoundation\Request  $request
     * @param  mixed  $response
     * @return \Nova\Http\Response
     */
    protected function prepareResponse($request, $response)
    {
        if (! $response instanceof SymfonyResponse) {
            $response = new Response($response);
        }

        return $response->prepare($request);
    }

    /**
     * Get the request currently being dispatched.
     *
     * @return \Nova\Http\Request
     */
    public function getCurrentRequest()
    {
        return $this->currentRequest;
    }
}
