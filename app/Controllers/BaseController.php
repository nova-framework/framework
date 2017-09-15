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
use Nova\Support\Facades\Response;
use Nova\Support\Facades\View;
use Nova\View\Layout;

use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

use BadMethodCallException;


abstract class BaseController extends Controller
{
    use AuthorizeRequestsTrait, ValidateRequestsTrait;

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
     * The View path (and module name) for views of this Controller.
     *
     * @var array
     */
    protected $viewInfo;


    /**
     * Create a new Controller instance.
     */
    public function __construct()
    {
        // Setup the used Theme to default, if it is not already defined.
        if (! isset($this->theme)) {
            $this->theme = Config::get('app.theme', 'Bootstrap');
        }
    }

    /**
     * Method executed after any action.
     *
     * @param mixed  $response
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function after($response)
    {
        if ($response instanceof Renderable) {
            // If the response which is returned from the called Action is a Renderable instance,
            // we will assume we want to render it using the Controller's themed environment.

            if ((! $response instanceof Layout) && ! empty($this->layout)) {
                $content = $this->createLayout()->with('content', $response)->render();
            } else {
                $content = $response->render();
            }

            return Response::make($content);
        }

        return parent::after($response);
    }

    /**
     * Create a Layout instance.
     *
     * @param  string|null  $layout
     * @return \Nova\View\Layout
     */
    protected function createLayout($layout = null)
    {
        return View::createLayout($layout ?: $this->layout, $this->theme);
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
            $view = $this->getMethod();
        }

        list ($module, $viewPath) = $this->getViewInfo();

        // Compute the qualified View name.
        $view = sprintf('%s/%s', $viewPath, ucfirst($view));

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

        // Cumpute the (application) base path - usually it is: 'App'
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
