<?php
/**
 * Router - routing urls to closures and controllers.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Core;

use Core\Base\Router as BaseRouter;
use Core\Response;
use Core\Route;
use Helpers\Inflector;
use Helpers\Request;
use Helpers\Url;

/**
 * Router class will load requested controller / closure based on url.
 */
class Router extends BaseRouter
{
    /**
     * Array of Route Groups
     *
     * @var array $routeGroups
     */
    private static $routeGroups = array();

    /**
     * Default Route, usually the Catch-All one.
     *
     * @var Route $defaultRoute
     */
    private $defaultRoute = null;


    /**
     * Router constructor.
     *
     * @codeCoverageIgnore
     */
    protected function __construct()
    {
        parent::__construct();
    }

    /**
     * Register catchAll route.
     *
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
     * Maps a Method and URL pattern to a Callback.
     *
     * @param string $method HTTP metod(s) to match
     * @param string $route URL pattern to match
     * @param callback $callback Callback object
     */
    protected static function register($method, $route, $callback = null)
    {
        // Get the Router instance.
        $router =& self::getInstance();

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
                // Add the current prefix to the prefix list.
                array_push($parts, trim($group['prefix'], '/'));

                // Always update to the last Controller namespace.
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
     * Dispatch route.
     *
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

        // Not an Asset File URI? Route the current request.
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

                if($result instanceof Response) {
                    // The Filters returned a Response instance; send it and quit processing.
                    $result->send();

                    return true;
                }

                // Get the matched Route callback.
                $callback = $route->callback();

                if ($callback !== null) {
                    // Invoke the Route's Callback with the associated parameters.
                    return $this->invokeObject($callback, $route->params());
                }

                return true;
            }
        }

        // No valid Route found; send an Error 404 Response.
        $data = array('error' => htmlspecialchars($uri, ENT_COMPAT, 'ISO-8859-1', true));

        Response::error('404', $data)->send();

        return false;
    }
}
