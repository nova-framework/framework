<?php
/**
 * Router - routing urls to closures and controllers.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date December 11th, 2015
 */

namespace Nova\Net;

use Nova\Core\Controller;
use Nova\Core\View;
use Nova\Helpers\Inflector;
use Nova\Forensics\Console;
use Nova\Net\Request;
use Nova\Net\Response;
use Nova\Net\Route;
use Nova\Net\Url;
use Nova\Config;

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
    private $errorCallback = '\App\Controllers\Error@error404';

    /**
     * The Configuration options.
     */
    private $config;


    /**
     * Router constructor.
     *
     * @codeCoverageIgnore
     */
    public function __construct()
    {
        self::$instance =& $this;

        $this->config = Config::get('routing');
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
        $router = self::getInstance();

        $router->addRoute($method, $params[0], $params[1]);
    }

    /**
     * Return true if the Router is in Testing Mode.
     *
     * @return bool
     */
    public static function testing()
    {
        return static::$testing;
    }

    /**
     * Return the current detected URI.
     *
     * @return string
     */
    public static function currentUri()
    {
        return Url::detectUri();
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
        $router = self::getInstance();

        $router->callback($callback);
    }

    /**
     * Register catchAll route
     *
     * @param $callback
     */
    public static function catchAll($callback)
    {
        $router =& self::getInstance();

        $router->defaultRoute = new Route('any', '(:all)', $callback);
    }

    /**
     * Defines a multi-method Route Match.
     *
     * @param string|array $method HTTP method(s) to match
     * @param string $route URL pattern to match
     * @param callback $callback Callback object
     */
    public static function match($method, $route, $callback = null)
    {
        $router =& self::getInstance();

        $router->addRoute($method, $route, $callback);
    }

    /**
     * Defines a Route Group.
     *
     * @param string $group The scope of the current Routes Group
     * @param callback $callback Callback object called to define the Routes.
     */
    public static function group($group, $callback)
    {
        if(is_array($group)) {
            $prefix    = $group['prefix'];
            $before    = isset($group['filters']) ? $group['filters'] : '';
            $namespace = isset($group['namespace']) ? $group['namespace'] : '';
        } else {
            $prefix    = $group;
            $before    = '';
            $namespace = '';
        }

        // Add the current Route Group to the array.
        array_push(self::$routeGroups, array(
            'prefix'    => trim($prefix, '/'),
            'filters'    => $before,
            'namespace' => $namespace
        ));

        // Call the Callback, to define the Routes on the current Group.
        call_user_func($callback);

        // Removes the last Route Group from the array.
        array_pop(self::$routeGroups);
    }

    /* The Resourcefull Routes in the Laravel Style.

    Method     |  Path                 |  Action   |
    -----------|-----------------------|-----------|
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
        $router =& self::getInstance();

        $router->addRoute('get',                 $basePath,                 $controller .'@index');
        $router->addRoute('get',                 $basePath .'/create',      $controller .'@create');
        $router->addRoute('post',                $basePath,                 $controller .'@store');
        $router->addRoute('get',                 $basePath .'/(:any)',      $controller .'@show');
        $router->addRoute('get',                 $basePath .'/(:any)/edit', $controller .'@edit');
        $router->addRoute(array('put', 'patch'), $basePath .'/(:any)',      $controller .'@update');
        $router->addRoute('delete',              $basePath .'/(:any)',      $controller .'@delete');
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
     * @param string|array $method HTTP method(s) to match
     * @param string $route URL pattern to match
     * @param string|array|callable $options Callback object or options
     */
    public function addRoute($method, $route, $options = null)
    {
        $methods = array_map('strtoupper', is_array($method) ? $method : array($method));

        $pattern = ltrim($route, '/');

        // If there is an options array, extract the filters and callback.
        if(is_array($options)) {
            $filters = isset($options['filters']) ? trim($options['filters'], '|') : '';

            $callback = isset($options['uses']) ? $options['uses'] : null;
        } else {
            $filters = '';
            $callback = $options;
        }

        if (! empty(self::$routeGroups)) {
            $parts     = array();
            $namespace = '';

            foreach (self::$routeGroups as $group) {
                // Add the current prefix to the prefixes list.
                array_push($parts, trim($group['prefix'], '/'));

                $filter = trim($group['filters'], '|');

                // Append the Group Filters to the main list.
                if(! empty($filter)) {
                    $filters = $filters .'|' .$filter;
                }

                // Keep always the last namespace.
                $namespace = $group['namespace'];
            }

            if (! empty($pattern)) {
                array_push($parts, $pattern);
            }

            // Adjust the Route PATTERN.
            if (! empty($parts)) {
                $pattern = implode('/', $parts);
            }
        } else {
            $namespace = '';
        }

        // Adjust the Route CALLBACK, when it is not a Closure.
        if(! empty($namespace) && ! is_object($callback)) {
            $callback = sprintf('%s\%s', trim($namespace, '\\'),  trim($callback, '\\'));
        }

        $route = new Route($methods, $pattern, array('filters' => trim($filters, '|'), 'uses' => $callback));

        // Add the current Route instance to the known Routes list.
        array_push($this->routes, $route);
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
    protected function invokeController($className, $method, $params, $withResult = true)
    {
        // Controller's Methods starting with '_' and the Flight ones cannot be called via Router.
        if(($method == 'initialize') || ($method == 'filters') || ($method == 'after')) {
            return false;
        }

        // Initialize the Controller.
        /** @var Controller $controller */
        $controller = new $className();

        // The called Method should be defined in the called Controller, not in one of its parents.
        if (! in_array(strtolower($method), array_map('strtolower', get_class_methods($controller)))) {
            return false;
        }

        $controller->initialize($className, $method, $params);

        // Start the Flight and return the result.
        return $controller->execute();
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
             $result = call_user_func_array($callback, $params);

             if ($result instanceof View) {
                 // If the object invocation returned a View instance, render it.
                 $result->display();
             }

             return true;
        }

        // Call the object Controller and its Method.
        $segments = explode('@', $callback);

        $controller = $segments[0];
        $method     = $segments[1];

        // The Method shouldn't start with '_'; also check if the Controller's class exists.
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
        $patterns = $this->config('patterns');

        // Detect the current URI.
        $uri = static::currentUri();

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
            if ($route->match($uri, $method, $patterns)) {
                // Found a valid Route; process it.
                $this->matchedRoute = $route;

                // Apply the (specified) Filters on matched Route.
                $result = $route->applyFilters();

                if($result === false) {
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
            $filePath = BASEPATH.'assets'.DS.$matches[1];
        } else if (preg_match('#^(templates|modules)/(.+)/assets/(.*)$#i', $uri, $matches)) {
            // We need to classify the path name (the Module/Template path).
            $basePath = ucfirst($matches[1]) .DS .Inflector::classify($matches[2]);

            $filePath = APPPATH.$basePath.DS.'Assets'.DS.$matches[3];
        }

        if (! empty($filePath)) {
            // Serve the specified Asset File.
            Response::serveFile($filePath);

            return true;
        }

        return false;
    }

    protected function config($key = null)
    {
        if ($key !== null) {
            return array_key_exists($key, $this->config) ? $this->config[$key] : null;
        }

        return $this->config;
    }
}
