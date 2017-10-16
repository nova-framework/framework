<?php

namespace App\Controllers;

use Nova\Foundation\Auth\Access\AuthorizesRequestsTrait;
use Nova\Foundation\Validation\ValidatesRequestsTrait;
use Nova\Routing\Controller;
use Nova\Support\Contracts\RenderableInterface;
use Nova\Support\Facades\App;
use Nova\Support\Facades\Config;
use Nova\Support\Facades\Language;
use Nova\Support\Facades\View;
use Nova\Support\Str;

use BadMethodCallException;


class BaseController extends Controller
{
    use AuthorizesRequestsTrait, ValidatesRequestsTrait;

    /**
     * The currently called action.
     *
     * @var string
     */
    protected $action;

    /**
     * The currently used Theme.
     *
     * @var string
     */
    protected $theme;

    /**
     * The currently used Layout.
     *
     * @var string
     */
    protected $layout = 'Default';

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
     * The View path for views of this Controller.
     *
     * @var string
     */
    protected $viewPath;

    /**
     * The View variables.
     *
     * @var array
     */
    protected $viewData = array();


    /**
     * Method executed before any action.
     *
     * @return void
     */
    protected function initialize()
    {
        // Setup the used Theme to default, if it is not already defined.
        if (is_null($this->theme)) {
            $this->theme = Config::get('app.theme', 'Bootstrap');
        }
    }

    /**
     * Execute an action on the controller.
     *
     * @param string  $method
     * @param array   $params
     * @return mixed
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
        if (! $this->autoRender()) {
            return $response;
        }

        // The auto-rendering is active.
        else if (is_null($response)) {
            $response = $this->createView();
        }

        if (! $response instanceof RenderableInterface) {
            return $response;
        }

        // The response is a RenderableInterface implementation.
        else if ($this->autoLayout() && ! empty($this->layout)) {
            $view = $this->getLocalizedLayout();

            return View::make($view, $this->viewData)->with('content', $response);
        }

        return $response;
    }

    /**
     * Gets a localized View name for the implicit Layout.
     *
     * @return string
     */
    protected function getLocalizedLayout()
    {
        if ('rtl' == Language::direction()) {
            $layout = sprintf('RTL/%s', $this->layout);

            if (View::exists($view = $this->getQualifiedLayout($layout))) {
                return $view;
            }
        }

        return $this->getQualifiedLayout();
    }

    /**
     * Gets a qualified View name for the implicit or given Layout.
     *
     * @param  string|null  $layout
     * @return string
     */
    protected function getQualifiedLayout($layout = null)
    {
        $view = sprintf('Layouts/%s', $layout ?: $this->layout);

        if (! empty($theme = $this->getTheme())) {
            return sprintf('%s::%s', $theme, $view);
        }

        return $view;
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

        // Compute the qualified View name.
        $view = sprintf('%s/%s', $this->getViewPath(), $view);

        return View::make($view, array_merge($this->viewData, $data));
    }

    /**
     * Gets a qualified View path.
     *
     * @return string
     * @throws \BadMethodCallException
     */
    protected function getViewPath()
    {
        if (isset($this->viewPath)) {
            return $this->viewPath;
        }

        $basePath = trim(str_replace('\\', '/', App::getNamespace()), '/');

        $classPath = str_replace('\\', '/', static::class);

        if (preg_match('#^(.+)/Controllers/(.*)$#', $classPath, $matches) === 1) {
            $viewPath = $matches[2];

            //
            $namespace = $matches[1];

            if ($namespace !== $basePath) {
                // A Controller within a Plugin namespace.
                $viewPath = $namespace .'::' .$viewPath;
            }

            return $this->viewPath = $viewPath;
        }

        throw new BadMethodCallException('Invalid controller namespace');
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

        $this->viewData = $data + $this->viewData;

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
     * Return the current Theme.
     *
     * @return string
     */
    public function getTheme()
    {
        if (! isset($this->theme)) {
            return $this->theme = Config::get('app.theme', 'AcmeCorp/Bootstrap');
        }

        return $this->theme;
    }

    /**
     * Return the current Layout.
     *
     * @return string
     */
    public function getLayout()
    {
        return $this->layout;
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
