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
     * The currently used Template.
     *
     * @var string
     */
    protected $template = null;

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
        // Setup the used Template to default, if it is not already defined.
        if (! isset($this->template)) {
            $this->template = Config::get('app.template');
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
            // we will assume we want to render it using the Controller's templated environment.

            if ((! $response instanceof Layout) && is_string($this->layout) && ! empty($this->layout)) {
                $response = ViewFactory::makeLayout($this->layout, $this->template)->with('content', $response);
            }

            // Create a proper Response instance.
            $response = new Response($response->render(), 200, array('Content-Type' => 'text/html'));
        }

        // If the response is not a instance of Symfony Response, create a proper one.
        else if (! $response instanceof SymfonyResponse) {
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
        // Get the currently called method.
        $method = $this->getMethod();

         // Transform the complete class name on a path like variable.
        $path = str_replace('\\', '/', static::class);

        // Check for a valid controller on Application.
        if (preg_match('#^App/Controllers/(.*)$#i', $path, $matches)) {
            $view = $matches[1] .'/' .ucfirst($method);

            return ViewFactory::make($view, $data, null, $this->template);
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

            return ViewFactory::make($view, $data, $module, $this->template);
        }

        // If we arrived there, the called class is not a Controller; go Exception.
        throw new BadMethodCallException('Invalid Controller namespace: ' .static::class);
    }

    /**
     * Return the current Template name.
     *
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Return a Layout instance.
     *
     * @return \Nova\View\Layout
     *
     * @throws \BadMethodCallException
     */
    public function getLayout()
    {
        if ($this->layout instanceof View) {
            return $this->layout;
        } else if (is_string($this->layout) && ! empty($this->layout)) {
            return ViewFactory::makeLayout($this->layout, $this->template);
        }

        throw new BadMethodCallException('Method not available for the current Layout');
    }

    /**
     * Return the current Layout (class) name.
     *
     * @return string
     *
     * @throws \BadMethodCallException
     */
    public function getLayoutName()
    {
        if ($this->layout instanceof View) {
            return $this->layout->getName();
        } else if (is_string($this->layout)) {
            return $this->layout;
        }

        throw new BadMethodCallException('Method not available for the current Layout');
    }

}
