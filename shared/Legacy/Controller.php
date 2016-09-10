<?php
/**
 * Controller - A base controller with legacy API support.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Shared\Legacy;

use Http\Request;
use Http\Response;
use Routing\Controller as BaseController;
use Routing\Route;
use Support\Facades\Language;
use Support\Facades\View;
use Template\Template as Layout;

use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

use BadMethodCallException;


abstract class Controller extends BaseController
{
    /**
     * The requested Method by Router.
     *
     * @var string|null
     */
    private $method = null;

    /**
     * The parameters given by Router.
     *
     * @var array
     */
    private $params = array();

    /**
     * Language variable to use the languages class.
     *
     * @var string
     */
    public $language = null;

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
        parent::__construct();

        // Setup the used Template to default, if it is not already defined.
        if (! isset($this->template)) {
            $this->template = Config::get('app.template');
        }

        // Initialise the Language object.
        if ($this->language !== false) {
            $this->language = Language::instance('legacy_api', LANGUAGE_CODE);
        }

        // Setup the (legacy) Middleware.
        $this->beforeFilter('@callLegacyBefore');

        $this->afterFilter('@callLegacyAfter');
    }

    /**
     * Setup the Controller's parameters and method name.
     *
     * @param \Routing\Route $route
     * @return void
     */
    protected function setupController(Route $route, Request $request)
    {
        $action = $route->getAction();

        if (isset($action['controller'])) {
            list($class, $method) = explode('@', $action['controller']);

            $this->method = $method;
        }

        $this->params = $route->getParams();
    }

    /**
     *  Call the (legacy) Before Middleware.
     *
     * @return mixed
     */
    public function callLegacyBefore(Route $route, Request $request)
    {
        // Setup the Controller instance.
        $this->setupController($route, $request);

        // Execute the Controller's Before Middleware.
        return $this->before();
    }

    /**
     *  Call the (legacy) After Middleware.
     *
     * @return void
     */
    public function callLegacyAfter(Route $route, Request $request, $response)
    {
        // Execute the Controller's After Middleware.
        $this->after($response);
    }

    /**
     * The (legacy) Middleware called before the Action execution.
     *
     * @return mixed|void
     */
    protected function before()
    {
        //
    }

    /**
     * The (legacy) Middleware called after the Action execution.
     *
     * @param mixed $response
     *
     * @return void
     */
    protected function after($response)
    {
        //
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
     * Return a translated string.
     *
     * @return string
     */
    protected function trans($message, $code = LANGUAGE_CODE)
    {
        if ($this->language instanceof Language) {
            return $this->language->get($message, $code);
        }

        return $message;
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

            if (is_string($this->layout) && (! $response instanceof Layout)) {
                $response = Template::make($this->layout, $this->template)->with('content', $response);
            }

            // Create a proper Response instance.
            $response = new Response($response->render(), 200, array('Content-Type' => 'text/html'));
        }

        // If the response which is returned from the Controller's Action is null and we have
        // View instances on View's Legacy support, we will assume that we are on Legacy Mode.
        else if (is_null($response)) {
             $response = $this->createLegacyResponse();
        }

        // If the response is not a instance of Symfony Response, create a proper one.
        if (! $response instanceof SymfonyResponse) {
            $response = new Response($response);
        }

        return $response;
    }

    /**
     * Create a Response instance from the legacy View API and return it.
     *
     * @return \Http\Response
     */
    protected function createLegacyResponse()
    {
        $items = View::getItems();

        $headers = array_merge(array('Content-Type' => 'text/html'), View::getHeaders());

        // Render the View instances to response.
        $response = '';

        foreach ($items as $item) {
            $response .= $item->render();
        }

        // Create a Response instance and return it.
        return new Response($response, 200, $headers);
    }

    /**
     * @return mixed
     */
    protected function getMethod()
    {
        return $this->method;
    }

    /**
     * @return array
     */
    protected function getParams()
    {
        return $this->params;
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
