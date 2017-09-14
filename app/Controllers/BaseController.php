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
                $response = $this->getLayout()->with('content', $response);
            }

            // Create and return a proper Response instance.
            $content = $response->render();

            return Response::make($content);
        }

        return parent::after($response);
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

        // Check for a valid controller on App and its Modules.
        if (preg_match('#^App(?:/Modules/(.+))?/Controllers/(.*)$#', $path, $matches) === 1) {
            $module = ! empty($matches[1]) ? $matches[1] : '';

            $view = sprintf('%s/%s', $matches[2], ucfirst($method));

            return View::make($view, $data, $module, $this->theme);
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
     * @return \View\Layout
     */
    public function getLayout()
    {
        return View::createLayout($this->layout, $this->theme);
    }

    /**
     * Return the current Layout name.
     *
     * @return string
     */
    public function getLayoutName()
    {
        return $this->layout;
    }

}
