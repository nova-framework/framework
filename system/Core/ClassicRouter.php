<?php
/**
 * ClassicRoute - manage, in classic style, a route to an HTTP request and an assigned callback function.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Core;

use Helpers\Inflector;
use Core\Route;
use Core\Router;
use Helpers\Request;
use Helpers\Url;

/**
 * Router class will load requested controller / closure based on url.
 */
class ClassicRouter extends Router
{
    /**
     * ClassicRouter constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Dispatch
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

        // Search the defined Routes for matches; invoke the associated Callback, if any.
        foreach ($this->routes as $route) {
            if ($route->match($uri, $method, false)) {
                // Found a valid Route; process it.
                $this->matchedRoute = $route;

                $callback = $route->callback();

                if (is_object($callback)) {
                    // Invoke the Route's Callback with the associated parameters.
                    call_user_func_array($callback, $route->params());

                    return true;
                }

                // Pattern based Route.
                $regex = $route->regex();

                // Prepare the URI used by autoDispatch, applying the REGEX if exists.
                if (! empty($regex)) {
                    $uri = preg_replace('#^' .$regex .'$#', $callback, $uri);
                } else {
                    $uri = $callback;
                }

                break;
            }
        }

        // Auto-dispatch the processed URI; quit if the attempt finished successfully.
        if ($this->autoDispatch($uri)) {
            return true;
        }

        // The dispatching failed; invoke the Error Callback with the current URI as parameter.
        $params = array(
            htmlspecialchars($uri, ENT_COMPAT, 'ISO-8859-1', true)
        );

        $this->invokeObject($this->callback(), $params);

        return false;
    }

    /**
     * Ability to call controllers in their module/directory/controller/method/param way.
     *
     * NOTE: This Auto-Dispatch routing use the styles:
     *      <DIR><directory><controller><method><params>
     *      <DIR><module><directory><controller><method><params>
     *
     * @param $uri
     * @return bool
     */
    public function autoDispatch($uri)
    {
        // Explode the URI in its parts.
        $parts = explode('/', trim($uri, '/'));

        // Loop through URI parts, checking for the Controller file including its path.
        $controller = '';

        if (! empty($parts)) {
            // Classify, to permit: '<DIR>/file_manager/admin/' -> '<APPDIR>/Modules/FileManager/Admin/
            $controller = Inflector::classify(array_shift($parts));
        }

        // Verify if the first URI part match a Module.
        $testPath = APPDIR.'Modules'.DS.$controller;

        if (! empty($controller) && is_dir($testPath)) {
            // Walking in a Module path.
            $moduleName = $controller;
            $basePath   = 'Modules/'.$controller.'/Controllers/';

            // Go further only if have other URI Parts, to permit URL mappings like:
            // '<DIR>/clients' -> '<APPDIR>/app/Modules/Clients/Controllers/Clients.php'
            if (! empty($parts)) {
                $controller = Inflector::classify(array_shift($parts));
            }
        } else {
            $moduleName = '';
            $basePath   = 'Controllers/';
        }

        // Check for the Controller, even in sub-directories.
        $directory = '';

        while (! empty($parts)) {
            $testPath = APPDIR.str_replace('/', DS, $basePath.$directory.$controller);

            if (! is_readable($testPath .'.php') && is_dir($testPath)) {
                $directory .= $controller .DS;

                $controller = Inflector::classify(array_shift($parts));

                continue;
            }

            break;
        }

        // Get the normalized Controller
        $defaultOne = !empty($moduleName) ? $moduleName : DEFAULT_CONTROLLER;

        $controller = !empty($controller) ? $controller : $defaultOne;

        // Get the normalized Method
        $method = !empty($parts) ? array_shift($parts) : DEFAULT_METHOD;

        // Prepare the Controller's class name.
        $controller = str_replace(array('//', '/'), '\\', 'App/'.$basePath.$directory.$controller);

        // The Method shouldn't start with '_'; also check if the Controller's class exists.
        if (($method[0] !== '_') && class_exists($controller)) {
            // Get the parameters, if any.
            $params = ! empty($parts) ? $parts : array();

            // Invoke the Controller's Method with the given arguments.
            return $this->invokeController($controller, $method, $params);
        }

        return false;
    }
}
