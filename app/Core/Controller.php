<?php
/**
 * Controller - base controller
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace App\Core;

use Nova\Http\Response;
use Nova\Routing\Controller as BaseController;
use Nova\Support\Contracts\RenderableInterface as Renderable;
use Nova\Support\Facades\Config;
use Nova\Support\Facades\View as ViewFactory;
use Nova\View\Layout;
use Nova\View\View;

use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

use BadMethodCallException;


abstract class Controller extends BaseController
{
    /**
     * The currently used Theme.
     *
     * @var string
     */
    protected $theme = null;

    /**
     * The currently used Layout.
     *
     * @var string
     */
    protected $layout = 'Default';


    /**
     * Create a new Controller instance.
     */
    public function __construct()
    {
        // Setup the used Theme to default, if it is not already defined.
        if (! isset($this->theme)) {
            $this->theme = Config::get('app.theme');
        }
    }

    /**
     * Create from the given result a Response instance and send it.
     *
     * @param mixed  $response
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function processResponse($response)
    {
        if ($response instanceof Renderable) {
            // If the response which is returned from the called Action is a Renderable instance,
            // we will assume we want to render it using the Controller's themed environment.

            if ((! $response instanceof Layout) && is_string($this->layout) && ! empty($this->layout)) {
                $response = ViewFactory::makeLayout($this->layout, $this->theme)
                    ->with('content', $response);
            }

            // Create a proper Response instance.
            $response = new Response($response->render(), 200, array('Content-Type' => 'text/html'));
        }

        // If the response is not a instance of Symfony Response, create a proper one.
        if (! $response instanceof SymfonyResponse) {
            $response = new Response($response);
        }

        return $response;
    }

    /**
     * Return a default View instance.
     *
     * @return \Nova\View\View
     * @throws \BadMethodCallException
     */
    protected function getView(array $data = array())
    {
        list(, $caller) = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);

        // Retrieve the called Controller method from the caller.
        $method = $caller['function'];

         // Transform the complete class name on a path like variable.
        $path = str_replace('\\', '/', static::class);

        // Check for a valid controller on Application.
        if (preg_match('#^App/Controllers/(.*)$#i', $path, $matches)) {
            $view = $matches[1] .'/' .ucfirst($method);

            return ViewFactory::make($view, $data, '', $this->theme);
        }

        // Retrieve the Modules namespace from their configuration.
        $namespace = Config::get('modules.namespace', 'App\Modules\\');

        // Transform the Modules namespace on a path like variable.
        $basePath = str_replace('\\', '/', rtrim($namespace, '\\'));

        // Check for a valid controller on Modules.
        if (preg_match('#^'. $basePath .'/(.+)/Controllers/(.*)$#i', $path, $matches)) {
            $module = $matches[1];

            $view = $matches[2] .'/' .ucfirst($method);

            return ViewFactory::make($view, $data, $module, $this->theme);
        }

        // If we arrived there, the called class is not a Controller; go Exception.
        throw new BadMethodCallException('Invalid Controller namespace: ' .static::class);
    }

    /**
     * Return the current Theme name.
     *
     * @return string
     */
    public function getTheme()
    {
        return $this->theme;
    }

    /**
     * Return a Layout instance.
     *
     * @param string|null $layout
     * @param array $data
     *
     * @return \Theme\Theme|\View\View
     * @throws \BadMethodCallException
     */
    public function getLayout($layout = null, array $data = array())
    {
        // Adjust the current used Layout.
        $layout = $layout ?: $this->layout;

        if ($layout instanceof View) {
            return $layout->with($data);
        } else if (is_string($layout)) {
            return LayoutFactory::make($layout, $data, $this->theme);
        }

        throw new BadMethodCallException('Method not available for the current Layout');
    }

    /**
     * Return the current Layout (class) name.
     *
     * @return string
     */
    public function getLayoutName()
    {
        if ($this->layout instanceof View) {
            return class_name($this->layout);
        } else if (is_string($this->layout)) {
            return $this->layout;
        }
    }

}
