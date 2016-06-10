<?php
/**
 * Router - routing urls to closures and controllers.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Routing;

use Core\Config;
use Helpers\Inflector;
use Helpers\Url;
use Routing\BaseRouter;
use Routing\Route;

use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

use App;
use Response;
use Request;


/**
 * Router class will load requested controller / closure based on url.
 */
class Router extends BaseRouter
{
    /**
     * Array of Route Groups
     *
     * @var array $groupStack
     */
    private static $groupStack = array();

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
    public function __construct()
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
        if (! is_array($group)) {
            $group = array('prefix' => $group);
        }

        // Add the Route Group to the array.
        array_push(static::$groupStack, $group);

        // Call the Callback, to define the Routes on the current Group.
        call_user_func($callback);

        // Removes the last Route Group from the array.
        array_pop(static::$groupStack);
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
     * @param callback $action Callback object
     */
    protected static function register($method, $route, $action = null)
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

        // Pre-process the Action information.
        if (! is_array($action)) {
            $action = array('uses' => $action);
        }

        if (! empty(static::$groupStack)) {
            $parts = array();

            $namespace = null;

            foreach (static::$groupStack as $group) {
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
        array_push($router->routes, $route);
    }

    /**
     * Dispatch route.
     *
     * @return bool
     */
    public function dispatch()
    {
        // Retrieve the additional Routing Patterns from configuration.
        $patterns = Config::get('routing.patterns', array());

        // Detect the current URI.
        $uri = Url::detectUri();

        // First, we will supose that URI is associated with an Asset File.
        if ((Request::method() == 'GET') && $this->dispatchFile($uri)) {
            return true;
        }

        // Not an Asset File URI? Route the current request.
        $method = Request::method();

        // If there exists a Catch-All Route, firstly we add it to Routes list.
        if ($this->defaultRoute !== null) {
            array_push($this->routes, $this->defaultRoute);
        }

        foreach ($this->routes as $route) {
            if ($route->match($uri, $method, true, $patterns)) {
                // Found a valid Route; process it.
                $this->matchedRoute = $route;

                // Apply the (specified) Filters on matched Route.
                $result = $route->applyFilters();

                if($result instanceof SymfonyResponse) {
                    // Finish the Session and send the Response.
                    App::finish($result);

                    return true;
                }

                // Get the matched Route callback.
                $callback = $route->getCallback();

                if ($callback !== null) {
                    // Invoke the Route's Callback with the associated parameters.
                    return $this->invokeObject($callback, $route->getParams());
                }

                return true;
            }
        }

        // No valid Route found; send an Error 404 Response.
        $data = array('error' => htmlspecialchars($uri, ENT_COMPAT, 'ISO-8859-1', true));

        $response = Response::error(404, $data);

        // Finish the Session and send the Response.
        App::finish($response);

        return false;
    }
}
