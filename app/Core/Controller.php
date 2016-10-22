<?php
/**
 * Controller - base controller
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace App\Core;

use Nova\Config\Config;
use Nova\Http\Response;
use Nova\Routing\Controller as BaseController;
use Nova\Support\Contracts\RenderableInterface as Renderable;
use Nova\Support\Facades\Template;
use Nova\Support\Facades\View;
use Nova\Template\Template as Layout;

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
    protected $layout = 'default';


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

            if (is_string($this->layout) && ! empty($this->layout) && (! $response instanceof Layout)) {
                $response = Template::make($this->layout, array(), $this->template)
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

        $method = $caller['function'];

        //
        $path = str_replace('\\', '/', static::class);

        if (preg_match('#^App/Controllers/(.*)$#i', $path, $matches)) {
            $view = $matches[1] .'/' .ucfirst($method);

            return View::make($view, $data);
        } else if (preg_match('#^App/Modules/(.+)/Controllers/(.*)$#i', $path, $matches)) {
            $view = $matches[2] .'/' .ucfirst($method);

            return View::make($view, $data, $matches[1]);
        }

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
     * @param string|null $layout
     * @param array $data
     *
     * @return \Template\Template|\View\View
     * @throws \BadMethodCallException
     */
    public function getLayout($layout = null, array $data = array())
    {
        // Adjust the current used Layout.
        $layout = $layout ?: $this->layout;

        if ($layout instanceof View) {
            return $layout->with($data);
        } else if (is_string($layout)) {
            return Template::make($layout, $data, $this->template);
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
