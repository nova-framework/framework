<?php
/**
 * Router - routing urls to closures and controllers.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Routing;

use Core\Config;
use Core\BaseView as View;
use Core\Controller;
use Events\Dispatcher;

use Helpers\Inflector;
use Http\Request;
//use Http\Response;
use Routing\Route;
use Support\Facades\Facade;

use Illuminate\Container\Container;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesser;

use App;
use Console;
use Language;
use Response;


/**
 * Router class will load requested Controller / Closure based on URL.
 */
class Router
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
    private static $groupStack = array();

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
    public static $methods = array('GET', 'POST', 'PUT', 'DELETE', 'HEAD', 'OPTIONS');

    /**
     * Router constructor.
     *
     * @codeCoverageIgnore
     */
    protected function __construct(Dispatcher $events = null, Container $container = null)
    {
        $this->events = $events;

        $this->container = $container ?: new Container();
    }

    public static function getInstance()
    {
        $app = Facade::getFacadeApplication();

        if(! is_null($app)) {
            return $app['router'];
        }

        if (self::$instance === null) {
            self::$instance = new static();
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
        $router = self::getInstance();

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
    public static function getLanguage()
    {
        $instance = static::getInstance();

        $route = $instance->getMatchedRoute();

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

        $path = $request->path();

        // First, we will supose that URI is associated with an Asset File.
        if (($method == 'GET') && $this->dispatchFile($path)) {
            // Return a null value, to notify for no further processing.
            return null;
        }

        // If there exists a Catch-All Route, firstly we add it to Routes list.
        if ($this->defaultRoute !== null) {
            array_push($this->routes, $this->defaultRoute);
        }

        // Retrieve the additional Routing Patterns from configuration.
        $patterns = Config::get('routing.patterns', array());

        // Execute the Routes matching loop.
        foreach ($this->routes as $route) {
            if ($route->match($path, $method, true, $patterns)) {
                // Found a valid Route; process it.
                $this->matchedRoute = $route;

                // Apply the (specified) Filters on matched Route.
                $result = $route->applyFilters();

                if($result instanceof SymfonyResponse) {
                    return $result;
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
        $data = array('error' => htmlspecialchars($path, ENT_COMPAT, 'ISO-8859-1', true));

        return Response::error(404, $data);
    }

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
        } else if (preg_match('#^(templates|modules)/([^/]+)/assets/([^/]+)/(.*)$#i', $uri, $matches)) {
            $module = Inflector::classify($matches[2]);

            $folder = $matches[3];

            $path = $matches[4];

            if($matches[1] == 'Modules') {
                // A Module Asset file.
                $filePath = $this->getModuleAssetPath($module, $folder, $path);
            } else {
                // A Template Asset file.
                $filePath = $this->getTemplateAssetPath($module, $folder, $path);
            }
        }

        if (! empty($filePath)) {
            // Serve the specified Asset File.
            static::serveFile($filePath);

            return true;
        }

        return false;
    }

    /**
     * Get the path of a Asset file
     * @return string|null
     */
    protected function getModuleAssetPath($module, $folder, $path)
    {
        $basePath = APPDIR .str_replace('/', DS, "Modules/$module/Assets/");

        return $basePath .$folder .DS .$path;
    }

    /**
     * Get the path of a Asset file
     * @return string|null
     */
    protected function getTemplateAssetPath($template, $folder, $path)
    {
        $path = str_replace('/', DS, $path);

        // Retrieve the Template Info
        $infoFile = APPDIR .'Templates' .DS .$template .DS .'template.json';

        if (is_readable($infoFile)) {
            $info = json_decode(file_get_contents($infoFile), true);

            // Template Info should be always an array; ensure that.
            $info = $info ?: array();
        } else {
            $info = array();
        }

        //
        $basePath = null;

        // Get the current Asset Folder's Mode.
        $mode = array_get($info, 'assets.paths.' .$folder, 'local');

        if ($mode == 'local') {
            $basePath = APPDIR .str_replace('/', DS, "Templates/$template/Assets/");
        } else if ($mode == 'vendor') {
            // Get the Vendor name.
            $vendor = array_get($info, 'assets.vendor', '');

            if (! empty($vendor)) {
                $basePath = ROOTDIR .str_replace('/', DS, "vendor/$vendor/");
            }
        }

        return ! empty($basePath) ? $basePath .$folder .DS .$path : '';
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
        $guesser = MimeTypeGuesser::getInstance();

        // Even the Symfony's HTTP Foundation have troubles with the CSS and JS files?
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
                $contentType = $guesser->guess($filePath);
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
