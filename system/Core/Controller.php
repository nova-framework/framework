<?php
/**
 * Controller - base controller
 *
 * @author David Carr - dave@novaframework.com
 * @version 3.0
 */

namespace Core;

use Core\Config;
use Core\Language;
use Http\Response;
use Routing\Controller as BaseController;
use Support\Contracts\RenderableInterface as Renderable;
use Template\Template as Layout;

use Template;
use View;


/**
 * Core controller, all other controllers extend this base controller.
 */
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
     * Language variable to use the languages class.
     *
     * @var string
     */
    public $language = null;


    /**
     * On the initial run, create an instance of the config class and the view class.
     */
    public function __construct()
    {
        parent::__construct();

        // Setup the used Template to default, if it is not already defined.
        if (($this->layout !== false) && ! isset($this->template)) {
            $this->template = Config::get('app.template');
        }

        // Initialise the Language object.
        if ($this->language !== false) {
            $this->language = Language::getInstance();
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
                return Template::make($this->layout, $this->template)->with('content', $response);
            }
        } else if (is_null($response)) {
            // If the response which is returned from the Controller's Action is null and we have
            // View instances on View's Legacy support, we will assume that we are on Legacy Mode.

            $items = View::getItems();

            $headers = View::getHeaders();

            // Render the View instances to response.
            $response = '';

            foreach ($items as $item) {
                $response .= $item->render();
            }

            // Create a Response instance and return it.
            return new Response($response, 200, $headers);
        }

        return $response;
    }

    /**
     * Return a translated string.
     * @return string
     */
    protected function trans($str, $code = LANGUAGE_CODE)
    {
        if ($this->language instanceof Language) {
            return $this->language->get($str, $code);
        }

        return $str;
    }

    /**
     * Return a default View instance.
     *
     * @return \View\View
     * @throw \BadMethodCallException
     */
    protected function getView(array $data = array())
    {
        $path = str_replace('\\', '/', static::class);

        //
        list(, $caller) = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);

        $method = $caller['function'];

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
    protected function getTemplate()
    {
        return $this->template;
    }

    /**
     * @return mixed
     */
    protected function getLayout()
    {
        return $this->layout;
    }

}
