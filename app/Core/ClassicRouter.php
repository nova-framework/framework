<?php
/**
 * ClassicRoute - manage, in classic style, a route to an HTTP request and an assigned callback function.
 *
 * @author Virgil-Adrian Teaca - virgil@@giulianaeassociati.com
 * @version 3.0
 * @date December 11th, 2015
 */

namespace App\Core;

use Nova\Helpers\Inflector;
use Nova\Net\Route;
use Nova\Net\Request;
use Nova\Net\Url;

/**
 * Router class will load requested controller / closure based on url.
 */
class ClassicRouter extends \Nova\Net\Router
{
    // Constructor
    public function __construct()
    {
        parent::__construct();
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

        // The URI used by autoDispatch is, by default, the incoming one.
        $autoUri = $uri;

        foreach ($this->routes as $route) {
            if ($route->match($uri, $method, false)) {
                // Found a valid Route; process its options.
                $callback = $route->callback();

                if (is_object($callback)) {
                    $this->invokeObject($callback, $route->params());

                    return true;
                }

                // Pattern based Route.
                $regex = $route->regex();

                // Wilcard Routes match any Route, while those pattern based should be processed.
                if (! empty($regex)) {
                    $autoUri = preg_replace('#^' .$regex .'$#', $callback, $uri);
                }
                else {
                    $autoUri = $callback;
                }

                break;
            }
        }

        // We arrived there
        $result = $this->autoDispatch($autoUri);

        if (!$result) {
            // No valid Route found; invoke the Error Callback with the current URI as parameter.
            $params = array(
                htmlspecialchars($uri, ENT_COMPAT, 'ISO-8859-1', true)
            );

            $this->invokeObject($this->callback(), $params);

            return false;
        }

        return true;
    }

   /**
     * Ability to call controllers in their module/directory/controller/method/param way.
     */
    public function autoDispatch($uri)
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
            $controller = Inflector::classify(array_shift($parts));
        }

        // Verify if the first URI part match a Module.
        $testPath = SMVC.'Modules'.DS.$controller;

        if (! empty($controller) && is_dir($testPath)) {
            // Walking in a Module path.
            $moduleName = $controller;
            $basePath   = 'Modules/'.$controller.'/Controllers/';

            // Go further only if have other URI Parts, to permit URL mappings like:
            // '<DIR>/clients' -> '<SMVC>/app/Modules/Clients/Controllers/Clients.php'
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
            $testPath = SMVC.str_replace('/', DS, $basePath.$directory.$controller);

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

        // Get the Controller's class name.
        $controller = str_replace(array('//', '/'), '\\', 'App/'.$basePath.$directory.$controller);

        // Get the parameters, if any.
        $params = !empty($parts) ? $parts : array();

        // Invoke the Controller's Method with the given arguments.
        return $this->invokeController($controller, $method, $params);
    }
}
