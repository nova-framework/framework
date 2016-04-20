<?php
/**
 * Router - routing urls to closures and controllers.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Core;

use Core\Controller;
use Helpers\Inflector;
use Helpers\Request;
use Helpers\Response;
use Core\Route;
use Helpers\Url;

/**
 * Router class will load requested controller / closure based on url.
 */
class Router
{
    private static $instance;

    private static $routeGroups = array();

    /**
     * Array of routes
     *
     * @var Route[] $routes
     */
    protected $routes = array();

    /**
     * Default Route, usualy the Catch-All one.
     */
    private $defaultRoute = null;

    /**
     * Matched Route, the current found Route, if any.
     */
    protected $matchedRoute = null;

    /**
     * Set an Error Callback
     *
     * @var null $errorCallback
     */
    private $errorCallback = '\App\Controllers\Error@index';

    /**
     * An array of HTTP request Methods.
     *
     * @var array
     */
    public static $methods = array('GET', 'POST', 'PUT', 'DELETE', 'HEAD', 'OPTIONS');

    /**
     * Router constructor.
     *
     * @codeCoverageIgnore
     */
    public function __construct()
    {
        self::$instance =& $this;
    }

    public static function &getInstance()
    {
        $appRouter = APPROUTER;

        if (! self::$instance) {
            $router = new $appRouter();
        } else {
            $router =& self::$instance;
        }

        return $router;
    }

    /**
     * Defines a route with or without Callback and Method.
     *
     * @param string $method
     * @param array @params
     */
    public static function __callStatic($method, $params)
    {
        $method = strtoupper($method);

        if (($method == 'ANY') || in_array($method, static::$methods)) {
            $route    = array_shift($params);
            $callback = array_shift($params);

            // Register the route.
            static::register($method, $route, $callback);
        }
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
     * Defines callback if route is not found.
     *
     * @param string $callback
     */
    public static function error($callback)
    {
        // Get the Router instance.
        $router = self::getInstance();

        //
        $router->callback($callback);
    }

    /**
     * Register catchAll route
     * @param $callback
     */
    public static function catchAll($callback)
    {
        // Get the Router instance.
        $router =& self::getInstance();

        //
        $router->defaultRoute = new Route('any', '(:all)', $callback);
    }

    /**
     * Defines a multi-method Route Match.
     *
     * @param array|string $method The Route's method(s).
     * @param string $route The Route definition.
     * @param callback $callback Callback object called to define the Routes.
     */
    public static function match($method, $route, $callback = null)
    {
        self::register($method, $route, $callback);
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
    public static function share($routes, $callback)
    {
        foreach ($routes as $entry) {
            $method = array_shift($entry);
            $route  = array_shift($entry);

            // Register the route.
            static::register($method, $route, $callback);
        }
    }

    /**
     * Defines a Route Group.
     *
     * @param string $group The scope of the current Routes Group
     * @param callback $callback Callback object called to define the Routes.
     */
    public static function group($group, $callback)
    {
        if (is_array($group)) {
            $prefix    = trim($group['prefix'], '/');
            $namespace = isset($group['namespace']) ? trim($group['namespace'], '\\') : '';
        } else {
            $prefix    = trim($group, '/');
            $namespace = '';
        }

        // Add the Route Group to the array.
        array_push(self::$routeGroups, array('prefix' => $prefix, 'namespace' => $namespace));

        // Call the Callback, to define the Routes on the current Group.
        call_user_func($callback);

        // Removes the last Route Group from the array.
        array_pop(self::$routeGroups);
    }

    /* The Resourcefull Routes in the Laravel Style.

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
     * Defines a Resourcefull Routes Group to a target Controller.
     *
     * @param string $basePath The base path of the resourcefull routes group
     * @param string $controller The target Resourcefull Controller's name.
     */
    public static function resource($basePath, $controller)
    {
        self::register('get',                 $basePath,                 $controller .'@index');
        self::register('get',                 $basePath .'/create',      $controller .'@create');
        self::register('post',                $basePath,                 $controller .'@store');
        self::register('get',                 $basePath .'/(:any)',      $controller .'@show');
        self::register('get',                 $basePath .'/(:any)/edit', $controller .'@edit');
        self::register(array('put', 'patch'), $basePath .'/(:any)',      $controller .'@update');
        self::register('delete',              $basePath .'/(:any)',      $controller .'@delete');
    }

    /**
     * Router Error Callback
     *
     * @param null $callback
     * @return callback|null
     */
    public function callback($callback = null)
    {
        if (is_null($callback)) {
            return $this->errorCallback;
        }

        $this->errorCallback = $callback;

        return null;
    }

    /**
     * Maps a Method and URL pattern to a Callback.
     *
     * @param string $method HTTP metod(s) to match
     * @param string $route URL pattern to match
     * @param callback $callback Callback object
     */
    public static function register($method, $route, $callback = null)
    {
        // Get the Router instance.
        $router =& self::getInstance();

        // Prepare the route Methods.
        if (is_string($method) && (strtolower($method) == 'any')) {
            $methods = static::$methods;
        } else {
            $methods = array_map('strtoupper', is_array($method) ? $method : array($method));

            // Ensure the requested Methods being valid ones.
            $methods = array_intersect($methods, static::$methods);
        }

        if (empty($methods)) {
            // If there are no valid Methods defined, fallback to ANY.
            $methods = static::$methods;
        }

        // Prepare the Route PATTERN.
        $pattern = ltrim($route, '/');

        // If $callback is an options array, extract the Filters and Callback.
        if (is_array($callback)) {
            $filters = isset($callback['filters']) ? trim($callback['filters'], '|') : '';

            $callback = isset($callback['uses']) ? $callback['uses'] : null;
        } else {
            $filters = '';
        }

        if (! empty(self::$routeGroups)) {
            $parts = array();

            // The current Controller namespace; prepended to Callback if it is not a Closure.
            $namespace = '';

            foreach (self::$routeGroups as $group) {
                // Add the current prefix to the prefixes list.
                array_push($parts, trim($group['prefix'], '/'));

                // Update always to the last Controller namespace.
                $namespace = trim($group['namespace'], '\\');
            }

            if (! empty($pattern)) {
                array_push($parts, $pattern);
            }

            // Adjust the Route PATTERN.
            if (! empty($parts)) {
                $pattern = implode('/', $parts);
            }

            // Adjust the Route CALLBACK, when it is not a Closure.
            if (! empty($namespace) && ! is_object($callback)) {
                $callback = sprintf('%s\%s', $namespace,  trim($callback, '\\'));
            }
        }

        // Create a Route instance using the processed information.
        $route = new Route($methods, $pattern, array('filters' => $filters, 'uses' => $callback));

        // Add the current Route instance to the known Routes list.
        array_push($router->routes, $route);
    }

    /**
     * Return the current Matched Route, if there is any.
     *
     * @return null|Route
     */
    public function matchedRoute()
    {
        return $this->matchedRoute;
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
            return false;
        }

        // Initialize the Controller.
        /** @var Controller $controller */
        $controller = new $className();

        // Obtain the available methods into requested Controller.
        $methods = array_map('strtolower', get_class_methods($controller));

        // The called Method should be defined right on the called Controller to be executed.
        if (in_array(strtolower($method), $methods)) {
            // Execute the Controller's Method with the given arguments.
            $controller->execute($method, $params);

            return true;
        }

        return false;
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
            call_user_func_array($callback, $params);

            return true;
        }

        // Call the object Controller and its Method.
        $segments = explode('@', $callback);

        $controller = $segments[0];
        $method     = $segments[1];

        // The Method shouldn't be called 'execute' or starting with '_'; also check if the Controller's class exists.
        if (($method[0] !== '_') && class_exists($controller)) {
            // Invoke the Controller's Method with the given arguments.
            return $this->invokeController($controller, $method, $params);
        }

        return false;
    }

