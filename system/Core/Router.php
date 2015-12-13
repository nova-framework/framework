<?php
/**
 * Router - routing urls to closures and controllers.
 *
 * @author Virgil-Adrian Teaca - virgil@@giulianaeassociati.com
 * @version 3.0
 * @date December 11th, 2015
 */

namespace Core;

use Core\Route;
use Helpers\Request;
use Helpers\Url;

/**
 * Router class will load requested controller / closure based on url.
 */
class Router
{
    private static $instance;

    /**
     * Array of routes
     *
     * @var array $routes
     */
    protected $routes = array();

    /**
     * Set an Error Callback
     *
     * @var null $errorCallback
     */
    private $errorCallback = '\Core\Error@index';


    // Constructor
    public function __construct()
    {
        self::$instance =& $this;
    }

    public static function &getInstance()
    {
        if (! self::$instance) {
            $appRouter = APPROUTER;

            self::$instance = new $appRouter();
        }

        return self::$instance;
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
     * Defines callback if route is not found.
     *
     * @param string $callback
     */
    public static function error($callback)
    {
        $router = self::getInstance();

        $router->callback($callback);
    }

    public function callback($callback = null)
    {
        if (is_null($callback)) {
            return $this->errorCallback;
        }

        $this->errorCallback = $callback;
    }

    /**
     * Maps a Method and URL pattern to a Callback.
     *
     * @param string $method HTTP metod to match
     * @param string $pattern URL pattern to match
     * @param callback $callback Callback object
     */
    public function addRoute($method, $route, $callback)
    {
        $method = strtoupper($method);
        $pattern = ltrim($route, '/');

        $route = new Route($method, $pattern, $callback);

        array_push($this->routes, $route);
    }

    /**
     * Invoke the callback with its associated parameters.
     *
     * @param  object $callback
     * @param  array  $params array of matched parameters
     * @param  string $message
     */
    protected function invokeObject($callback, $params = array())
    {
        if (is_object($callback)) {
            // Call the Closure.
            return call_user_func_array($callback, $params);
        }

        // Call the object Controller and its Method.
        $segments = explode('@', $callback);

        $controller = $segments[0];
        $method     = $segments[1];

        // Initialize the Controller
        $controller = new $controller();

        // Execute the Controller's Method with the given arguments.
        return call_user_func_array(array($controller, $method), $params);
    }

    public function dispatch()
    {
        // Detect the URI and the HTTP Method.
        $uri = Url::detectUri();

        $method = Request::getMethod();

        foreach ($this->routes as $route) {
            if ($route->match($uri, $method)) {
                // Found a valid Route; invoke the Route's Callback and go out.
                $this->invokeObject($route->callback(), $route->params());

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
}
