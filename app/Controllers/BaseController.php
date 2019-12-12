<?php

namespace App\Controllers;

use Nova\Foundation\Auth\Access\AuthorizesRequestsTrait;
use Nova\Foundation\Bus\DispatchesJobsTrait;
use Nova\Foundation\Validation\ValidatesRequestsTrait;
use Nova\Http\Request;
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
    use DispatchesJobsTrait, AuthorizesRequestsTrait, ValidatesRequestsTrait;

    /**
     * The current Request instance.
     *
     * @var \Nova\Http\Request
     */
    protected $request;

    /**
     * The currently called action.
     *
     * @var string
     */
    protected $action;

    /**
     * The current call parameters.
     *
     * @var array
     */
    protected $parameters;

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
    protected $autoRender = false;

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
            $this->theme = Config::get('app.theme', 'Themes/Bootstrap');
        }

        if ($this->theme === false) {
            return;
        }

        // A Theme is configured for this Controller.
        else if (! Str::contains($theme = $this->theme, '/')) {
            $theme = 'Themes/' .$theme;
        }

        View::overridesFrom($theme);

        Config::set('themes.current', $theme);
    }

    /**
     * Execute an action on the controller.
     *
     * @param \Nova\Http\Request  $request
     * @param string  $method
     * @param array   $params
     * @return mixed
     */
    public function callAction(Request $request, $method, array $parameters)
    {
        $this->request = $request;
        $this->action  = $method;

        $this->parameters = $parameters;

        //
        $this->initialize();

        $response = call_user_func_array(array($this, $method), $parameters);

        if (is_null($response) && $this->autoRender()) {
            $response = $this->createView();
        }

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
        if (! $response instanceof RenderableInterface) {
            return $response;
        }

        // The response is a RenderableInterface implementation.
        else if ($this->autoLayout() && ! empty($view = $this->resolveLayoutView())) {
            return View::make($view, $this->viewData)->with('content', $response);
        }

        return $response;
    }

    /**
     * Gets a localized View name for the currently used Layout.
     *
     * @return string
     */
    protected function resolveLayoutView()
    {
        if (empty($layout = $this->getLayout())) {
            return false;
        }

        // We have a valid layout.
        else if (Language::direction() == 'rtl') {
            $path = sprintf('Layouts/RTL/%s', $layout);

            if (View::exists($view = $this->resolveViewFromTheme($path))) {
                return $view;
            }
        }

        $path = sprintf('Layouts/%s', $layout);

        return $this->resolveViewFromTheme($path);
    }

    /**
     * Gets a qualified View name for the implicit or given Layout.
     *
     * @param  string  $view
     * @return string
     */
    protected function resolveViewFromTheme($view)
    {
        if (empty($theme = Config::get('themes.current', $this->getTheme()))) {
            return $view;
        }

        // A theme is specified for auto rendering.
        else if (! Str::contains($theme, '/')) {
            return sprintf('Themes/%s::%s', $theme, $view);
        }

        return sprintf('%s::%s', $theme, $view);
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

        $view = sprintf('%s/%s', $this->resolveViewPath(), $view);

        return View::make($view, array_merge($this->viewData, $data));
    }

    /**
     * Gets a qualified View path.
     *
     * @return string
     * @throws \BadMethodCallException
     */
    protected function resolveViewPath()
    {
        if (isset($this->viewPath)) {
            return $this->viewPath;
        }

        $path = str_replace('\\', '/', static::class);

        if (preg_match('#^(.+)/Controllers/(.*)$#', $path, $matches) === 1) {
            list (, $basePath, $viewPath) = $matches;

            if ($basePath !== 'App') {
                $viewPath = sprintf('%s::%s', $basePath, $viewPath);
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
     * @return BaseController
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
        if (is_null($enable)) {
            return $this->autoRender;
        }

        $this->autoRender = (bool) $enable;

        return $this;
    }

    /**
     * Turns on or off Nova's conventional mode of applying layout files.
     *
     * @param bool|null  $enable
     * @return bool
     */
    public function autoLayout($enable = null)
    {
        if (is_null($enable)) {
            return $this->autoLayout;
        }

        $this->autoLayout = (bool) $enable;

        return $this;
    }

    /**
     * Return the current Request instance.
     *
     * NOTE: this information is available after calling the Action.
     *
     * @return \Nova\Http\Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Return the current called action
     *
     * NOTE: this information is available after calling the Action.
     *
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Return the current call parameters
     *
     * NOTE: this information is available after calling the Action.
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Return the current Theme.
     *
     * @return string
     */
    public function getTheme()
    {
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
