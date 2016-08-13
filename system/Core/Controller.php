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
use Core\Template;
use Core\View;
use Http\Response;
use Routing\Controller as BaseController;
use Support\Contracts\RenderableInterface as Renderable;


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
    public $language;


    /**
     * On the initial run, create an instance of the config class and the view class.
     */
    public function __construct()
    {
        parent::__construct();

        // Setup the used Template to default, if it is not already defined.
        if(! isset($this->template)) {
            $this->template = Config::get('app.template');
        }

        // Initialise the Language object.
        $this->language = Language::getInstance();
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
            if (($this->layout !== false) && ! $response->isLayout()) {
                $response = Template::make($this->layout, $this->template)->with('content', $response);
            }
        }

        // At this point, we will return any not null response.
        if (! is_null($response)) return $response;

        // If the response which is returned from the Controller's Action is null and we have
        // View instances on View's Legacy support, we will assume that we are on Legacy Mode.

        // Get the (legacy) Headers stored on the View Facade.
        $headers = View::getHeaders();

        // Retrieve the (legacy) View instances stored on the View Facade.
        $items = View::getItems();

        // Setup the default value of the response.
        $response = '';
        
        // Render every View instance and append the result to the response.
        foreach ($items as $item) {
            $response .= $item->render();
        }

        // Create a Response instance from gathered information and return it.
        return new Response($response, 200, $headers);
    }

    /**
     * Return a translated string.
     * @return string
     */
    protected function trans($str, $code = LANGUAGE_CODE)
    {
        return $this->language->get($str, $code);
    }

    /**
     * @param  string $title
     *
     * @return \Core\Controller
     */
    protected function title($title)
    {
        View::share('title', $title);
    }

    /**
     * Return a default View instance.
     *
     * @return \Core\View
     */
    protected function getView(array $data = array())
    {
        list(, $caller) = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);

        $baseView = ucfirst($caller['function']);

        //
        $classPath = str_replace('\\', '/', static::class);

        if (preg_match('#^App/Controllers/(.*)$#i', $classPath, $matches)) {
            $view = str_replace('/', DS, $matches[1]) .DS .$baseView;

            $module = null;
        } else if (preg_match('#^App/Modules/(.+)/Controllers/(.*)$#i', $classPath, $matches)) {
            $view = str_replace('/', DS, $matches[2]) .DS .$baseView;

            $module = $matches[1];
        } else {
            throw new BadMethodCallException('Invalid Controller namespace: ' .static::class);
        }

        return View::make($view, $data, $module);
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
