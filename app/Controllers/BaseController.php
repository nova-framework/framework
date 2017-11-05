<?php
/**
 * Controller - base controller
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace App\Controllers;

use Nova\Foundation\Auth\Access\AuthorizeRequestsTrait;
use Nova\Foundation\Validation\ValidateRequestsTrait;
use Nova\Routing\Controller;
use Nova\Support\Contracts\RenderableInterface as Renderable;
use Nova\Support\Facades\App;
use Nova\Support\Facades\Config;
use Nova\Support\Facades\View;
use Nova\View\Layout;

use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

use BadMethodCallException;


abstract class BaseController extends Controller
{
    use AuthorizeRequestsTrait, ValidateRequestsTrait;

    /**
     * The currently called action.
     *
     * @var string
     */
    private $action;

    /**
     * The View path (and module name) for views of this Controller.
     *
     * @var array
     */
    protected $viewInfo;

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
     * Method executed before any action.
     *
     * @return void
     */
    protected function initialize()
    {
        if (! isset($this->theme)) {
            return $this->theme = Config::get('app.theme', 'Bootstrap');
        }
    }

    /**
     * Execute an action on the controller.
     *
     * @param string  $method
     * @param array   $params
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function callAction($method, array $parameters)
    {
        $this->action = $method;

        //
        $this->initialize();

        $response = call_user_func_array(array($this, $method), $parameters);

        return $this->processResponse($response);
    }

    /**
     * Process a Controller action response.
     *
     * @param  mixed   $response
     * @return mixed
     */
    protected function processResponse($response)
    {
        if (! $response instanceof Renderable) {
            return $response;
        }

        // The auto-rendering in a Layout of the returned View instance.
        else if ((! $response instanceof Layout) && ! empty($this->layout)) {
            return $this->createLayout()->with('content', $response);
        }

        return $response;
    }

    /**
     * Create a Layout instance.
     *
     * @param  string|null  $layout
     * @return \Nova\View\Layout
     */
    protected function createLayout($layout = null)
    {
        if (is_null($layout)) {
            $layout = $this->getLayout();
        }

        if (! is_null($theme = $this->getTheme()) && ($theme !== false)) {
            return View::createLayout($layout, $theme);
        }

        $view = 'Layouts/' .($layout ?: 'Default');

        return View::make($view);
    }

    /**
     * Create a View instance for the implicit (or specified) View name.
     *
     * @param  array  $data
     * @param  string|null  $view
     * @return \Nova\View\View
     */
    protected function createView(array $data = array(), $view = null)
    {
        if (is_null($view)) {
            $view = ucfirst($this->action);
        }

        list ($module, $viewPath) = $this->getViewInfo();

        // Compute the full qualified View name.
        $view = $viewPath .'/' .$view;

        return View::make($view, $data, $module, $this->theme);
    }

    /**
     * Gets the default View's path and module.
     *
     * @return string
     * @throws \BadMethodCallException
     */
    protected function getViewInfo()
    {
        if (isset($this->viewInfo)) {
            return $this->viewInfo;
        }

        // Cumpute the (application) base path - usually, it is: 'App'
        $basePath = str_replace('\\', '/', trim(App::getNamespace(), '\\'));

         // Transform the complete class name on a path like variable.
        $classPath = str_replace('\\', '/', static::class);

        // Check for a valid controller on App and its Modules.
        if (preg_match('#^' .$basePath .'(?:/Modules/(.+))?/Controllers/(.*)$#', $classPath, $matches) === 1) {
            return $this->viewInfo = array_slice($matches, 1);
        }

        throw new BadMethodCallException('Invalid Controller namespace');
    }

    /**
     * Return a default View instance.
     *
     * @return \Nova\View\View
     */
    protected function getView(array $data = array())
    {
        return $this->createView($data);
    }

    /**
     * Return the current Theme name.
     *
     * @return string
     */
    public function getTheme()
    {
        if (! isset($this->theme)) {
            return $this->theme = Config::get('app.theme', 'Bootstrap');
        }

        return $this->theme;
    }

    /**
     * Return the current Layout name.
     *
     * @return string
     */
    public function getLayout()
    {
        return $this->layout;
    }
}