    /**
     * Dispatch route
     * @return bool
     */
    public function dispatch()
    {
        // Detect the current URI.
        $uri = Url::detectUri();

        // First, we will supose that URI is associated with an Asset File.
        if (Request::isGet() && $this->dispatchFile($uri)) {
            return true;
        }

        // Not an Asset File URI? Routes the current request.
        $method = Request::getMethod();

        // If there exists a Catch-All Route, firstly we add it to Routes list.
        if ($this->defaultRoute !== null) {
            array_push($this->routes, $this->defaultRoute);
        }

        foreach ($this->routes as $route) {
            if ($route->match($uri, $method)) {
                // Found a valid Route; process it.
                $this->matchedRoute = $route;

                // Apply the (specified) Filters on matched Route.
                $result = $route->applyFilters();

                if ($result === false) {
                    // Matched Route filtering failed; we should go to (404) Error.
                    break;
                }

                $callback = $route->callback();

                if ($callback !== null) {
                    // Invoke the Route's Callback with the associated parameters.
                    return $this->invokeObject($callback, $route->params());
                }

                return true;
            }
        }

        // No valid Route found; invoke the Error Callback with the current URI as parameter.
        $params = array(
            htmlspecialchars($uri, ENT_COMPAT, 'ISO-8859-1', true)
        );

        $this->invokeObject($this->callback(), $params);

        return false;
    }

    protected function dispatchFile($uri)
    {
        // For properly Assets serving, the file URI should be as following:
        //
        // /templates/default/assets/css/style.css
        // /modules/blog/assets/css/style.css
        // /assets/css/style.css

        $filePath = '';

        if (preg_match('#^assets/(.*)$#i', $uri, $matches)) {
            $filePath = ROOTDIR.'assets'.DS.$matches[1];
        } else if (preg_match('#^(templates|modules)/(.+)/assets/(.*)$#i', $uri, $matches)) {
            // We need to classify the path name (the Module/Template path).
            $basePath = ucfirst($matches[1]) .DS .Inflector::classify($matches[2]);

            $filePath = APPDIR.$basePath.DS.'Assets'.DS.$matches[3];
        }

        if (! empty($filePath)) {
            // Serve the specified Asset File.
            Response::serveFile($filePath);

            return true;
        }

        return false;
    }
}
