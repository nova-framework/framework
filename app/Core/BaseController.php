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

        // Check for a valid controller on App or Modules.
        if (preg_match('#^(.+)/Http/Controllers/(.*)$#i', $path, $matches)) {
            $view = $matches[2] .'/' .ucfirst($method);

            if ($matches[1] == 'App') {
               $module = null;
            } else if (count($segments = explode('/', $matches[1])) === 2) {
               $module = last($segments);
            } else {
                throw new BadMethodCallException('Invalid Controller namespace: ' .static::class);
            }

            return ViewFactory::make($view, $data, $module);
        }

        // If we arrived there, the called class is not a Controller; go Exception.
        throw new BadMethodCallException('Invalid Controller namespace: ' .static::class);
    }

}
