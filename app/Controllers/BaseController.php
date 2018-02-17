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
     * The Module which hosts the Controller.
     *
     * @var string
     */
    private $module = null;

    /**
     * The current Views path for Controller.
     *
     * @var string
     */
    private $viewPath;

    /**
     * The View variables.
     *
     * @var array
     */
    private $viewData = array();

    /**
     * True when the auto-rendering is active.
     *
     * @var bool
     */
    protected $autoRender = true;

    /**
     * True when the auto-layouting is active.
     *
     * @var bool
     */
    protected $autoLayout = true;

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
     * @throws \BadMethodCallException
     */
    protected function initialize()
    {
        if (! isset($this->theme)) {
            $this->theme = Config::get('app.theme', 'Bootstrap');
        }

        // Transform the complete class name on a path like variable.
        $classPath = str_replace('\\', '/', static::class);

        // Check for a valid controller on App and its Modules.
        if (preg_match('#^(App|Modules)(?:/(.+))?/Controllers/(.*)$#', $classPath, $matches) !== 1) {
            throw new BadMethodCallException('Invalid Controller namespace');
        }

        $this->viewPath = $matches[3];

        if (($matches[1] == 'Modules') && ! empty($module = $matches[2])) {
            $this->module = $module;
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

        // Initialize the Controller instance.
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
        if (! $this->autoRender()) {
            return $response;
        }

        // The auto-rendering is active.
        else if (is_null($response)) {
            $response = $this->createView();
        }

        if (! $response instanceof Renderable) {
            return $response;
        }

        // The auto-rendering in a Layout of the returned View instance.
        else if ($this->autoLayout() && ! empty($this->layout) && (! $response instanceof Layout)) {
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
            return View::createLayout($layout, $theme, $this->viewData);
        }

        $view = 'Layouts/' .$layout;

        return View::make($view, $this->viewData);
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

        $data = array_merge($this->viewData, $data);

        // Compute the fully qualified View name.
        $view = $this->viewPath .'/' .$view;

        return View::make($view, $data, $this->module, $this->theme);
    }

    /**
     * Add a key / value pair to the view data.
     *
     * Bound data will be available to the view as variables.
     *
     * @param  string|array  $one
     * @param  string|array  $two
     * @return View
     */
    public function set($one, $two = null)
    {
        if (is_array($one)) {
            $data = is_array($two) ? array_combine($one, $two) : $one;
        } else {
            $data = array($one => $two);
        }

        $this->viewData = array_merge($data, $this->viewData);

        return $this;
    }

    /**
     * Turns on or off Nova's conventional mode of auto-rendering.
     *
     * @param bool|null  $enable
     * @return bool
     */
    public function autoRender($enable = null)
    {
        if (! is_null($enable)) {
            $this->autoRender = (bool) $enable;

            return $this;
        }

        return $this->autoRender;
    }

    /**
     * Turns on or off Nova's conventional mode of applying layout files.
     *
     * @param bool|null  $enable
     * @return bool
     */
    public function autoLayout($enable = null)
    {
        if (! is_null($enable)) {
            $this->autoLayout = (bool) $enable;

            return $this;
        }

        return $this->autoLayout;
    }

    /**
     * Return the current called action.
     *
     * @return string
     */
    public function getAction()
    {
        return $this->action;
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
     * Return the current Layout name.
     *
     * @return string
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * Return the Controller's Module if any or null.
     *
     * @return string|null
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * Return the current Views path for Controller.
     *
     * @return string
     */
    public function getViewPath()
    {
        return $this->viewPath;
    }

    /**
     * Return the current View data.
     *
     * @return string
     */
    public function getViewData()
    {
        return $this->viewData;
    }
}
