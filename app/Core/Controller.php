<?php
/**
 * Controller - base controller
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace App\Core;

use Config\Config;
use Http\Response;
use Routing\Controller as BaseController;
use Support\Contracts\RenderableInterface as Renderable;
use Template\Template as Layout;

use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

use Template;
use View;

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
        if (($this->layout !== false) && ! isset($this->template)) {
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

            if (($this->layout !== false) && (! $response instanceof Layout)) {
                $response = Template::make($this->layout, $this->template)->with('content', $response);
            }
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
     * @return \View\View
     * @throw \BadMethodCallException
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
     * @return mixed
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @return mixed
     */
    public function getLayout()
    {
        return $this->layout;
    }

}
