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
    private $errorCallback = '\Core\Error@index';


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

        if (preg_match('#^(templates|modules)/(.+)/assets/(.*)$#i', $uri, $matches)) {
            // We need to classify the path name (the Module/Template path).
            $pathName = str_replace(array('-', '_'), '', ucwords($matches[1].'/'.$matches[2], '-_'));

            $filePath = realpath(APP.$pathName.'/Assets/'.$matches[3]);

            // Serve the specified Asset File.
            $this->serveFile($filePath);

            return true;
        }

        return false;
    }

    protected function serveFile($filePath)
    {
        $httpProtocol = $_SERVER['SERVER_PROTOCOL'];

        $expires = 60 * 60 * 24 * 365; // Cache for one year

        if (! file_exists($filePath)) {
            header("$httpProtocol 404 Not Found");

            return false;
        }
        else if (! is_readable($filePath)) {
            header("$httpProtocol 403 Forbidden");

            return false;
        }
        //
        // Collect the current file information.

        $finfo = finfo_open(FILEINFO_MIME_TYPE); // Return mime type ala mimetype extension

        $contentType = finfo_file($finfo, $filePath);

        finfo_close($finfo);

        // There is a bug with finfo_file();
        // https://bugs.php.net/bug.php?id=53035
        //
        // Hard coding the correct mime types for presently needed file extensions
        switch($fileExt = pathinfo($filePath, PATHINFO_EXTENSION)) {
            case 'css':
                $contentType = 'text/css';
                break;
            case 'js':
                $contentType = 'application/javascript';
                break;
            default:
                break;
        }

        //
        // Prepare and send the headers with browser-side caching support.

        // Firstly, we finalize the output buffering.
        if (ob_get_level()) ob_end_clean();

        header("$httpProtocol 200 OK");
        header('Access-Control-Allow-Origin: *');
        header('Content-type: ' .$contentType);
        header('Content-Length: ' .filesize($filePath));

        // Send the current file content.
        readfile($filePath);

        return true;
    }

}
