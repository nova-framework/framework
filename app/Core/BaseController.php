<?php
/**
 * Controller - base controller
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace App\Core;

use Nova\Foundation\Auth\Access\AuthorizesRequestsTrait;
use Nova\Foundation\Bus\DispatchesJobsTrait;
use Nova\Foundation\Validation\ValidatesRequestsTrait;
use Nova\Routing\Controller;
use Nova\Support\Facades\View;

use BadMethodCallException;


abstract class BaseController extends Controller
{
    use DispatchesJobsTrait, AuthorizesRequestsTrait, ValidatesRequestsTrait;


    /**
     * Return a default View instance.
     *
     * @return \Nova\View\View
     * @throws \BadMethodCallException
     */
    protected function getView(array $data = array())
    {
        // Get the currently called method.
        $method = $this->getMethod();

         // Transform the complete class name on a path like variable.
        $path = str_replace('\\', '/', static::class);

        // Check for a valid controller on App and Modules.
        if (preg_match('#^(.+)/Http/Controllers/(.*)$#i', $path, $matches)) {
            $view = $matches[2] .'/' .ucfirst($method);

            if ($matches[1] == 'App') {
                return View::make($view, $data);
            }

            $segments = explode('/', $matches[1]);

            if (count($segments) === 2) {
                return View::make($view, $data, last($segments));
            }
        }

        throw new BadMethodCallException('Invalid Controller namespace: ' .static::class);
    }

}
