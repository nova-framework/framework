<?php
/**
 * ClassicRoute - manage, in classic style, a route to an HTTP request and an assigned callback function.
 *
 * @author Virgil-Adrian Teaca - virgil@@giulianaeassociati.com
 * @version 3.0
 * @date December 11th, 2015
 */

namespace App\Core;

use Core\Route;
use Helpers\Request;
use Helpers\Url;

/**
 * Router class will load requested controller / closure based on url.
 */
class ClassicRouter extends \Core\Router
{
    // Constructor
    public function __construct()
    {
        parent::__construct();
    }

   /**
     * Ability to call controllers in their module/directory/controller/method/param way.
     */
    public static function autoDispatch($uri)
    {
        // NOTE: This Auto-Dispatch routing use the styles:
        //
        // <DIR><directory><controller><method><params>
        // <DIR><module><directory><controller><method><params>

        $parts = explode('/', trim($uri, '/'));

        // Loop through URI parts, checking for the Controller file including its path.
        $controller = '';

        if (! empty($parts)) {
            // Classify, to permit: '<DIR>/file_manager/admin/' -> '<SMVC>/Modules/FileManager/Admin/
            $controller = str_replace(array('-', '_'), '', ucwords(array_shift($parts), '-_'));
        }

        // Verify if the first URI part match a Module.
        $testPath = SMVC.'Modules/'.$controller;

        if (! empty($controller) && is_dir($testPath)) {
            // Walking in a Module path.
            $moduleName = $controller;
            $basePath   = 'Modules/'.$controller.'/Controllers/';

            // Go further only if have other URI Parts, to permit URL mappings like:
            // '<DIR>/clients' -> '<SMVC>/app/Modules/Clients/Controllers/Clients.php'
            if (! empty($parts)) {
                $controller = str_replace(array('-', '_'), '', ucwords(array_shift($parts), '-_'));
            }
        } else {
            $moduleName = '';
            $basePath   = 'Controllers/';
        }

        // Check for the Controller, even in sub-directories.
        $directory = '';

        while (! empty($parts)) {
            $testPath = SMVC.$basePath.$directory.$controller;

            if (! is_readable($testPath .'.php') && is_dir($testPath)) {
                $directory .= $controller .'/';
                $controller = str_replace(array('-', '_'), '', ucwords(array_shift($parts), '-_'));

                continue;
            }

            break;
        }

        // Get the normalized Controller
        $defaultOne = !empty($moduleName) ? $moduleName : DEFAULT_CONTROLLER;
        $controller = !empty($controller) ? $controller : $defaultOne;

        // Get the normalized Method
        $method = !empty($parts) ? array_shift($parts) : DEFAULT_METHOD;

        // Get the Controller's className.
        $controller = str_replace(array('//', '/'), '\\', 'App/'.$basePath.$directory.$controller);

        // Controller's Methods starting with '_' are not allowed also to be called on Router.
        if (($method[0] === '_') || !class_exists($controller)) {
            return false;
        }

        // Initialize the Controller
        $controller = new $controller();

        // Check for a valid public Controller's Method.
        if (! in_array(strtolower($method), array_map('strtolower', get_class_methods($controller)))) {
            return false;
        }

        // Execute the current Controller's Method with the given arguments.
        call_user_func_array(array($controller, $method), !empty($parts) ? $parts : array());

        return true;
    }

    public function dispatch()
    {
        // Detect the URI and the HTTP Method.
        $uri = Url::detectUri();

        $method = Request::getMethod();

        foreach ($this->routes as $route) {
            if ($route->match($uri, $method, false)) {
                // Found a valid Route; invoke the autoDispatch and go out.
                $callback = $route->callback();

                if (! is_object($callback)) {
                    $regex = $route->regex();

                    if (! empty($regex) && (strpos($route->pattern(), ':') !== false)) {
                        $autoUri = preg_replace('#^' .$regex .'$#', $callback, $uri);
                    } else {
                        $autoUri = $callback;
                    }

                    $this->autoDispatch($autoUri);
                } else {
                    $this->invokeObject($callback, $route->params());
                }

                return true;
            }
        }

        // We arrived there
        $routeFound = $this->autoDispatch($uri);

        if (!$routeFound) {
            // No valid Route found; invoke the Error Callback with the current URI as parameter.
            $params = array(
                htmlspecialchars($uri, ENT_COMPAT, 'ISO-8859-1', true)
            );

            $this->invokeObject($this->callback(), $params);

            return false;
        }

        return true;
    }
}
