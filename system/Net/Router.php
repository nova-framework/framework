<?php
/**
 * Router - routing urls to closures and controllers.
 *
 * @author Virgil-Adrian Teaca - virgil@@giulianaeassociati.com
 * @version 3.0
 * @date December 11th, 2015
 */

namespace Nova\Net;

use Nova\Helpers\Inflector;
use Nova\Net\Request;
use Nova\Net\Response;
use Nova\Net\Route;
use Nova\Net\Url;

/**
 * Router class will load requested controller / closure based on url.
 */
class Router
{
    private static $instance;

    private static $routeGroup = '';

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
    private $errorCallback = '\App\Controllers\Error@error404';


    // Constructor
    public function __construct()
    {
        self::$instance =& $this;
    }

    public static function &getInstance()
    {
        $appRouter = APPROUTER;

        if (! self::$instance) {
            $router = new $appRouter();
        }
        else {
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
     * Defines a Route Group.
     *
     * @param string $group The scope of the current Routes Group
     * @param callback $callback Callback object called to define the Routes.
     */
    public static function group($group, $callback)
    {
        // Set the current Routes Group
        self::$routeGroup = trim($group, '/');

        // Call the Callback, to define the Routes on the current Group.
        call_user_func($callback);

        // Reset the Routes Group to default (none).
        self::$routeGroup = '';
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
        $pattern = ltrim(self::$routeGroup.'/'.$route, '/');

        $route = new Route($method, $pattern, $callback);

        array_push($this->routes, $route);
    }

    /**
     * Invoke the Controller's Method with its associated parameters.
     *
     * @param  string $controller to be instantiated
     * @param  string $method method to be invoked
     * @param  array  $params parameters passed to method
     */
    protected function invokeController($className, $method, $params)
    {
        // Controller's Methods starting with '_' and the Flight ones cannot be called via Router.
        switch($method) {
            case 'beforeFlight':
            case 'afterFlight':
                return false;

            default:
                if ($method[0] === '_') {
                    return false;
                }

                break;
        }

        // Check first if the Controller exists.
        if (!class_exists($className)) {
            return false;
        }

        // Initialize the Controller.
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
     * @param  object $callback
     * @param  array  $params array of matched parameters
     * @param  string $message
     */
    protected function invokeObject($callback, $params = array())
    {
        if (is_object($callback)) {
            // Call the Closure.
            call_user_func_array($callback, $params);

            return true;
        }

        // Call the object Controller and its Method.
        $segments = explode('@', $callback);

        $controller = $segments[0];
        $method     = $segments[1];

        // Invoke the Controller's Method with the given arguments.
        return $this->invokeController($controller, $method, $params);
    }

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

    protected function dispatchFile($uri)
    {
        // For properly Assets serving, the file URI should be as following:
        //
        // /templates/default/assets/css/style.css
        // /modules/blog/assets/css/style.css
        // /assets/css/style.css

        $filePath = '';

        if(preg_match('#^assets/(.*)$#i', $uri, $matches)) {
            $filePath = BASEPATH.'assets'.DS.$matches[1];
        }
        else if (preg_match('#^(templates|modules)/(.+)/assets/(.*)$#i', $uri, $matches)) {
            // We need to classify the path name (the Module/Template path).
            $basePath = Inflector::classify($matches[1].DS.$matches[2]);

            $filePath = APPPATH.$basePath.DS.'Assets'.DS.$matches[3];
        }

        if(! empty($filePath)) {
            // Serve the specified Asset File.
            Response::serveFile($filePath);

            return true;
        }

        return false;
    }

}
