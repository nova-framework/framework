<?php
/**
 * Controller - base controller
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace App\Core;

use Nova\Http\Response;
use Nova\Routing\Controller;
use Nova\Support\Contracts\RenderableInterface as Renderable;
use Nova\Support\Facades\Config;
use Nova\Support\Facades\Module;
use Nova\Support\Facades\View as ViewFactory;
use Nova\View\Layout;
use Nova\View\View;

use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

use BadMethodCallException;


abstract class BaseController extends Controller
{

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

        // Check for a valid controller on Application.
        if (preg_match('#^App/Controllers/(.*)$#i', $path, $matches)) {
            $view = $matches[1] .'/' .ucfirst($method);

            return ViewFactory::make($view, $data);
        }

        // Retrieve the Modules namespace from their configuration.
        $namespace = Config::get('modules.namespace', '');

        if (! empty($namespace)) {
            // Transform the Modules namespace on a path like variable.
            $basePath = str_replace('\\', '/', rtrim($namespace, '\\')) .'/';
        } else {
            $basePath = '';
        }

        // Check for a valid controller on Modules.
        if (preg_match('#^'. $basePath .'(.+)/Controllers/(.*)$#i', $path, $matches)) {
            $module = $matches[1];

            $view = $matches[2] .'/' .ucfirst($method);

            return ViewFactory::make($view, $data, $module);
        }

        // If we arrived there, the called class is not a Controller; go Exception.
        throw new BadMethodCallException('Invalid Controller namespace: ' .static::class);
    }

}
