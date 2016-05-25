<?php
/**
 * BaseRouter - routing urls to closures and controllers.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Routing;

use Core\BaseView as View;
use Core\Controller;
use Helpers\Inflector;
use Routing\Route;

use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

use Response;
use Session;


/**
 * Router class will load requested Controller / Closure based on URL.
 */
abstract class BaseRouter
{
    /**
     * The Router instance.
     *
     * @var Router $instance
     */
    private static $instance;

    /**
     * Array of routes
     *
     * @var Route[] $routes
     */
    protected $routes = array();

    /**
     * Matched Route, the current found Route, if any.
     *
     * @var Route|null $matchedRoute
     */
    protected $matchedRoute = null;

    /**
     * An array of HTTP request Methods.
     *
     * @var array $methods
     */
    public static $methods = array('GET', 'POST', 'PUT', 'DELETE', 'HEAD', 'OPTIONS');

    /**
     * Router constructor.
     *
     * @codeCoverageIgnore
     */
    protected function __construct()
    {
    }

    public static function &getInstance()
    {
        if (self::$instance === null) {
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
        static::register($method, $route, $callback);
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
    public static function match($method, $route, $callback = null)
    {
        self::register($method, $route, $callback);
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
        // Nothing to do there.
    }

    /**
     * Return the current Matched Route, if there are any.
     *
     * @return null|Route
     */
    public function matchedRoute()
    {
        return $this->matchedRoute;
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
            // Finsih the Session Store.
            Session::finish($result);

            // Send the Response.
            $result->send();
        }  else if($result instanceof View) {
            // Create a Response instance.
            $response = Response::make($result);

            // Finish the Session Store.
            Session::finish($response);

            // Send the Response.
            $response->send();
        }

        return true;
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

        // Obtain the available methods into the requested Controller.
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
            return $this->invokeCallback($callback, $params);
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
    abstract public function dispatch();

    /**
     * Dispatch/Serve a file
     * @return bool
     */
    protected function dispatchFile($uri)
    {
        // For proper Assets serving, the file URI should be either of the following:
        //
        // /templates/default/assets/css/style.css
        // /modules/blog/assets/css/style.css
        // /assets/css/style.css

        $filePath = '';

        if (preg_match('#^assets/(.*)$#i', $uri, $matches)) {
            $filePath = ROOTDIR .'assets' .DS .$matches[1];
        } else if (preg_match('#^(templates|modules)/(.+)/assets/(.*)$#i', $uri, $matches)) {
            // We need to classify the path name (the Module/Template path).
            $basePath = ucfirst($matches[1]) .DS .Inflector::classify($matches[2]);

            $filePath = APPDIR .$basePath .DS .'Assets' .DS .$matches[3];
        }

        if (! empty($filePath)) {
            // Serve the specified Asset File.
            static::serveFile($filePath);

            return true;
        }

        return false;
    }

    /**
     * Serve a File.
     *
     * @param string $filePath
     * @return bool
     */
    public static function serveFile($filePath)
    {
        $httpProtocol = $_SERVER['SERVER_PROTOCOL'];

        $expires = 60 * 60 * 24 * 365; // Cache for one year

        if (! file_exists($filePath)) {
            header("$httpProtocol 404 Not Found");

            return false;
        } else if (! is_readable($filePath)) {
            header("$httpProtocol 403 Forbidden");

            return false;
        }

        // Collect the current file information.

        $finfo = \finfo_open(FILEINFO_MIME_TYPE); // Return mime type a la mimetype extension

        $contentType = \finfo_file($finfo, $filePath);

        \finfo_close($finfo);

        // There is a bug with finfo_file();
        // https://bugs.php.net/bug.php?id=53035
        //
        // Hard coding the correct mime types for presently needed file extensions.
        switch ($fileExt = pathinfo($filePath, PATHINFO_EXTENSION)) {
            case 'css':
                $contentType = 'text/css';
                break;
            case 'js':
                $contentType = 'application/javascript';
                break;
            default:
                break;
        }

        // Prepare and send the headers with browser-side caching support.

        // Get the last-modified-date of this very file.
        $lastModified = filemtime($filePath);

        // Get the HTTP_IF_MODIFIED_SINCE header if set.
        $ifModifiedSince = isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ? $_SERVER['HTTP_IF_MODIFIED_SINCE'] : false;

        // Firstly, we finalize the output buffering.
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        header('Access-Control-Allow-Origin: *');
        header('Content-type: ' .$contentType);
        header('Expires: '.gmdate('D, d M Y H:i:s', time() + $expires).' GMT');
        header('Last-Modified: '.gmdate('D, d M Y H:i:s', $lastModified).' GMT');
        // header('Etag: '.$etagFile);
        header('Cache-Control: max-age='.$expires);

        // Check if the page has changed. If not, send 304 and exit.
        if (@strtotime($ifModifiedSince) == $lastModified) {
            header("$httpProtocol 304 Not Modified");

            return true;
        }

        // Send the current file.

        header("$httpProtocol 200 OK");
        header('Content-Length: ' .filesize($filePath));

        // Send the current file content.
        readfile($filePath);

        return true;
    }

}
